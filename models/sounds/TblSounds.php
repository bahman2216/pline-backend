<?php

namespace app\models\sounds;

use Yii;

/**
 * This is the model class for table "tblSounds".
 *
 * @property int $id
 * @property string $name
 * @property string $file
 * @property string $desc
 */
class TblSounds extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblSounds';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'file'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['file'], 'string', 'max' => 256],
            [['desc'], 'string', 'max' => 1024],
            [['file'], 'unique'],
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
            'name' => 'نام صدا',
            'file' => 'مسیر فایل صوتی',
            'desc' => 'شرح',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TblSoundsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblSoundsQuery(get_called_class());
    }
}
