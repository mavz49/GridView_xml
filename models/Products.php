<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Categories;

class Products extends Model
{
    public $fileName;
    public $id;
    public $categoryId;
    public $categoryName;
    public $price;
    public $hidden;
    public $hiddenName;
    
    private $_dataProduct = null;
    private $_categoryList = null;
    private $_filtered = false;
    
    
    const HIDDEN_NONE                  = 0;//Видимый
    const HIDDEN_YES                   = 1;//Скрытый
    
    public function getHiddenList()
    {
          return self::getHiddenListStatic();

    }
    public function getHiddenName($hidden)
    {
        if(array_key_exists($hidden, $this->hiddenList))
            return $this->hiddenList[$hidden];
        return false;
    }  
    public static function getHiddenListStatic()
    {
          return array(self::HIDDEN_NONE                => 'Видимый',
                       self::HIDDEN_YES                 => 'Скрытый');
    }    
    
    public function init(){
        if(!empty($this->fileName)){
            $this->getDataProduct();
        }
    }   
    public function getCategoryName($categoryId)
    {
        if(array_key_exists($categoryId, $this->categoryList))
            return $this->categoryList[$categoryId];
        return false; 
    } 
    public function getDataProduct()
    {
        if ($this->_dataProduct === null) {
            $this->_dataProduct = $this->parseFile();
        }
        return $this->_dataProduct;
    }

    public function getCategoryList()
    {
        if ($this->_categoryList === null) {
            $this->_categoryList = $this->getCategories();
        }
        return $this->_categoryList;
    }
   
    private function getCategories()
    {   
        $model = Categories::model('categories');
        if(empty($model->dataCategory))return false;
        $arr = [];
        foreach($model->dataCategory as $item){
            $arr[$item['id']] = $item['name'];
        }
        return $arr;
    }
    
    private function parseFile() {
       $textXml = file_get_contents($this->partName  . $this->fileName . '.xml');
       $data = new \SimpleXMLElement($textXml);
       $arr = [];
       if(empty($data[0])) return false;
       $key=0;
       foreach($data[0] as $item){
           $arr[$key]['id'] = (int)$item->id;
           $arr[$key]['categoryId'] = (int)$item->categoryId;
           $arr[$key]['categoryName'] = (string)$this->getCategoryName((int)$item->categoryId);
           $arr[$key]['price'] = (string)$item->price;
           $arr[$key]['hidden'] = (int)$item->hidden;
           $arr[$key]['hiddenName'] = (string)$this->getHiddenName((int)$item->hidden);
           $key++;
       }
       return $arr;
    }
    
    public function getPartName() {
        return Yii::getAlias('@app').'/data/products/';
    }  
    
    public function search($params)
    {
        /**
         * $params is the array of GET parameters passed in the actionExample().
         * These are being loaded and validated.
         * If validation is successful _filtered property is set to true to prepare
         * data source. If not - data source is displayed without any filtering.
         */
        if ($this->load($params) && $this->validate()) {
            $this->_filtered = true;
        }

        return new \yii\data\ArrayDataProvider([
            // ArrayDataProvider here takes the actual data source
            'allModels' => $this->getData(),
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                // we want our columns to be sortable:
                'attributes' => ['id', 'categoryName', 'price', 'hiddenName'],
            ],
        ]);
    } 
    
    protected function getData()
    {
        $data = $this->_dataProduct;
        if ($this->_filtered) {
            $data = array_filter($data, function ($value) {
                $conditions = [true];
                if (!empty($this->id)) {
                    $conditions[] = strpos($value['id'], $this->id) !== false;
                }
                if (!empty($this->categoryName)) {
                    $conditions[] = strpos($value['categoryId'], $this->categoryName) !== false;
                }
                if (!empty($this->price)) {
                    $conditions[] = strpos($value['price'], $this->price) !== false;
                }
                if (!empty($this->hiddenName)) {
                    $conditions[] = strpos($value['hidden'], $this->hiddenName) !== false;
                }
                return array_product($conditions);
            });
        }

        return $data;
    }
    
    
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id', 'categoryId', 'categoryName', 'price', 'hidden', 'hiddenName'], 'string'],
        ];
    }
    public static function model($fileName){
        $model = new self;
        $model->fileName = $fileName;
        $model->init();
        return $model;
    }

}
