<?php

namespace app\controllers;

use Yii;
//use yii\filters\AccessControl;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use app\models\Categories;
use app\models\Products;

class ProductsController extends Controller
{

   /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        $model = Products::model('products');
        $provider = $model->search(Yii::$app->request->get());
        return $this->render('index', ['dataProvider' => $provider, 'filter' => $model,]);
    }

}
