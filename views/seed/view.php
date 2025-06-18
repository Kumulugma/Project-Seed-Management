<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Nasiona', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seed-view">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 text-primary fw-bold">
                <i class="bi bi-info-circle me-2"></i><?= Html::encode($this->title) ?>
                <?php if (!empty($model->company)): ?>
                    <small class="text-muted">od <?= Html::encode($model->company) ?></small>
                <?php endif; ?>
            </h1>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                <?= Html::a('<i class="bi bi-pencil me-1"></i>Edytuj', ['update', 'id' => $model->id], [
                    'class' => 'btn btn-primary',
                    'encode' => false
                ]) ?>
                
                <?= Html::a('<i class="bi bi-files me-1"></i>Kopiuj', ['copy', 'id' => $model->id], [
                    'class' => 'btn btn-info',
                    'encode' => false
                ]) ?>
                
                <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Lista', ['index'], [
                    'class' => 'btn btn-outline-secondary',
                    'encode' => false
                ]) ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Szczegóły nasiona
                    </h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped detail-view'],
                        'attributes' => [
                            'name:text:Nazwa',
                            [
                                'attribute' => 'company',
                                'label' => 'Firma/Producent',
                                'value' => $model->company ?: 'Nie podano',
                                'format' => 'text',
                            ],
                            'description:text:Opis',
                            [
                                'attribute' => 'type',
                                'value' => $model->getTypeLabel(),
                                'format' => 'html',
                                'label' => 'Typ',
                            ],
                            [
                                'attribute' => 'height',
                                'value' => $model->getHeightLabel(),
                                'label' => 'Wysokość rośliny',
                            ],
                            [
                                'attribute' => 'plant_type',
                                'value' => $model->getPlantTypeLabel(),
                                'label' => 'Typ rośliny',
                            ],
                            [
                                'attribute' => 'sowing_start',
                                'value' => function($model) {
                                    return date('d.m', strtotime('2024-' . $model->sowing_start)) . ' (' . $model->sowing_start . ')';
                                },
                                'label' => 'Początek wysiewu',
                            ],
                            [
                                'attribute' => 'sowing_end',
                                'value' => function($model) {
                                    return date('d.m', strtotime('2024-' . $model->sowing_end)) . ' (' . $model->sowing_end . ')';
                                },
                                'label' => 'Koniec wysiewu',
                            ],
                            [
                                'attribute' => 'priority',
                                'value' => function($model) {
                                    $class = 'priority-' . $model->getPriorityClass();
                                    return '<span class="priority-badge ' . $class . '">' . $model->priority . '</span>';
                                },
                                'format' => 'html',
                                'label' => 'Priorytet',
                            ],
                            [
                                'attribute' => 'status',
                                'value' => function($model) {
                                    $class = $model->status === 'available' ? 'success' : 'secondary';
                                    return '<span class="badge bg-' . $class . '">' . $model->getStatusLabel() . '</span>';
                                },
                                'format' => 'html',
                                'label' => 'Status',
                            ],
                            'expiry_date:date:Data ważności',
                            'purchase_year:text:Rok zakupu',
                            'notes:ntext:Notatki',
                            'created_at:datetime:Data utworzenia',
                            'updated_at:datetime:Data aktualizacji',
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Akcje szybkie -->
            <div class="card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Szybkie akcje
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?php if ($model->status === \app\models\Seed::STATUS_AVAILABLE): ?>
                                <?= Html::a(
                                    '<i class="bi bi-check-circle me-2"></i>Oznacz jako zużyte', 
                                    ['mark-as-used', 'id' => $model->id], 
                                    [
                                        'class' => 'btn btn-warning w-100',
                                        'data-confirm' => 'Czy na pewno chcesz oznaczyć to nasiono jako zużyte?',
                                        'data-method' => 'post',
                                        'encode' => false
                                    ]
                                ) ?>
                            <?php else: ?>
                                <?= Html::a(
                                    '<i class="bi bi-arrow-clockwise me-2"></i>Przywróć jako dostępne', 
                                    ['mark-as-available', 'id' => $model->id], 
                                    [
                                        'class' => 'btn btn-success w-100',
                                        'data-confirm' => 'Czy na pewno chcesz przywrócić to nasiono jako dostępne?',
                                        'data-method' => 'post',
                                        'encode' => false
                                    ]
                                ) ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?= Html::a(
                                '<i class="bi bi-trash me-2"></i>Usuń nasiono', 
                                ['delete', 'id' => $model->id], 
                                [
                                    'class' => 'btn btn-danger w-100',
                                    'data-confirm' => 'Czy na pewno chcesz usunąć to nasiono? Ta operacja jest nieodwracalna.',
                                    'data-method' => 'post',
                                    'encode' => false
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Zdjęcie -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-image me-2"></i>Zdjęcie opakowania
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php if ($model->image_path && file_exists(Yii::getAlias('@webroot') . '/uploads/' . $model->image_path)): ?>
                        <img src="/uploads/<?= Html::encode($model->image_path) ?>" 
                             class="img-fluid rounded" 
                             style="max-height: 300px; width: auto;"
                             alt="<?= Html::encode($model->name) ?>">
                    <?php else: ?>
                        <div class="text-muted py-5">
                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                            <p class="mt-2">Brak zdjęcia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status wysiewu -->
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Status wysiewu
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $isInSeason = $model->isInSowingPeriod();
                    $currentDate = date('m-d');
                    ?>
                    
                    <?php if ($isInSeason): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Aktualny okres wysiewu!</strong><br>
                            Możesz wysiewać to nasiono teraz.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Poza okresem wysiewu</strong><br>
                            Okres: <?= date('d.m', strtotime('2024-' . $model->sowing_start)) ?> - 
                            <?= date('d.m', strtotime('2024-' . $model->sowing_end)) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Dziś:</strong> <?= date('d.m') ?><br>
                            <strong>Okres wysiewu:</strong> 
                            <?= date('d.m', strtotime('2024-' . $model->sowing_start)) ?> - 
                            <?= date('d.m', strtotime('2024-' . $model->sowing_end)) ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historia wysiewów -->
    <?php if (!empty($sownSeeds)): ?>
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Historia wysiewów (<?= count($sownSeeds) ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php
                $sownDataProvider = new ArrayDataProvider([
                    'allModels' => $sownSeeds,
                    'pagination' => ['pageSize' => 10],
                    'sort' => [
                        'attributes' => ['sown_date', 'status'],
                        'defaultOrder' => ['sown_date' => SORT_DESC]
                    ],
                ]);
                ?>
                
                <?= GridView::widget([
                    'dataProvider' => $sownDataProvider,
                    'layout' => "{items}\n{pager}",
                    'tableOptions' => ['class' => 'table table-striped'],
                    'columns' => [
                        [
                            'attribute' => 'sown_date',
                            'format' => 'date',
                            'label' => 'Data wysiewu',
                        ],
                        [
                            'attribute' => 'sowing_code',
                            'format' => 'html',
                            'value' => function($model) {
                                return '<code>' . Html::encode($model->sowing_code) . '</code>';
                            },
                            'label' => 'Kod',
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function($model) {
                                $statusOptions = [
                                    'sown' => ['label' => 'Wysiany', 'class' => 'secondary'],
                                    'germinated' => ['label' => 'Wykiełkował', 'class' => 'success'],
                                    'failed' => ['label' => 'Nie wykiełkował', 'class' => 'danger'],
                                ];
                                
                                $status = $statusOptions[$model->status] ?? ['label' => $model->status, 'class' => 'secondary'];
                                return '<span class="badge bg-' . $status['class'] . '">' . $status['label'] . '</span>';
                            },
                            'label' => 'Status',
                        ],
                        [
                            'attribute' => 'notes',
                            'value' => function($model) {
                                return $model->notes ? Html::encode(mb_substr($model->notes, 0, 100)) . (mb_strlen($model->notes) > 100 ? '...' : '') : '-';
                            },
                            'label' => 'Notatki',
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{note}',
                            'buttons' => [
                                'note' => function ($url, $model, $key) {
                                    return Html::a('<i class="bi bi-pencil-square"></i>', ['/dashboard/add-note', 'id' => $model->id], [
                                        'class' => 'btn btn-outline-primary btn-sm',
                                        'title' => 'Dodaj notatkę',
                                        'encode' => false
                                    ]);
                                },
                            ],
                            'label' => 'Akcje',
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.priority-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-weight: 600;
    color: white;
    font-size: 0.75rem;
}

.priority-high { background-color: #dc3545; }
.priority-medium { background-color: #fd7e14; }
.priority-low { background-color: #198754; }
.priority-none { background-color: #6c757d; }
</style>