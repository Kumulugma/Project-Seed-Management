<?php

use yii\helpers\Html;
use app\models\Seed;
use yii\widgets\LinkPager;

$this->title = 'Lista nasion';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seed-index">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 text-primary fw-bold">
                <i class="bi bi-list-ul me-2"></i><?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group-responsive" role="group">
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-success flex-fill" href="/seed/create">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span class="d-none d-sm-inline">Dodaj</span>
                        <span class="d-sm-none">+</span>
                        <span class="d-none d-md-inline"> nasiona</span>
                    </a>
                    <a class="btn btn-info flex-fill" href="/seed/stats">
                        <i class="bi bi-graph-up me-1"></i>
                        <span class="d-none d-sm-inline">Statystyki</span>
                        <span class="d-sm-none">Stats</span>
                    </a>
                    <a class="btn btn-outline-secondary flex-fill" href="/seed/export">
                        <i class="bi bi-download me-1"></i>
                        <span class="d-none d-sm-inline">Eksport</span>
                        <span class="d-sm-none">CSV</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtry i wyszukiwanie -->
    <div class="card mb-4">
        <div class="card-body">
            <?= Html::beginForm(['index'], 'get', ['class' => 'row g-3']) ?>
            
            <div class="col-lg-4 col-md-6">
                <label class="form-label">Szukaj</label>
                <?= Html::textInput('search', Yii::$app->request->get('search'), [
                    'class' => 'form-control',
                    'placeholder' => 'Nazwa, opis, producent...'
                ]) ?>
            </div>

            <div class="col-lg-2 col-md-3">
                <label class="form-label">Typ</label>
                <?= Html::dropDownList('type', Yii::$app->request->get('type'), 
                    array_merge(['' => 'Wszystkie'], (new Seed())->getTypeOptions()), 
                    ['class' => 'form-select']
                ) ?>
            </div>

            <div class="col-lg-2 col-md-3">
                <label class="form-label">Status</label>
                <?= Html::dropDownList('status', Yii::$app->request->get('status'), 
                    array_merge(['' => 'Wszystkie'], (new Seed())->getStatusOptions()), 
                    ['class' => 'form-select']
                ) ?>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <?= Html::submitButton('<i class="bi bi-search me-1"></i>Szukaj', [
                        'class' => 'btn btn-primary',
                        'encode' => false
                    ]) ?>
                </div>
            </div>

            <?php if (Yii::$app->request->get('search') || Yii::$app->request->get('type') || Yii::$app->request->get('status')): ?>
                <div class="col-12">
                    <?= Html::a('<i class="bi bi-x-circle me-1"></i>Wyczyść filtry', ['index'], [
                        'class' => 'btn btn-outline-secondary',
                        'encode' => false
                    ]) ?>
                </div>
            <?php endif; ?>

            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($dataProvider->totalCount > 0): ?>
                <!-- 5 kolumn z mniejszymi marginesami -->
                <div class="row g-2" id="seeds-grid">
                    <?php foreach ($dataProvider->models as $model): ?>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card h-100 seed-card">
                                <div class="card-header d-flex justify-content-between align-items-center p-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?= $model->type === 'vegetables' ? 'success' : ($model->type === 'flowers' ? 'primary' : 'info') ?> me-1 small">
                                            <?= $model->getTypeLabel() ?>
                                        </span>
                                        <span class="priority-badge priority-<?= $model->priority >= 8 ? 'high' : ($model->priority >= 5 ? 'medium' : ($model->priority > 0 ? 'low' : 'none')) ?> small">
                                            <?= $model->priority ?>
                                        </span>
                                    </div>
                                    <span class="badge bg-<?= $model->status === 'available' ? 'success' : 'secondary' ?> small">
                                        <?= $model->getStatusLabel() ?>
                                    </span>
                                </div>
                                
                                <div class="card-body p-2">
                                    <?php if ($model->image_path && file_exists(Yii::getAlias('@webroot') . '/uploads/' . $model->image_path)): ?>
                                        <div class="text-center mb-2">
                                            <img src="/uploads/<?= Html::encode($model->image_path) ?>" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 80px; max-width: 100%; object-fit: cover;"
                                                 alt="<?= Html::encode($model->name) ?>">
                                        </div>
                                    <?php endif; ?>

                                    <h6 class="card-title mb-1 small fw-bold">
                                        <?= Html::encode($model->name) ?>
                                    </h6>
                                    
                                    <!-- Nazwa firmy/producenta -->
                                    <?php if (!empty($model->company)): ?>
                                        <p class="card-text small text-muted mb-1">
                                            <i class="bi bi-building me-1"></i><?= Html::encode($model->company) ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                                        <span>
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= date('d.m', strtotime('2024-' . $model->sowing_start)) ?> - 
                                            <?= date('d.m', strtotime('2024-' . $model->sowing_end)) ?>
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <?= $model->getHeightLabel() ?>
                                        </span>
                                    </div>

                                    <?php if ($model->description): ?>
                                        <p class="card-text small">
                                            <?= Html::encode(mb_substr($model->description, 0, 60)) ?><?= mb_strlen($model->description) > 60 ? '...' : '' ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="card-footer p-2 bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                                            'class' => 'btn btn-outline-primary btn-sm flex-fill',
                                            'title' => 'Zobacz szczegóły',
                                            'encode' => false
                                        ]) ?>
                                        
                                        <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                                            'class' => 'btn btn-outline-secondary btn-sm flex-fill',
                                            'title' => 'Edytuj',
                                            'encode' => false
                                        ]) ?>
                                        
                                        <?php if ($model->status === Seed::STATUS_AVAILABLE): ?>
                                            <?= Html::a('<i class="bi bi-check-circle"></i>', ['mark-as-used', 'id' => $model->id], [
                                                'class' => 'btn btn-outline-warning btn-sm flex-fill',
                                                'title' => 'Oznacz jako zużyte',
                                                'data-confirm' => 'Czy na pewno chcesz oznaczyć to nasiono jako zużyte?',
                                                'data-method' => 'post',
                                                'encode' => false
                                            ]) ?>
                                        <?php else: ?>
                                            <?= Html::a('<i class="bi bi-arrow-clockwise"></i>', ['mark-as-available', 'id' => $model->id], [
                                                'class' => 'btn btn-outline-success btn-sm flex-fill',
                                                'title' => 'Oznacz jako dostępne',
                                                'data-confirm' => 'Czy na pewno chcesz przywrócić to nasiono jako dostępne?',
                                                'data-method' => 'post',
                                                'encode' => false
                                            ]) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <?= LinkPager::widget([
                        'pagination' => $dataProvider->pagination,
                        'options' => ['class' => 'pagination'],
                        'linkOptions' => ['class' => 'page-link'],
                        'pageCssClass' => 'page-item',
                        'prevPageCssClass' => 'page-item',
                        'nextPageCssClass' => 'page-item',
                        'firstPageCssClass' => 'page-item',
                        'lastPageCssClass' => 'page-item',
                        'disabledPageCssClass' => 'page-item disabled',
                        'activePageCssClass' => 'page-item active',
                    ]); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Brak nasion</h4>
                    <p class="text-muted">Nie znaleziono nasion spełniających kryteria wyszukiwania.</p>
                    <?= Html::a('<i class="bi bi-plus-circle me-2"></i>Dodaj pierwsze nasiona', ['create'], [
                        'class' => 'btn btn-success btn-lg',
                        'encode' => false
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary bg-opacity-10 border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-collection text-primary display-6"></i>
                    <h4 class="text-primary"><?= $dataProvider->totalCount ?></h4>
                    <p class="text-muted mb-0">Łącznie nasion</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success bg-opacity-10 border-success">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success display-6"></i>
                    <h4 class="text-success"><?= Seed::find()->where(['status' => Seed::STATUS_AVAILABLE])->count() ?></h4>
                    <p class="text-muted mb-0">Dostępnych</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning bg-opacity-10 border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle text-warning display-6"></i>
                    <h4 class="text-warning"><?= count(Seed::getSowingSeeds()) ?></h4>
                    <p class="text-muted mb-0">Do wysiewu</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-secondary bg-opacity-10 border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle text-secondary display-6"></i>
                    <h4 class="text-secondary"><?= Seed::find()->where(['status' => Seed::STATUS_USED])->count() ?></h4>
                    <p class="text-muted mb-0">Zużytych</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Ulepszone style dla 5 kolumn */
.seed-card {
    transition: all 0.2s ease;
    border: 1px solid #dee2e6;
}

.seed-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #198754;
}

.seed-card .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 0.5rem;
}

.seed-card .card-body {
    font-size: 0.875rem;
}

.seed-card .card-footer {
    border-top: 1px solid #dee2e6;
    padding: 0.5rem;
}

.seed-card .btn-group .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.priority-badge {
    font-size: 0.65rem;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-weight: 600;
    color: white;
}

.priority-high { background-color: #dc3545; }
.priority-medium { background-color: #fd7e14; }
.priority-low { background-color: #198754; }
.priority-none { background-color: #6c757d; }

/* Responsive dla 5 kolumn */
@media (max-width: 1400px) {
    .col-xl-2 { flex: 0 0 20%; max-width: 20%; } /* 5 kolumn na XL */
}

@media (max-width: 1200px) {
    .col-lg-3 { flex: 0 0 25%; max-width: 25%; } /* 4 kolumny na LG */
}

@media (max-width: 768px) {
    .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; } /* 3 kolumny na MD */
}

@media (max-width: 576px) {
    .col-sm-6 { flex: 0 0 50%; max-width: 50%; } /* 2 kolumny na SM */
}

/* Mniejsze marginesy */
.row.g-2 > * {
    padding-right: 0.5rem;
    padding-left: 0.5rem;
}

.row.g-2 {
    margin-right: -0.5rem;
    margin-left: -0.5rem;
}
</style>