<?php

namespace app\controllers;

use app\models\azans\TblAzans;
use app\models\azans\TblAzansSearch;
use app\pline\customs\PlineActiveController;
use app\pline\tools\PersianDate;
use app\pline\tools\Tools;
use Yii;
use yii\web\UploadedFile;

class PrayerController extends PlineActiveController
{

    public $enableCsrfValidation = false;

    public $modelClass = TblAzans::class;

    public $serializer = [
        'class' => \yii\rest\Serializer::class,
        'collectionEnvelope' => 'items',
    ];

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function ($action) {
            $model = new  TblAzansSearch();
            $query = $model->search(\Yii::$app->request->queryParams);
            $query->sort->defaultOrder = ['id' => SORT_ASC];
            $query->pagination->defaultPageSize = 10;
            return $query;
        };
        return $actions;
    }

    public function afterAction($action, $result)
    {
        $action_name = Yii::$app->controller->action->id;
        if (in_array($action_name, ['create', 'delete', 'update'])) {
            Tools::genrateSchedule();
        }
        return parent::afterAction($action, $result);
    }

    public function actionCurDate()
    {
        return [
            'date' => PersianDate::GetCurDate(),
            'time' => date('H:i')
        ];
    }

    public function converDate($date)
    {
        $date = explode("/", $date);
        if (count($date) != 3) {
            return false;
        }
        $r = str_pad($date[0], 4, '0', STR_PAD_LEFT) . "/";
        $r .= str_pad($date[1], 2, '0', STR_PAD_LEFT) . "/";
        $r .= str_pad($date[2], 2, '0', STR_PAD_LEFT);
        return $r;
    }

    public function converTime($time)
    {
        $_time = explode(":", $time);
        if (count($_time) != 3) {
            return false;
        }
        // $r = str_pad($time[0], 2, '0', STR_PAD_LEFT) . ":";
        // $r .= str_pad($time[1], 2, '0', STR_PAD_LEFT) . ":";
        // $r .= str_pad($time[2], 2, '0', STR_PAD_LEFT);
        $r = date("H:i:s", strtotime($time));
        return $r;
    }

    public function actionUpload()
    {

        $base = Yii::getAlias("@webroot");
        if (!is_dir("{$base}/tmp")) {
            mkdir("{$base}/tmp");
        }

        $file = UploadedFile::getInstanceByName('file');
        $uid = date("Ymdhis") . uniqid();
        $message = [
            "field" => "file",
            "message" => "خطا در بار گذاری فایل csv"
        ];

        if ($file->saveAs("tmp/{$uid}.{$file->getExtension()}", true)) {
            $exten = $file->getExtension();
            if (strtolower($exten) == "csv") {
                $error = [];
                $csvfile = fopen("{$base}/tmp/{$uid}.{$file->getExtension()}", 'r');
                $index = 0;
                $csv_data = [];
                while (($line = fgetcsv($csvfile)) !== FALSE) {
                    $index++;
                    if ($index == 1) continue;
                    if (count($line) == 4) {
                        $r = $this->converDate($line[0]);
                        if ($r !== false && PersianDate::isValidDate($r)) {
                            $row = [];
                            $row['date'] = $r;

                            $r = $this->converTime($line[1]);
                            if ($r !== false && PersianDate::isValidTime($r, true)) {
                                $row['time1'] = $r;
                                $row['enable1'] = true;
                            } else {
                                $row['time1'] = "00:00:00";
                                $row['enable1'] = false;
                            }


                            $r = $this->converTime($line[2]);
                            if ($r !== false && PersianDate::isValidTime($r, true)) {
                                $row['time2'] = $r;
                                $row['enable2'] = true;
                            } else {
                                $row['time2'] = "00:00:00";
                                $row['enable2'] = false;
                            }

                            $r = $this->converTime($line[3]);
                            if ($r !== false && PersianDate::isValidTime($r, true)) {
                                $row['time3'] = $r;
                                $row['enable3'] = true;
                            } else {
                                $row['time3'] = "00:00:00";
                                $row['enable3'] = false;
                            }

                            array_push($csv_data, $row);
                        } else {
                            array_push($error, [
                                "field" => "file",
                                "خطا خواندن تاریخ ردیف {$index}"
                            ]);
                        }
                    }
                }
                fclose($csvfile);
                if (count($error) == 0) {
                    return ([
                        'result' => $csv_data,
                    ]);
                } else {
                    $message = $error;
                }
            } else {
                $message = [
                    [
                        "field" => "file",
                        "message" => "فایل بار گذاری شده پشتیبانی نمی شود. فرمت باید csv باشد"
                    ]
                ];
            }
            unlink("tmp/{$uid}.{$file->getExtension()}");
        }
        Yii::$app->response->statusCode = 422;
        return $message;
    }

    public function actionSaveCsv()
    {
        $delete = Yii::$app->request->post('old_delete');
        if ($delete) {
            TblAzans::deleteAll();
        }
        $data = Yii::$app->request->post('data');
        $error = [];
        foreach ($data as $value) {
            $model = new TblAzans();
            $model->date = $value['date'];

            $model->time1 = $value['time1'];
            $model->enable1 = $value['enable1'];

            $model->time2 = $value['time2'];
            $model->enable2 = $value['enable2'];

            $model->time3 = $value['time3'];
            $model->enable3 = $value['enable3'];
            $model->zones1 = ["0"];
            $model->zones2 = ["0"];
            $model->zones3 = ["0"];
            $model->sound1 = $model->sound2 = $model->sound3 = 0;
            $model->befor_sound1 = $model->befor_sound2 = $model->befor_sound3 = -1;
            $model->after_sound1 = $model->after_sound2 = $model->after_sound3 = 0;
            $model->volume1 = $model->volume2 = $model->volume3 = 0;

            $model->desc = ".::ایجاد شده از فایل csv::.";
            if (!$model->save()) {
                foreach ($model->getFirstErrors() as $value) {
                    array_push($error, [
                        "field" => "file",
                        "message" => $value,
                    ]);
                }
            }
        }
        if (count($error) > 0) Yii::$app->response->statusCode = 422;
        return $error;
    }
}
