<?php

use yii\db\Migration;

/**
 * Class m220705_033556_new_tbl_schedules
 */
class m220705_033556_new_tbl_schedules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tblSchedules', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique()->notNull(),
            'zones' => $this->string(1024)->notNull(),
            'sounds' => $this->string(1024)->notNull(),
            'schedules' => $this->string(2048)->notNull(),
            'enable' => $this->boolean()->notNull()->defaultValue(true),
            'desc' => $this->string(1024)->notNull()->defaultValue(''),
        ]);
        $this->createIndex('tblSchedules_id', 'tblSchedules', 'id', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tblSchedules');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220705_033556_new_tbl_schedules cannot be reverted.\n";

        return false;
    }
    */
}
