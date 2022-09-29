<?php

namespace app\controllers;

use app\models\pagers\VwPagers;
use app\models\sounds\TblSounds;
use app\models\zones\TblZones;
use app\pline\customs\PlineActiveController;
use app\pline\tools\PersianDate;
use app\pline\tools\Tools;
use Yii;
use yii\base\BaseObject;

class ManualPlayController extends PlineActiveController
{

    public $enableCsrfValidation = false;

    public $modelClass = BaseObject::class;

    public function actionPlay()
    {
        $sounds = Yii::$app->request->post("sounds");
        $zones = Yii::$app->request->post("zones");
        $volume = Yii::$app->request->post("volume");

        $pagers = VwPagers::find()
            ->where(['zone_id' => $zones])
            ->andWhere(['enable' => 1])
            ->all();
        $sounds = TblSounds::find()
            ->where(['id' => $sounds])
            ->all();
        if ($pagers && $sounds) {
            $str_agent = [];
            foreach ($pagers as $value) {
                $type_pager = $value->type_pager == 0 ? "CONSOLE" : "SIP";
                array_push($str_agent, "{$type_pager}/{$value->username}");
            }
            $str_sounds = [];
            $path =  Yii::getAlias("@webroot");
            foreach ($sounds as $value) {
                array_push($str_sounds, "{$path}/uploads/{$value->file}");
            }

            return [
                "result" => true,
                "message" => Tools::callFilesOnAgents($str_sounds, $str_agent, $volume),
                [$str_agent, $str_sounds]
            ];
        }
        Yii::$app->response->statusCode = 422;
        return [
            [
                "field" => "pager",
                "message" => "ناحیه یا فایل صوتی انتخاب شده جهت پخش درست نمی باشد. لطفا از صحت اطلاعات انتخابی خود مطمئن شوید"
            ]
        ];
    }

    public function actionAllZonesAndSounds()
    {
        $zones = TblZones::find()
            ->select(['id', 'name'])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()->all();
        $sounds = TblSounds::find()
            ->select(['id', 'name'])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()->all();
        return [
            'sounds' => $sounds,
            'zones' => $zones,
        ];
    }

    public function actionHangupAll()
    {
        return ['r' => Tools::hangupAll()];
    }

    public function actionGetCurDateTime()
    {
        return [
            'time' => date("H:i:s"),
            'date' => PersianDate::getCurDate(),
        ];
    }
}