<?php

namespace app\models\pagers;

/**
 * This is the ActiveQuery class for [[VwPagers]].
 *
 * @see VwPagers
 */
class VwPagersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return VwPagers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return VwPagers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
