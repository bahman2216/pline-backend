<?php

use yii\db\Migration;

/**
 * Class m220705_033505_new_tbl_sounds
 */
class m220705_033505_new_tbl_sounds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tblSounds', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique()->notNull(),
            'file' => $this->string(256)->unique()->notNull(),
            'desc' => $this->string(1024)->notNull()->defaultValue(''),
        ]);
        $this->createIndex('tblSounds_id', 'tblSounds', 'id', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tblSounds');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220705_033505_new_tbl_sounds cannot be reverted.\n";

        return false;
    }
    */
}
