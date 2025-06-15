<?php
/**
 * =================================================================
 * LOKALIZACJA: views/dashboard/add-note.php
 */
?>
<?php
/* @var $this yii\web\View */
/* @var $sownSeed app\models\SownSeed */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Dodaj notatkę - ' . $sownSeed->seed->name;
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/dashboard/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="add-note">
    <div class="row">
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a('🔙 Powrót', ['/dashboard/index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">📝 Informacje o wysiewie</h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="dl-horizontal">
                        <dt>Nasiono:</dt>
                        <dd><strong><?= Html::encode($sownSeed->seed->name) ?></strong></dd>
                        
                        <dt>Kod wysiewu:</dt>
                        <dd><code><?= Html::encode($sownSeed->sowing_code) ?></code></dd>
                        
                        <dt>Data wysiewu:</dt>
                        <dd><?= date('d.m.Y', strtotime($sownSeed->sown_date)) ?></dd>
                        
                        <dt>Dni od wysiewu:</dt>
                        <dd><?= $sownSeed->getDaysFromSowing() ?></dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="dl-horizontal">
                        <dt>Typ:</dt>
                        <dd><?= $sownSeed->seed->getTypeLabel() ?></dd>
                        
                        <dt>Status:</dt>
                        <dd>
                            <span class="label label-<?= $sownSeed->getStatusColor() ?>">
                                <?= $sownSeed->getStatusLabel() ?>
                            </span>
                        </dd>
                        
                        <dt>Wysokość:</dt>
                        <dd><?= $sownSeed->seed->getHeightLabel() ?></dd>
                        
                        <dt>Typ rośliny:</dt>
                        <dd><?= $sownSeed->seed->getPlantTypeLabel() ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">📝 Notatka</h4>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['/dashboard/add-note', 'id' => $sownSeed->id]
            ]); ?>

            <div class="form-group">
                <label for="notes" class="control-label">Notatka dotycząca tego wysiewu:</label>
                <textarea name="notes" id="notes" class="form-control" rows="6" 
                          placeholder="Wpisz swoje obserwacje, warunki wysiewu, problemy, postępy kiełkowania itp."><?= Html::encode($sownSeed->notes) ?></textarea>
                <p class="help-block">
                    Możesz tutaj zapisać wszelkie obserwacje dotyczące tego wysiewu, 
                    np. warunki pogodowe, sposób przygotowania podłoża, postęp kiełkowania, problemy.
                </p>
            </div>

            <div class="form-group">
                <?= Html::submitButton('💾 Zapisz notatkę', ['class' => 'btn btn-primary btn-lg']) ?>
                <?= Html::a('❌ Anuluj', ['/dashboard/index'], ['class' => 'btn btn-default btn-lg']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Przykłady notatek -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">💡 Przykłady przydatnych notatek</h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>🌱 Kiełkowanie:</h5>
                    <ul class="text-muted">
                        <li>"Pierwsze kiełki po 3 dniach"</li>
                        <li>"Kiełkowanie nierównomierne"</li>
                        <li>"Wykiełkowało 80% nasion"</li>
                        <li>"Silne, zdrowe kiełki"</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>🌡️ Warunki:</h5>
                    <ul class="text-muted">
                        <li>"Temperatura 20°C, wilgotno"</li>
                        <li>"Wysiane w cieplarni"</li>
                        <li>"Podłoże uniwersalne + perlit"</li>
                        <li>"Nakryte folią do kiełkowania"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>