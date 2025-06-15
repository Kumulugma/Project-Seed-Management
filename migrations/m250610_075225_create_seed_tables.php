<?php

use yii\db\Migration;

class m250610_075225_create_seed_tables extends Migration
{
    public function safeUp()
    {
        // Tabela użytkowników
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32),
            'email' => $this->string(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Tabela nasion
        $this->createTable('{{%seed}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'image_path' => $this->string(),
            'expiry_date' => $this->date(),
            'purchase_year' => $this->integer(),
            'height' => $this->string(10)->notNull(),
            'type' => $this->string(20)->notNull(),
            'sowing_start' => $this->date()->notNull(),
            'sowing_end' => $this->date()->notNull(),
            'plant_type' => $this->string(20)->notNull(),
            'status' => $this->string(20)->defaultValue('available'),
            'priority' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Tabela wysianych nasion
        $this->createTable('{{%sown_seed}}', [
            'id' => $this->primaryKey(),
            'seed_id' => $this->integer()->notNull(),
            'sown_date' => $this->date()->notNull(),
            'status' => $this->string(20)->defaultValue('sown'),
            'sowing_code' => $this->string(50),
            'notes' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Indeksy
        $this->createIndex('idx-seed-type', '{{%seed}}', 'type');
        $this->createIndex('idx-seed-status', '{{%seed}}', 'status');
        $this->createIndex('idx-seed-sowing_dates', '{{%seed}}', ['sowing_start', 'sowing_end']);
        $this->createIndex('idx-sown_seed-status', '{{%sown_seed}}', 'status');
        $this->createIndex('idx-sown_seed-sown_date', '{{%sown_seed}}', 'sown_date');

        // Klucze obce
        $this->addForeignKey(
            'fk-sown_seed-seed_id',
            '{{%sown_seed}}',
            'seed_id',
            '{{%seed}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Dodaj domyślnego użytkownika (admin/admin123)
        $this->insert('{{%user}}', [
            'username' => 'admin',
            'password_hash' => '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO', // admin123
            'auth_key' => 'test100key',
            'email' => 'admin@example.com',
        ]);

        // Dodaj przykładowe nasiona
        $this->insert('{{%seed}}', [
            'name' => 'Pomidor Malinowy Ożarowski',
            'description' => 'Odmiana wysoka, owoce duże, mięsiste',
            'expiry_date' => '2026-12-31',
            'purchase_year' => 2024,
            'height' => 'high',
            'type' => 'vegetables',
            'sowing_start' => '2024-02-15',
            'sowing_end' => '2024-04-15',
            'plant_type' => 'annual',
            'priority' => 8,
        ]);

        $this->insert('{{%seed}}', [
            'name' => 'Bazylia Pospolita',
            'description' => 'Aromatyczne zioło, łatwe w uprawie',
            'expiry_date' => '2025-12-31',
            'purchase_year' => 2024,
            'height' => 'low',
            'type' => 'herbs',
            'sowing_start' => '2024-03-01',
            'sowing_end' => '2024-05-31',
            'plant_type' => 'annual',
            'priority' => 5,
        ]);

        $this->insert('{{%seed}}', [
            'name' => 'Słonecznik Ozdobny',
            'description' => 'Wysokie kwiaty, różne odmiany',
            'expiry_date' => '2025-08-31',
            'purchase_year' => 2024,
            'height' => 'high',
            'type' => 'flowers',
            'sowing_start' => '2024-04-01',
            'sowing_end' => '2024-06-15',
            'plant_type' => 'annual',
            'priority' => 3,
        ]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-sown_seed-seed_id', '{{%sown_seed}}');
        $this->dropTable('{{%sown_seed}}');
        $this->dropTable('{{%seed}}');
        $this->dropTable('{{%user}}');
    }
}
