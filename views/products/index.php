<?php
use yii\grid\GridView;
use app\models\Products;
$this->title = 'Продукты';

?>
<div class="site-index">

    <div class="body-content">
        <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $filter,
                'columns' => [
                    'id',
                    [
                      'attribute'=>'categoryName',
                      'label'=>'Категория',
                      'format'=>'text',
                      'filter'=>$filter->categoryList,    
                    ],
                    [
                      'attribute'=>'price',
                      'label'=>'Цена',
                      'format'=>'text',
                    ],
                    [
                      'attribute'=>'hiddenName',
                      'label'=>'Видимость',
                      'format'=>'text',
                      'filter'=>Products::getHiddenListStatic(),  
                    ],
                ],
                
                
                
            ]);
        ?>
    </div>
</div>
