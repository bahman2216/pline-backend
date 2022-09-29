<?php

namespace app\models\users;

use Lcobucci\JWT\Token;
use Symfony\Polyfill\Intl\Idn\Resources\unidata\Regex;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "tblUsers".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 * @property string|null $id_number
 * @property string $f_name
 * @property string $l_name
 * @property string|null $departeman
 * @property bool $enable
 * @property string|null $desc
 */
class TblUsers extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblUsers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'f_name', 'l_name'], 'required'],
            [['enable'], 'boolean'],
            [['username'], 'string', 'max' => 20],
            [['password'], 'string', 'max' => 32],
            [['authKey', 'accessToken', 'id_number', 'f_name', 'l_name', 'departeman'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 1024],
            [['username'], 'unique'],
            [['username'], 'CheckUsernameValue'],

        ];
    }

    public function CheckUsernameValue($attribute)
    {
        $string = $this->username;
        $num = 0;
        while (isset($string[$num])) {
            if (ord($string[$num]) & 0x80) {
                $this->addError($attribute, "نام کاربری باید شامل کارکترهای لاتین باشد");
                return false;
            }
            $num++;
        }
        if (preg_match("/^[a-zA-Z0-9]+$/", $string) == 1) {
            return true;
        }
        $this->addError($attribute, "نام کاربری باید شامل کارکترهای " . "[a-z][A-Z][0-9]" . " باشد.");
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'نام کاربری',
            'password' => 'کلمه عبور',
            'authKey' => 'Auth Key',
            'accessToken' => 'Access Token',
            'id_number' => 'کد شناسایی',
            'f_name' => 'نام',
            'l_name' => 'نام خانوادگی',
            'departeman' => 'واحد',
            'enable' => 'فعال/غیرفعال',
            'desc' => 'شرح',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TblUsersQuery the active query used by this AR class.
     */
    public static function find(): TblUsersQuery
    {
        return new TblUsersQuery(get_called_class());
    }

    public static function findIdentity($id): ?TblUsers
    {
        return TblUsers::findOne(['id' => $id]);
    }

    private static function getJwt($token): Token
    {
        return Yii::$app->jwt->parse($token);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?TblUsers
    {
        $now = new \DateTimeImmutable();
        if (self::getJwt($token)->isExpired($now)) {
            return null;
        }
        return TblUsers::findOne(['accessToken' => $token]);
    }

    public static function findByUsername($username): ?TblUsers
    {
        return TblUsers::findOne(['username' => $username]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password): bool
    {
        return $this->password === md5($password);
    }
}