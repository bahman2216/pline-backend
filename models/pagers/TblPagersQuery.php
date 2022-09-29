<?php

namespace app\models\pagers;

/**
 * This is the ActiveQuery class for [[TblPagers]].
 *
 * @see TblPagers
 */
class TblPagersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblPagers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblPagers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
