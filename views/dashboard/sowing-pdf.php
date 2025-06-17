<?php
/**
 * LOKALIZACJA: views/dashboard/sowing-pdf.php
 */

/* @var $this yii\web\View */
/* @var $seeds app\models\Seed[] */
/* @var $date string */

use yii\helpers\Html;
?>

<style>
body { 
    font-family: DejaVuSans, Arial, sans-serif; 
    font-size: 12px; 
    line-height: 1.4;
    margin: 0;
    padding: 20px;
}

.header { 
    text-align: center; 
    margin-bottom: 30px; 
    border-bottom: 2px solid #333;
    padding-bottom: 15px;
}

.header h1 {
    margin: 0;
    font-size: 24px;
    color: #2e7d32;
}

.header .date {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.seed-table { 
    width: 100%; 
    border-collapse: collapse; 
    margin-bottom: 20px; 
    font-size: 11px;
}

.seed-table th, .seed-table td { 
    border: 1px solid #333; 
    padding: 8px 6px; 
    text-align: left; 
    vertical-align: top;
}

.seed-table th { 
    background-color: #f5f5f5; 
    font-weight: bold; 
    text-align: center;
}

.checkbox { 
    width: 18px; 
    height: 18px; 
    border: 2px solid #333; 
    display: inline-block; 
    margin: 2px;
}

.priority-high {
    background-color: #ffeb3b;
}

.priority-medium {
    background-color: #fff;
}

.notes-section {
    margin-top: 30px;
    border-top: 1px solid #ccc;
    padding-top: 20px;
}

.footer {
    position: fixed;
    bottom: 20px;
    left: 20px;
    right: 20px;
    font-size: 10px;
    color: #666;
    text-align: center;
    border-top: 1px solid #ccc;
    padding-top: 10px;
}

.instructions {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.instructions h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #2e7d32;
}

.instructions ul {
    margin: 0;
    padding-left: 20px;
}

.instructions li {
    margin-bottom: 5px;
}
</style>

<div class="header">
    <h1>🌱 Plan wysiewu nasion</h1>
    <div class="date">Data: <?= date('d.m.Y', strtotime($date)) ?> (<?= date('l', strtotime($date)) ?>)</div>
    <div class="date">Łącznie nasion: <?= count($seeds) ?></div>
</div>

<div class="instructions">
    <h3>📋 Instrukcje wysiewu:</h3>
    <ul>
        <li>Sprawdź stan nasion przed wysiewem (data ważności, wilgotność)</li>
        <li>Przygotuj odpowiednie podłoże zgodnie z wymaganiami nasion</li>
        <li>Oznacz miejsce wysiewu etykietą z kodem</li>
        <li>Zanotuj datę wysiewu w kratce obok nazwy</li>
        <li>Po wysiewie zaznacz kratkę "Wysiany"</li>
    </ul>
</div>

<table class="seed-table">
    <thead>
        <tr>
            <th style="width: 8%;">Wysiany</th>
            <th style="width: 25%;">Nazwa nasiona</th>
            <th style="width: 12%;">Typ</th>
            <th style="width: 10%;">Wysokość</th>
            <th style="width: 10%;">Typ rośliny</th>
            <th style="width: 15%;">Okres wysiewu</th>
            <th style="width: 8%;">Priorytet</th>
            <th style="width: 12%;">Data wysiewu</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($seeds as $index => $seed): ?>
            <tr class="<?= $seed->priority >= 7 ? 'priority-high' : 'priority-medium' ?>">
                <td style="text-align: center;">
                    <span class="checkbox"></span>
                </td>
                <td>
                    <strong><?= Html::encode($seed->name) ?></strong>
                    <?php if ($seed->description): ?>
                        <br><small style="color: #666;"><?= Html::encode(substr($seed->description, 0, 80)) ?><?= strlen($seed->description) > 80 ? '...' : '' ?></small>
                    <?php endif; ?>
                </td>
                <td><?= $seed->getTypeLabel() ?></td>
                <td><?= $seed->getHeightLabel() ?></td>
                <td><?= $seed->getPlantTypeLabel() ?></td>
                <td style="text-align: center;">
                    <?= $seed->getFormattedSowingDate('sowing_start') ?> - <?= $seed->getFormattedSowingDate('sowing_end') ?>
                </td>
                <td style="text-align: center;">
                    <strong><?= $seed->priority ?></strong>
                </td>
                <td style="border-bottom: 1px dotted #999; height: 25px;">
                    <!-- Miejsce na wpisanie daty -->
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="notes-section">
    <h3>📝 Notatki z wysiewu:</h3>
    <div style="border: 1px solid #ccc; min-height: 100px; padding: 10px;">
        <!-- Miejsce na notatki -->
    </div>
</div>

<div class="notes-section">
    <h3>📅 Harmonogram sprawdzania kiełkowania:</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="border: 1px solid #ccc; padding: 10px; width: 25%;">
                <strong>Po 3-5 dniach:</strong><br>
                Pierwsze sprawdzenie
            </td>
            <td style="border: 1px solid #ccc; padding: 10px; width: 25%;">
                <strong>Po tygodniu:</strong><br>
                Sprawdzenie kiełkowania
            </td>
            <td style="border: 1px solid #ccc; padding: 10px; width: 25%;">
                <strong>Po 2 tygodniach:</strong><br>
                Ocena końcowa
            </td>
            <td style="border: 1px solid #ccc; padding: 10px; width: 25%;">
                <strong>Uwagi:</strong><br>
                Warunki, temperatura, wilgotność
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #ccc; padding: 10px; height: 40px;">
                Data: ___________
            </td>
            <td style="border: 1px solid #ccc; padding: 10px; height: 40px;">
                Data: ___________
            </td>
            <td style="border: 1px solid #ccc; padding: 10px; height: 40px;">
                Data: ___________
            </td>
            <td style="border: 1px solid #ccc; padding: 10px; height: 40px;">
                <!-- Miejsce na uwagi -->
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    System Zarządzania Nasionami | Wygenerowano: <?= date('d.m.Y H:i') ?> | 
    Nasiona z priorytetem ≥7 oznaczone żółtym tłem
</div>