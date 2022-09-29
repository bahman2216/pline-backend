<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\azans\TblAzans;
use app\models\pagers\TblPagers;
use app\models\schedules\TblSchedules;
use app\models\sounds\TblSounds;
use app\models\zones\TblZones;
use app\pline\tools\Tools;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class ScheduleController extends Controller
{

    public function beforeAction($action)
    {
        date_default_timezone_set("Asia/Tehran");
        $action = Yii::$app->controller->action->id;
        $controller = Yii::$app->controller->id;
        syslog(LOG_INFO, "{$controller}->{$action}");
        return parent::beforeAction($action);
    }

    public function actionReloadAzan()
    {
        Tools::genrateSchedule();
        syslog(LOG_INFO, "Reload Schedule");
        return ExitCode::OK;
    }

    public function actionRun($id)
    {
        $model = TblSchedules::findOne($id);
        if ($model == null || $model->enable == false)
            return ExitCode::OK;
        $zones = json_decode($model->zones, true);
        $zones_model = [];
        if (in_array("0", $zones)) {
            $zones_model = TblPagers::findAll(['enable' => true]);
        } else {
            $zones_model = TblPagers::findAll(['enable' => true, 'zone_id' => $zones]);
        }

        $pagers = [];
        foreach ($zones_model as $value) {
            $pager = $value->type_pager == Tools::$TypeALSA ? "ALSA" : "SIP";
            $pager = "{$pager}/{$value->username}";
            array_push($pagers, $pager);
        }

        $sounds = json_decode($model->sounds, true);
        $sounds_model = TblSounds::findAll(['id' => $sounds]);
        $sounds = [];
        $path = Yii::getAlias("@app");
        foreach ($sounds_model as $value) {
            array_push($sounds, "{$path}/web/uploads/{$value->file}");
        }

        $data = json_decode($model->schedules, true);
        Tools::callFilesOnAgents($sounds, $pagers, $data['volume']);

        $date = date("H:i:s");
        syslog(LOG_INFO, "In {$date} Schedules {$model->name} Runing");
        return ExitCode::OK;
    }

    /*************************************************************************************** */

    public function actionAzan($id, $nobat, $befor_sound)
    {
        srand(time());
        $model = TblAzans::findOne($id);
        if ($model == null || $model->getAttribute("enable{$nobat}") == false) {
            return ExitCode::OK;
        }

        $zones = json_decode($model->getAttribute("zones{$nobat}"), true);
        if (gettype($zones) == "string")
            $zones = json_decode($zones, true);
        $zones_model = [];
        if (in_array("0", $zones)) {
            $zones_model = TblPagers::findAll(['enable' => true]);
        } else {
            $zones_model = TblPagers::findAll(['enable' => true, 'zone_id' => $zones]);
        }

        $pagers = [];
        foreach ($zones_model as $value) {
            $pager = $value->type_pager == Tools::$TypeALSA ? "ALSA" : "SIP";
            $pager = "{$pager}/{$value->username}";
            array_push($pagers, $pager);
        }

        $path = Yii::getAlias("@app");
        $sounds = [];
        if ($model->getAttribute("befor_sound{$nobat}") >= 0) {
            array_push($sounds, $befor_sound);
        }

        if ($model->getAttribute("sound{$nobat}") == 0) {
            $sound = array_diff(scandir("{$path}/web/azans/"), array('..', '.'));
            $sound = array_values($sound);
            $rnd = rand(0, count($sound) - 1);
            array_push($sounds, "{$path}/web/azans/{$sound[$rnd]}");
        } else {
            if ($m = TblSounds::findOne($model->getAttribute("azans{$nobat}"))) {
                array_push($sounds, "{$path}/web/azans/{$m->file}");
            }
        }

        if ($model->getAttribute("after_sound{$nobat}") == 0) {
            $sound = array_diff(scandir("{$path}/web/after-azans/"), array('..', '.'));
            $sound = array_values($sound);
            $rnd = rand(0, count($sound) - 1);
            array_push($sounds, "{$path}/web/after-azans/{$sound[$rnd]}");
        } else {
            if ($m = TblSounds::findOne($model->getAttribute("after_sound{$nobat}"))) {
                array_push($sounds, "{$path}/web/after-azans/{$m->file}");
            }
        }
        Tools::callFilesOnAgents($sounds, $pagers, $model->getAttribute("volume{$nobat}"));
        $date = date("H:i:s");
        syslog(LOG_INFO, "In {$date} Azan {$model->date} Level {$nobat} Runing");
        return ExitCode::OK;
    }
}
