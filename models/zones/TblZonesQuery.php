<?php

namespace app\models\zones;

/**
 * This is the ActiveQuery class for [[TblZones]].
 *
 * @see TblZones
 */
class TblZonesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblZones[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblZones|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
