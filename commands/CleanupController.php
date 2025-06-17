<?php

namespace app\commands;

use yii\console\Controller;
use app\models\PasswordResetToken;

class CleanupController extends Controller
{
    /**
     * Czyści wygasłe tokeny resetowania hasła
     * Użycie: php yii cleanup/expired-tokens
     */
    public function actionExpiredTokens()
    {
        echo "Czyszczenie wygasłych tokenów resetowania hasła...\n";
        
        $deleted = PasswordResetToken::cleanExpiredTokens();
        
        echo "Usunięto {$deleted} wygasłych tokenów.\n";
        
        return 0; // Sukces
    }
    
    /**
     * Pokazuje statystyki tokenów
     * Użycie: php yii cleanup/token-stats
     */
    public function actionTokenStats()
    {
        $total = PasswordResetToken::find()->count();
        $expired = PasswordResetToken::find()
            ->where(['<', 'expires_at', date('Y-m-d H:i:s')])
            ->count();
        $active = $total - $expired;
        
        echo "=== Statystyki tokenów resetowania hasła ===\n";
        echo "Łącznie tokenów: {$total}\n";
        echo "Aktywne tokeny: {$active}\n";
        echo "Wygasłe tokeny: {$expired}\n";
        
        if ($expired > 0) {
            echo "\nWygasłe tokeny można usunąć komendą: php yii cleanup/expired-tokens\n";
        }
        
        return 0;
    }
}