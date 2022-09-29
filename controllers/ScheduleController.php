<?php


namespace app\controllers;

use app\models\schedules\TblSchedules;
use app\models\schedules\TblSchedulesSearch;
use app\pline\customs\PlineActiveController;
use app\pline\tools\Tools;
use Yii;

class ScheduleController extends PlineActiveController
{
    public $enableCsrfValidation = false;

    public $modelClass = TblSchedules::class;

    public $serializer = [
        'class' => \yii\rest\Serializer::class,
        'collectionEnvelope' => 'items',
    ];

    public function afterAction($action, $result)
    {
        $action_name = Yii::$app->controller->action->id;
        if (in_array($action_name, ['create', 'delete', 'update'])) {
            Tools::genrateSchedule();
        }
        return parent::afterAction($action, $result);
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function ($action) {
            $model = new  TblSchedulesSearch();
            $query = $model->search(\Yii::$app->request->queryParams);
            $query->sort->defaultOrder = ['id' => SORT_ASC];
            $query->pagination->defaultPageSize = 10;
            return $query;
        };
        return $actions;
    }
}
