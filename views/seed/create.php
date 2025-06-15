<?php

use yii\helpers\Html;

$this->title = 'Dodaj nasiona';
$this->params['breadcrumbs'][] = ['label' => 'Nasiona', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seed-create">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 text-success fw-bold">
                <i class="bi bi-plus-circle me-2"></i><?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="col-md-4 text-md-end">
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Powrót do listy', ['index'], [
                'class' => 'btn btn-outline-secondary',
                'encode' => false
            ]) ?>
        </div>
    </div>

    <div class="alert alert-info mb-4">
        <div class="d-flex">
            <i class="bi bi-lightbulb me-3 fs-5"></i>
            <div>
                <h6 class="mb-2">💡 Wskazówki przy dodawaniu nasion:</h6>
                <ul class="mb-0">
                    <li>Wszystkie pola oznaczone <span class="text-danger fw-bold">*</span> są wymagane</li>
                    <li>Okres wysiewu może przechodzić przez nowy rok (np. 01.12 - 28.02)</li>
                    <li>Priorytet 0-10 (wyższy = ważniejsze nasiona)</li>
                    <li>Zdjęcie powinno pokazywać opakowanie nasion</li>
                    <li>Pamiętaj: wysiewasz <strong>paczkę nasion</strong>, nie pojedyncze nasionko!</li>
                </ul>
            </div>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>