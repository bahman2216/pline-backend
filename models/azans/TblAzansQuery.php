<?php

namespace app\models\azans;

/**
 * This is the ActiveQuery class for [[TblAzans]].
 *
 * @see TblAzans
 */
class TblAzansQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblAzans[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblAzans|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
