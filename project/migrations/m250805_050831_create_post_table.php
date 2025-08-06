<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m250805_050831_create_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'author' => $this->string(15)->notNull(),
            'email' => $this->string()->notNull(),
            'message' => $this->text()->notNull(),
            'ip' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer(),
            'deleted_at' => $this->integer(),
            'edit_token' => $this->string(32)->notNull(),
            'delete_token' => $this->string(32)->notNull(),
        ]);
        $this->createIndex('post-ip', 'post', 'ip');
        $this->createIndex('post-author', 'post', 'author');
        $this->createIndex('post-email', 'post', 'email');
        $this->createIndex('post-created_at', 'post', 'created_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%post}}');
    }
}
