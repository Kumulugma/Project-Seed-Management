<?php

use yii\db\Migration;

class m250617_222720_fix_seed_table extends Migration
{
    public function safeUp()
    {
        // Dodaj kolumnę notes do tabeli seed
        $this->addColumn('{{%seed}}', 'notes', $this->text());
        
        // Zmień typ kolumn sowing_start i sowing_end na string (MM-DD format)
        // Najpierw konwertuj istniejące dane
        $this->execute("UPDATE {{%seed}} SET 
            sowing_start = DATE_FORMAT(sowing_start, '%m-%d'),
            sowing_end = DATE_FORMAT(sowing_end, '%m-%d')
        ");
        
        // Zmień typ kolumn na varchar(5) dla formatu MM-DD
        $this->alterColumn('{{%seed}}', 'sowing_start', $this->string(5)->notNull());
        $this->alterColumn('{{%seed}}', 'sowing_end', $this->string(5)->notNull());
    }

    public function safeDown()
    {
        // Przywróć poprzedni format (z aktualnym rokiem)
        $currentYear = date('Y');
        $this->execute("UPDATE {{%seed}} SET 
            sowing_start = CONCAT('$currentYear-', sowing_start),
            sowing_end = CONCAT('$currentYear-', sowing_end)
        ");
        
        $this->alterColumn('{{%seed}}', 'sowing_start', $this->date()->notNull());
        $this->alterColumn('{{%seed}}', 'sowing_end', $this->date()->notNull());
        
        // Usuń kolumnę notes
        $this->dropColumn('{{%seed}}', 'notes');
    }
}
