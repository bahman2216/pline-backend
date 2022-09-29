<?php

namespace app\models\schedules;

use app\pline\tools\PersianDate;
use Yii;

/**
 * This is the model class for table "tblSchedules".
 *
 * @property int $id
 * @property string $name
 * @property array $zones
 * @property array $sounds
 * @property array $schedules
 * @property bool $enable
 * @property string $desc
 */
class TblSchedules extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tblSchedules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'zones', 'sounds', 'schedules'], 'required'],
            [['enable'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 1024],
            [['name'], 'unique'],
            ['schedules', 'SchedulesValidation'],
            ['sounds', 'soundsValidations'],
            ['zones', 'zonesValidations'],
        ];
    }

    public function soundsValidations($attribute, $params, $validator)
    {
        if (count($this->sounds) == 0) {
            $this->addError($attribute, "صدا ها نمی تواند خالی باشد");
        } else {
            $this->sounds = json_encode($this->sounds);
            return true;
        }
        return false;
    }

    public function zonesValidations($attribute, $params, $validator)
    {
        if (count($this->zones) == 0) {
            $this->addError($attribute, "ناحیه ها نمی تواند خالی باشد");
        } else {
            $this->zones = json_encode($this->zones);
            return true;
        }
        return false;
    }

    public function SchedulesValidation($attribute, $params, $validator)
    {

        if ($this->schedules['type'] == 'date') {
            if (PersianDate::isValidTime($this->schedules['date']['time']) === false) {
                $this->addError($attribute, "ساعت وارد شده اشتباه می باشد");
                return false;
            }
            if (PersianDate::isValidDate($this->schedules['date']['date']) === false) {
                $this->addError($attribute, "تاریخ وارد شده اشتباه می باشد");
                return false;
            }
        } else if ($this->schedules['type'] == 'week') {
        } else {
            $this->addError($attribute, "نوع درخواست زمانبندی اشتباه می باشد");
        }
        $this->schedules = json_encode($this->schedules);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'نام زمانبندی پخش',
            'zones' => 'ناحیه ها',
            'sounds' => 'صداها',
            'schedules' => 'جدول زمانبندی',
            'enable' => 'وضعیت',
            'desc' => 'شرح',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TblSchedulesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblSchedulesQuery(get_called_class());
    }
}