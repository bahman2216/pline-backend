<?php

namespace app\controllers;

use app\models\pagers\TblPagers;
use app\models\sounds\TblSounds;
use app\models\sounds\TblSoundsSearch;
use app\pline\customs\PlineActiveController;
use app\pline\tools\Tools;
use Yii;
use yii\web\UploadedFile;

class SoundController extends PlineActiveController
{

    public $enableCsrfValidation = false;

    public $modelClass = TblSounds::class;

    public $serializer = [
        'class' => \yii\rest\Serializer::class,
        'collectionEnvelope' => 'items',
    ];

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function ($action) {
            $model = new  TblSoundsSearch();
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
        if ($id == -1) {
            Yii::$app->response->statusCode = 422;
            return [
                [
                    "field" => "name",
                    "message" => "امکان حذف ناحیه پیش فرض وجود ندارد."
                ]
            ];
        }

        $model = TblSounds::findOne(['id' => $id]);
        if ($model->delete())
            unlink("uploads/{$model->file}");

        Yii::$app->response->statusCode = 200;
        return;
    }

    public function actionAll()
    {
        return TblSounds::find()
            ->select(['id', 'name'])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()->all();
    }

    public function actionUpload()
    {
        // ini_set("upload_max_filesize", "1024M");
        // ini_set("post_max_size", "1024M");

        $base = Yii::getAlias("@webroot");
        if (!is_dir("{$base}/uploads")) {
            mkdir("{$base}/uploads");
        }

        if (!is_dir("{$base}/tmp")) {
            mkdir("{$base}/tmp");
        }

        $file = UploadedFile::getInstanceByName('file');
        $uid = date("Ymdhis") . uniqid();
        $message = "خطا در بار گذاری فایل صوتی";

        if ($file->saveAs("tmp/{$uid}.{$file->getExtension()}", true)) {
            //$file_name = $file->getBaseName() . "." . $file->getExtension();
            $exten = $file->getExtension();
            if (strtolower($exten) == "wav" || strtolower($exten) == "mp3" || strtolower($exten) == "ogg") {
                if (($r = Tools::convertToWav("{$base}/tmp/{$uid}.{$file->getExtension()}", "{$base}/uploads/{$uid}.wav", true)) === true) {

                    return ([
                        'file_name' => "{$uid}.wav",
                    ]);
                } else {
                    $message = "در تبدیل فایل خطای رخ داده لطفا مجددا تلاش کنید" . "\n" . $r;
                }
            } else {
                $message = "فرمت فایل پشتیبانی نمی شود";
            }
            unlink("tmp/{$uid}.{$file->getExtension()}");
        }
        Yii::$app->response->statusCode = 422;
        return [
            [
                "field" => "file",
                "message" => $message
            ]
        ];
    }

    public function actionMaxUploadSize()
    {
        // ini_set("upload_max_filesize", "1024M");
        // ini_set("post_max_size", "1024M");

        $max = ini_get('upload_max_filesize');
        $max_byte = Tools::convertBytes($max);
        return [
            'max' => $max,
            'max_byte' => $max_byte
        ];
    }

    public function actionTest()
    {
        $pager = Yii::$app->request->post('pager');
        $sound = Yii::$app->request->post('sound');
        $volume = Yii::$app->request->post('volume');

        $pager = TblPagers::findOne(['id' => $pager]);
        $sound = TblSounds::findOne(['id' => $sound]);
        if ($pager && $sound) {
            $path = Yii::getAlias("@webroot") . "/uploads/" . $sound->file;
            $type_pager = $pager->type_pager == 0 ? "CONSOLE" : "SIP";

            return [
                "result" => true,
                'message' => Tools::callFileOnAgent($path, strtoupper("{$type_pager}/{$pager->username}"), $volume),
            ];
        }
        Yii::$app->response->statusCode = 422;
        return [
            [
                "field" => "pager",
                "message" => "پیجر یا فایل صوتی انتخاب شده جهت پخش درست نمی باشد. لطفا از صحت اطلاعات انتخابی خود مطمئن شوید"
            ]
        ];
    }

    public function actionHangup()
    {
        $pager = Yii::$app->request->post('pager');
        $r = Tools::hangup($pager);
        if ($r)
            return [
                'message' => "صدا قطع شد",
            ];
        Yii::$app->response->statusCode = 422;
        return [
            [
                'message' => "خطا در قطع صدا",
            ]
        ];
    }

    public function actionGetSounds($id)
    {

        $yiiPath = Yii::getAlias("@app");
        $sound = [];
        switch ($id) {
            case 0: {
                    $sound = array_diff(scandir("{$yiiPath}/web/before-azans/"), array('..', '.'));
                }
                break;
            case 1: {
                    $sound = array_diff(scandir("{$yiiPath}/web/azans/"), array('..', '.'));
                }
                break;
            case 2: {
                    $sound = array_diff(scandir("{$yiiPath}/web/after-azans/"), array('..', '.'));
                }
                break;
        }
        $sound = array_values($sound);
        return $sound;
    }
}