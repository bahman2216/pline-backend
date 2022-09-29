<?php

use yii\db\Migration;

/**
 * Class m190107_105918_new_tbl_users
 */
class m190107_105918_new_tbl_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tblUsers', [
            'id' => $this->primaryKey(),
            'username' => $this->string(20)->notNull()->unique(),
            'password' => $this->string(32)->notNull(),
            'authKey' => $this->string(),
            'accessToken' => $this->string(),
            'id_number' => $this->string(),
            'f_name' => $this->string()->notNull(),
            'l_name' => $this->string()->notNull(),
            'departeman' => $this->string(),
            'enable' => $this->boolean()->notNull()->defaultValue(true),
            'desc' => $this->string(1024),
        ]);

        $this->insert('tblUsers', [
            'username' => 'admin',
            'password' => md5('admin'),
            'authKey' => '',
            'accessToken' => '',
            'f_name' => 'کاربر',
            'l_name' => 'ارشد',
            'enable' => true
        ]);
        $this->insert('tblUsers', [
            'username' => 'test',
            'password' => md5('test'),
            'authKey' => '',
            'accessToken' => '',
            'f_name' => 'کاربر ',
            'l_name' => 'تست',
            'enable' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tblUsers');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190107_105918_new_tbl_users cannot be reverted.\n";

        return false;
    }
    */
}