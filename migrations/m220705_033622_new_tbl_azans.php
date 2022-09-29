<?php

use yii\db\Migration;

/**
 * Class m220705_033622_new_tbl_azans
 */
class m220705_033622_new_tbl_azans extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tblAzans', [
            'id' => $this->primaryKey(),
            'date' => $this->string(10)->unique()->notNull(),
            'time1' => $this->string(8)->notNull(),
            'zones1' => $this->string()->notNull(),
            'sound1' => $this->integer()->notNull()->defaultValue(0),
            'befor_sound1' => $this->integer()->notNull()->defaultValue(0),
            'after_sound1' => $this->integer()->notNull()->defaultValue(0),
            'volume1' => $this->integer()->defaultValue(0)->notNull(),
            'enable1' => $this->boolean()->notNull()->defaultValue(false),

            'time2' => $this->string(8)->notNull(),
            'zones2' => $this->string()->notNull(),
            'sound2' => $this->integer()->notNull()->defaultValue(0),
            'befor_sound2' => $this->integer()->notNull()->defaultValue(0),
            'after_sound2' => $this->integer()->notNull()->defaultValue(0),
            'volume2' => $this->integer()->defaultValue(0)->notNull(),
            'enable2' => $this->boolean()->notNull()->defaultValue(false),

            'time3' => $this->string(8)->notNull(),
            'zones3' => $this->string()->notNull(),
            'sound3' => $this->integer()->notNull()->defaultValue(0),
            'befor_sound3' => $this->integer()->notNull()->defaultValue(0),
            'after_sound3' => $this->integer()->notNull()->defaultValue(0),
            'volume3' => $this->integer()->defaultValue(0)->notNull(),
            'enable3' => $this->boolean()->notNull()->defaultValue(false),

            'desc' => $this->string(512)->notNull()->defaultValue(''),
        ]);
        $this->createIndex('tblAzans_id', 'tblAzans', 'date', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tblAzans');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220705_033622_new_tbl_azans cannot be reverted.\n";

        return false;
    }
    */
}
