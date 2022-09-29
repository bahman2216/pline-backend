<?php

use yii\db\Migration;

/**
 * Class m220707_204015_new_view_pgers
 */
class m220707_204015_new_view_pgers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE OR REPLACE VIEW public."vwPagers"
            AS SELECT p.id,
                p.username,
                p.password,
                p.type_pager,
                p.zone_id,
                p.enable,
                p."desc",
                z.name AS zone_name
               FROM "tblPagers" p
                 LEFT JOIN "tblZones" z ON p.zone_id = z.id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP VIEW public."vwPagers"');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_204015_new_view_pgers cannot be reverted.\n";

        return false;
    }
    */
}
