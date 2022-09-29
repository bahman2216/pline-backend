<?php

namespace app\models\azans;

use app\pline\tools\PersianDate;
use Yii;

/**
 * This is the model class for table "tblAzans".
 *
 * @property int $id
 * @property string $date
 * @property string $time1
 * @property string $zones1
 * @property int $sound1
 * @property int $befor_sound1
 * @property int $after_sound1
 * @property int $volume1
 * @property bool $enable1
 * @property string $time2
 * @property string $zones2
 * @property int $sound2
 * @property int $befor_sound2
 * @property int $after_sound2
 * @property int $volume2
 * @property bool $enable2
 * @property string $time3
 * @property string $zones3
 * @property int $sound3
 * @property int $befor_sound3
 * @property int $after_sound3
 * @property int $volume3
 * @property bool $enable3
 * @property string $desc
 */
class TblAzans extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblAzans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'time1', 'zones1', 'time2', 'zones2', 'time3', 'zones3'], 'required'],
            [['sound1', 'befor_sound1', 'after_sound1', 'volume1', 'sound2', 'befor_sound2', 'after_sound2', 'volume2', 'sound3', 'befor_sound3', 'after_sound3', 'volume3'], 'default', 'value' => null],
            [['sound1', 'befor_sound1', 'after_sound1', 'volume1', 'sound2', 'befor_sound2', 'after_sound2', 'volume2', 'sound3', 'befor_sound3', 'after_sound3', 'volume3'], 'integer'],
            [['enable1', 'enable2', 'enable3'], 'boolean'],
            [['date'], 'string', 'max' => 10],
            [['time1', 'time2', 'time3'], 'string', 'max' => 8],
            [['desc'], 'string', 'max' => 512],
            [['date'], 'unique'],
            [['time1', 'time2', 'time3',], 'timeValidations'],
            [['zones1', 'zones2', 'zones3'], 'zonesValidations'],
            [['zones1', 'zones2', 'zones3'], 'string', 'max' => 255],
            [['date'], 'dateValidations'],
        ];
    }

    public function timeValidations($attribute, $params, $validator)
    {
        if (PersianDate::isValidTime($this->$attribute, true)) {
            return true;
        }
        $this->addError($attribute, " ساعت " . $this->attributeLabels()[$attribute] . " وارد شده اشتباه می باشد");
        return false;
    }

    public function dateValidations($attribute, $params, $validator)
    {
        if (PersianDate::isValidDate($this->$attribute)) {
            return true;
        }
        $this->addError($attribute, "تاریخ وارد شده اشتباه می باشد");
        return false;
    }


    public function zonesValidations($attribute, $params, $validator)
    {
        $this->$attribute = json_encode($this->$attribute);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'تاریخ',
            'time1' => 'اذان صبح',
            'zones1' => 'ناحیه ها',
            'sound1' => 'صداهای اذان',
            'befor_sound1' => 'صدای های قبل اذان',
            'after_sound1' => 'صدای بعد اذان',
            'volume1' => 'حجم صدا',
            'enable1' => 'فعال/غیرفعال',
            'time2' => 'اذان ظهر',
            'zones2' => 'ناحیه ها',
            'sound2' => 'صداهای اذان',
            'befor_sound2' => 'صدای های قبل اذان',
            'after_sound2' => 'صدای بعد اذان',
            'volume2' => 'حجم صدا',
            'enable2' => 'فعال/غیرفعال',
            'time3' => 'اذان مغرب',
            'zones3' => 'ناحیه ها',
            'sound3' => 'صداهای اذان',
            'befor_sound3' => 'صدای های قبل اذان',
            'after_sound3' => 'صدای بعد اذان',
            'volume3' => 'حجم صدا',
            'enable3' => 'فعال/غیرفعال',
            'desc' => 'شرح',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TblAzansQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblAzansQuery(get_called_class());
    }
}
