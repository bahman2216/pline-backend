<?php

namespace app\controllers;

use app\models\pagers\TblPagers;
use app\models\pagers\VwPagersSearch;
use app\pline\customs\PlineActiveController;
use app\pline\tools\PersianDate;
use app\pline\tools\Tools;
use Yii;

class PagerController extends PlineActiveController
{

    public $enableCsrfValidation = false;

    public $modelClass = TblPagers::class;

    public $serializer = [
        'class' => \yii\rest\Serializer::class,
        'collectionEnvelope' => 'items',
    ];

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function ($action) {
            $model = new  VwPagersSearch();
            $query = $model->search(\Yii::$app->request->queryParams);
            $query->sort->defaultOrder = ['id' => SORT_ASC];
            $query->pagination->defaultPageSize = 10;
            return $query;
        };
        unset($actions['delete']);
        return $actions;
    }

    public function afterAction($action, $result)
    {
        $action_name = Yii::$app->controller->action->id;
        if (in_array($action_name, ['create', 'delete', 'update'])) {
            Tools::genrateAgents();
        }
        return parent::afterAction($action, $result);
    }

    public function actionDelete($id)
    {
        if ($id == 1) {

            Yii::$app->response->statusCode = 422;
            return [
                [
                    "field" => "name",
                    "message" => "امکان حذف ناحیه پیش فرض وجود ندارد."
                ]
            ];
        }
        $model = TblPagers::findOne(['id' => $id]);
        $model->delete();
        Yii::$app->response->statusCode = 200;
        return;
    }

    public function actionAll()
    {
        return TblPagers::find()
            ->select(['id', 'username'])
            ->where(['enable' => 1])
            ->orderBy(['username' => SORT_ASC])
            ->asArray()->all();
    }

    public function actionPagerStatus()
    {
        $models = TblPagers::findAll(['enable' => true]);
        $data = [];
        foreach ($models as $value) {
            $status = false;
            $ip = "";
            if ($value->type_pager == Tools::$TypeALSA) {
                $status = true;
                $ip = "";
            } else {
                $ip = Tools::runCmd('/usr/sbin/asterisk -x "sip show peer ' . $value->username . '" | /usr/bin/grep "Addr->IP" | /usr/bin/grep -o -E "(\(null\))|([0-9\.]{1,}\:[0-9]{1,})"');
                $ip = trim($ip);
                if ($ip == "") {
                    $status = false;
                    $ip = "این پیجر تعریف نشده است";
                } else {
                    $status = $ip != "(null)";
                }
            }

            array_push($data, [
                'type' => $value->type_pager,
                'username' => $value->username,
                'status' => $status,
                'ip' => $ip,
            ]);
        }
        return $data;
    }
}
