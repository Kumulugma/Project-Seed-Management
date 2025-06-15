<?php
/**
 * =================================================================
 * LOKALIZACJA: views/seed/update.php
 */
?>
<?php
use yii\helpers\Html;

$this->title = 'Edytuj: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Nasiona', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edytuj';
?>

<div class="seed-update">
    <div class="row">
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <?= Html::a('👁️ Zobacz', ['view', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
                <?= Html::a('🔙 Powrót do listy', ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php if ($model->status === 'used'): ?>
        <div class="alert alert-warning">
            <strong>⚠️ Uwaga:</strong> To nasiono jest oznaczone jako "zużyte". 
            Możesz zmienić status na "dostępne" jeśli nadal je masz.
        </div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>