<?php

namespace app\models\schedules;

/**
 * This is the ActiveQuery class for [[TblSchedules]].
 *
 * @see TblSchedules
 */
class TblSchedulesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblSchedules[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblSchedules|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
