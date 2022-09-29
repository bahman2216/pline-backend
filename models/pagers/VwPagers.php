<?php

namespace app\models\pagers;

use Yii;

/**
 * This is the model class for table "vwPagers".
 *
 * @property int|null $id
 * @property string|null $username
 * @property string|null $password
 * @property int|null $type_pager
 * @property int|null $zone_id
 * @property bool|null $enable
 * @property string|null $desc
 * @property string|null $zone_name
 */
class VwPagers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vwPagers';
    }

    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_pager', 'zone_id'], 'default', 'value' => null],
            [['id', 'type_pager', 'zone_id'], 'integer'],
            [['enable'], 'boolean'],
            [['username', 'password', 'zone_name'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'type_pager' => 'Type Pager',
            'zone_id' => 'Zone ID',
            'enable' => 'Enable',
            'desc' => 'Desc',
            'zone_name' => 'Zone Name',
        ];
    }

    /**
     * {@inheritdoc}
     * @return VwPagersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VwPagersQuery(get_called_class());
    }
}
