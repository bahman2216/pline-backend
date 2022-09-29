<?php

namespace app\models\pagers;

use app\models\zones\TblZones;
use app\models\zones\TblZonesQuery;
use Yii;

/**
 * This is the model class for table "tblPagers".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int $type_pager
 * @property int $zone_id
 * @property bool $enable
 * @property string $desc
 *
 * @property TblZones $zone
 */
class TblPagers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblPagers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'type_pager', 'zone_id'], 'required'],
            [['type_pager', 'zone_id'], 'default', 'value' => null],
            [['type_pager', 'zone_id'], 'integer'],
            [['enable'], 'boolean'],
            [['username', 'password'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 1024],
            [['username'], 'unique'],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblZones::className(), 'targetAttribute' => ['zone_id' => 'id']],
        ];
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
            'type_pager' => 'نوع پیجر',
            'zone_id' => 'ناحیه کاربری',
            'enable' => 'وضعیت فعال/غیرفعال',
            'desc' => 'شرح',
        ];
    }

    /**
     * Gets query for [[Zone]].
     *
     * @return \yii\db\ActiveQuery|TblZonesQuery
     */
    public function getZone()
    {
        return $this->hasOne(TblZones::className(), ['id' => 'zone_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblPagersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblPagersQuery(get_called_class());
    }
}
