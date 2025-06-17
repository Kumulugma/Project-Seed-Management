<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%password_reset_token}}`.
 */
class m250617_222421_create_password_reset_token_table extends Migration
{
    public function safeUp()
    {
        // Tworzenie tabeli dla tokenów resetowania hasła
        $this->createTable('{{%password_reset_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'expires_at' => $this->timestamp()->notNull(),
        ]);

        // Indeksy
        $this->createIndex('idx-password_reset_token-user_id', '{{%password_reset_token}}', 'user_id');
        $this->createIndex('idx-password_reset_token-token', '{{%password_reset_token}}', 'token');
        $this->createIndex('idx-password_reset_token-expires_at', '{{%password_reset_token}}', 'expires_at');

        // Klucz obcy
        $this->addForeignKey(
            'fk-password_reset_token-user_id',
            '{{%password_reset_token}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-password_reset_token-user_id', '{{%password_reset_token}}');
        $this->dropTable('{{%password_reset_token}}');
    }
}
