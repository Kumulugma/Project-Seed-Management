<?php

use yii\db\Migration;

class m250618_221519_add_company_to_seed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Dodaj kolumnę company do tabeli seed
        $this->addColumn('{{%seed}}', 'company', $this->string(255)->after('name'));
        
        // Dodaj indeks dla lepszej wydajności wyszukiwania
        $this->createIndex('idx-seed-company', '{{%seed}}', 'company');
        
        // Opcjonalnie: dodaj kilka przykładowych firm
        $this->update('{{%seed}}', ['company' => 'Vilmorin'], ['like', 'name', 'Vilmorin']);
        $this->update('{{%seed}}', ['company' => 'Torseed'], ['like', 'name', 'Torseed']);
        $this->update('{{%seed}}', ['company' => 'W. Legutko'], ['like', 'name', 'Legutko']);
        $this->update('{{%seed}}', ['company' => 'PlantiCo'], ['like', 'name', 'PlantiCo']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Usuń indeks
        $this->dropIndex('idx-seed-company', '{{%seed}}');
        
        // Usuń kolumnę
        $this->dropColumn('{{%seed}}', 'company');
    }
}
