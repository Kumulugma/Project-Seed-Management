<?php
/**
 * LOKALIZACJA: views/dashboard/label.php
 */

/* @var $this yii\web\View */
/* @var $sownSeed app\models\SownSeed */

use yii\helpers\Html;
?>

<style>
body { 
    font-family: DejaVuSans, Arial, sans-serif; 
    font-size: 5px; 
    margin: 0; 
    padding: 0.5mm;
    line-height: 1.1;
}

.label-content {
    width: 38mm;
    height: 13mm;
    border: 1px solid #000;
    padding: 0.5mm;
    box-sizing: border-box;
    position: relative;
}

.name { 
    font-weight: bold; 
    font-size: 6px; 
    margin-bottom: 0.5mm;
    text-transform: uppercase;
    line-height: 1;
}

.code { 
    font-size: 4px; 
    margin-bottom: 0.5mm;
    font-family: monospace;
}

.info { 
    font-size: 4px;
    margin-bottom: 0.5mm;
}

.date-line { 
    border-bottom: 1px solid #000; 
    width: 12mm; 
    height: 1.5mm; 
    display: inline-block;
    margin-left: 1mm;
}

.bottom-info {
    position: absolute;
    bottom: 0.5mm;
    left: 0.5mm;
    right: 0.5mm;
    font-size: 3.5px;
}

.height-info {
    float: left;
}

.plant-type-info {
    float: right;
}
</style>

<div class="label-content">
    <div class="name">
        <?= Html::encode(substr($sownSeed->seed->name, 0, 20)) ?>
    </div>
    
    <div class="code">
        KOD: <?= Html::encode($sownSeed->sowing_code) ?>
    </div>
    
    <div class="info">
        DATA: <span class="date-line"></span>
    </div>
    
    <div class="bottom-info">
        <span class="height-info">
            <?= substr($sownSeed->seed->getHeightLabel(), 0, 1) ?>
        </span>
        <span class="plant-type-info">
            <?= $sownSeed->seed->plant_type === 'annual' ? 'J' : 'B' ?>
        </span>
    </div>
</div>