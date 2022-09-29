<?php

namespace app\models\sounds;

/**
 * This is the ActiveQuery class for [[TblSounds]].
 *
 * @see TblSounds
 */
class TblSoundsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblSounds[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblSounds|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
