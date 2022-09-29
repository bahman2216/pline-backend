<?php

use yii\db\Migration;

/**
 * Class m220705_033430_new_tbl_zones
 */
class m220705_033430_new_tbl_zones extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tblZones', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique()->notNull(),
            'desc' => $this->string(1024)->notNull()->defaultValue(''),
        ]);
        $this->createIndex('tblZones_id', 'tblZones', 'id', true);
        $this->insert('tblZones', [
            'name' => '.:: ناحیه عمومی ::.',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tblZones');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220705_033430_new_tbl_zones cannot be reverted.\n";

        return false;
    }
    */
}
