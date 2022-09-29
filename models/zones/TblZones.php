<?php

namespace app\models\zones;

use app\models\pagers\TblPagers;
use app\models\pagers\TblPagersQuery;
use Yii;

/**
 * This is the model class for table "tblZones".
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 *
 * @property TblPagers[] $tblPagers
 */
class TblZones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblZones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 1024],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'نام ناحیه',
            'desc' => 'شرح',
        ];
    }

    /**
     * Gets query for [[TblPagers]].
     *
     * @return \yii\db\ActiveQuery|TblPagersQuery
     */
    public function getTblPagers()
    {
        return $this->hasMany(TblPagers::className(), ['zone_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return TblZonesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblZonesQuery(get_called_class());
    }
}
