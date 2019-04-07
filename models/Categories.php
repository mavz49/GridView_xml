<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Categories extends Model
{
    public $fileName;
    private $_dataCategory = null;

    
    public function init(){
        if(!empty($this->fileName)){
            $this->getDataCategory();
        }
    }   

    public function getDataCategory()
    {
        if ($this->_dataCategory === null) {
            $this->_dataCategory = $this->parseFile();
        }
        return $this->_dataCategory;
    }
    
    
    private function parseFile() {
       $textXml = file_get_contents($this->partName  . $this->fileName . '.xml');
       $data = new \SimpleXMLElement($textXml);
       $arr = [];
       if(empty($data[0])) return false;
       $key=0;
       foreach($data[0] as $item){
           $arr[$key]['id'] = (int)$item->id;
           $arr[$key]['name'] = (string)$item->name;
           $key++;
       }
       return $arr;
    }
    
    public function getPartName() {
        return Yii::getAlias('@app').'/data/categories/';
    }  
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [

        ];
    }
    public static function model($fileName){
        $model = new self;
        $model->fileName = $fileName;
        $model->init();
        return $model;
    }

}
