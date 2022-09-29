<?php

namespace app\controllers;

use app\models\pagers\TblPagers;
use app\models\zones\TblZones;
use app\models\zones\TblZonesSearch;
use app\pline\customs\PlineActiveController;
use Yii;

class ZoneController extends PlineActiveController
{

    public $enableCsrfValidation = false;

    public $modelClass = TblZones::class;

    public $serializer = [
        'class' => \yii\rest\Serializer::class,
        'collectionEnvelope' => 'items',
    ];

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function ($action) {
            $model = new  TblZonesSearch();
            $query = $model->search(\Yii::$app->request->queryParams);
            $query->sort->defaultOrder = ['id' => SORT_ASC];
            $query->pagination->defaultPageSize = 10;
            return $query;
        };
        unset($actions['delete']);
        return $actions;
    }

    public function actionDelete($id)
    {
        $usage = TblPagers::find()
            ->where(['zone_id' => $id])
            ->count();

        if ($usage > 0) {

            Yii::$app->response->statusCode = 422;
            return [
                [
                    "field" => "name",
                    "message" => "امکان حذف این ناحیه به دلیل تخصیص به پیجرها وجود ندارد",
                ]
            ];
        }


        $model = TblZones::findOne(['id' => $id]);
        $model->delete();
        Yii::$app->response->statusCode = 200;
        return;
    }



    public function actionAll()
    {
        return TblZones::find()
            ->select(['id', 'name'])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()->all();
    }
}
