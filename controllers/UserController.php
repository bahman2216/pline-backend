<?php

namespace app\controllers;

use app\models\users\TblUsers;
use app\models\users\TblUsersSearch;
use app\pline\customs\PlineActiveController;
use app\pline\enums\ResponseStatusEnum;
use Yii;

class UserController extends PlineActiveController
{

    public $enableCsrfValidation = false;

    public $modelClass = TblUsers::class;

    public $serializer = [
        'class' => \yii\rest\Serializer::class,
        'collectionEnvelope' => 'items',
    ];

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function ($action) {
            $model = new  TblUsersSearch();
            $query = $model->search(\Yii::$app->request->queryParams);
            $query->query->select(['id', 'username', 'id_number', 'f_name', 'l_name', 'departeman', 'desc', "enable"]);
            $query->sort->defaultOrder = ['id' => SORT_ASC];
            $query->pagination->defaultPageSize = 10;
            return $query;
        };
        unset($actions['delete']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $action_name = Yii::$app->controller->action->id;
            if (Yii::$app->user->id > 1 && in_array($action_name, ['delete', 'insert', 'update', 'create'])) {
                Yii::$app->response->statusCode = 422;
                return [
                    [
                        "field" => "name",
                        "message" => "شما دسترسی لازم را ندارید",
                    ]
                ];
            }
        }
        return true;
    }

    public function actionView($id)
    {
        $model = TblUsers::find()
            ->select(['id', 'username', 'id_number', 'f_name', 'l_name', 'departeman', 'desc', "enable"])
            ->where(['id' => $id])
            ->asArray()
            ->all();
        if (count($model) == 0) {
            return [];
        }
        return $model[0];
    }

    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $data['password'] = md5($data['password']);

        $model = new TblUsers();
        $model->setAttributes($data);
        if (!$model->save()) {
            Yii::$app->response->statusCode = 422;
            $error = [];
            foreach ($model->getErrors() as $key => $value) {
                array_push($error, [
                    "field" => $key,
                    "message" => $value[0],
                ]);
            }
            return $error;
        }
    }

    public function actionUpdate($id)
    {
        $data = Yii::$app->request->post();

        if ($id == 1 && $data['enable'] == false) {
            Yii::$app->response->statusCode = 422;
            return [
                [
                    "field" => "name",
                    "message" => "امکان غیر فعال کردن کاربر ارشد وجود ندارد",
                ]
            ];
        }

        if (isset($data['password'])) {
            if (trim($data['password']) == "") {
                unset($data['password']);
            } else {
                $data['password'] = md5($data['password']);
            }
        }

        $model = TblUsers::findOne($id);
        $model->setAttributes($data);
        if (!$model->save()) {
            Yii::$app->response->statusCode = 422;
            $error = [];
            foreach ($model->getErrors() as $key => $value) {
                array_push($error, [
                    "field" => $key,
                    "message" => $value[0],
                ]);
            }
            return $error;
        }
    }

    public function actionDelete($id)
    {
        if ($id == 1) {
            Yii::$app->response->statusCode = 422;
            return [
                [
                    "field" => "name",
                    "message" => "امکان حذف کاربر ارشد وجود ندارد",
                ]
            ];
        }

        if ($model = TblUsers::findOne($id)) {
            $model->delete();
        } else {
            Yii::$app->response->statusCode = 422;
            return [
                [
                    "field" => "name",
                    "message" => "کاربری یافت نشد",
                ]
            ];
        }
    }

    public function actionLogin()
    {
        $model = TblUsers::findOne([
            'username' => Yii::$app->request->post("username"),
            'password' => md5(Yii::$app->request->post("password")),
        ]);

        if ($model == null) {
            return [
                "status" => ResponseStatusEnum::$Error,
                "auth" => false,
                "username" => null,
                "user_id" => null,
                'token' => null,
                'messages' => [
                    'نام کاربری یا کلمه عبور صحیح نمی باشد'
                ]
            ];
        }

        if ($model->enable == false) {
            return [
                "status" => ResponseStatusEnum::$Error,
                "auth" => false,
                "username" => null,
                "user_id" => null,
                'token' => null,
                'messages' => [
                    'کاربری شما غیر فعال شده است'
                ]
            ];
        }

        $now = new \DateTimeImmutable();
        $exp = $now->modify('+720 minute');
        $token = Yii::$app->jwt->getBuilder()
            ->withClaim('uid', $model->id)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($exp)
            ->issuedBy('Pline VoIP Server')
            ->getToken(
                Yii::$app->jwt->getConfiguration()->signer(),
                Yii::$app->jwt->getConfiguration()->signingKey()
            );


        $model->accessToken = $token->toString();
        $model->authKey = "*";
        $model->save();

        if ($model->hasErrors()) {
            return $model->errors;
        }

        if (Yii::$app->user->loginByAccessToken($token->toString())) {
            return [
                "status" => ResponseStatusEnum::$Success,
                "auth" => true,
                "username" => $model->f_name . " " . $model->l_name,
                "user_id" => Yii::$app->user->getId(),
                'token' => $token->toString(),

            ];
        }
        return [
            "status" => ResponseStatusEnum::$Error,
            "auth" => false,
            "username" => null,
            "user_id" => null,
            'token' => null,
            'messages' => [
                'نام کاربری یا کلمه عبور صحیح نمی باشد'
            ]
        ];
    }

    public function actionChangePassword()
    {
        $old = Yii::$app->request->post("oldPass");
        $new = Yii::$app->request->post("newPass");
        $rep = Yii::$app->request->post("repNewPass");
        if ($rep != $new) {
            Yii::$app->response->statusCode = 422;
            return [
                [
                    "field" => "newPass",
                    "message" => "کلمه عبور جدید با تکرار آن مطابقت ندارد"
                ]
            ];
        }

        $model = TblUsers::findOne(['id' => Yii::$app->user->id]);
        if ($model && md5($old) == $model->password) {
            $model->password = md5($new);
            if ($model->save()) {
                return [];
            }
        }
        Yii::$app->response->statusCode = 422;
        return [
            [
                "field" => "newPass",
                "message" => "خطا در تغییر کلمه عبور. لطفا دوباره تلاش کنید"
            ]
        ];
    }
}