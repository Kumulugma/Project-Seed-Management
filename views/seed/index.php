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

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtry i wyszukiwanie
            </h5>
        </div>
        <div class="card-body">
            <?= Html::beginForm(['index'], 'get', ['class' => 'row g-3', 'id' => 'search-form']) ?>

            <div class="col-lg-4 col-md-6">
                <label class="form-label">Szukaj</label>
                <?=
                Html::textInput('search', Yii::$app->request->get('search'), [
                    'class' => 'form-control',
                    'placeholder' => 'Nazwa lub opis nasion...',
                    'id' => 'search-input'
                ])
                ?>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Typ</label>
                <?=
                Html::dropDownList('type', Yii::$app->request->get('type'),
                        array_merge(['' => 'Wszystkie typy'], $searchModel->getTypeOptions()),
                        ['class' => 'form-select']
                )
                ?>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Status</label>
<?=
Html::dropDownList('status', Yii::$app->request->get('status'),
        array_merge(['' => 'Wszystkie statusy'], $searchModel->getStatusOptions()),
        ['class' => 'form-select']
)
?>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
            <?=
            Html::submitButton('<i class="bi bi-search me-1"></i>Szukaj', [
                'class' => 'btn btn-primary',
                'encode' => false
            ])
            ?>
                </div>
            </div>

            <?php if (Yii::$app->request->get('search') || Yii::$app->request->get('type') || Yii::$app->request->get('status')): ?>
                <div class="col-12">
    <?=
    Html::a('<i class="bi bi-x-circle me-1"></i>Wyczyść filtry', ['index'], [
        'class' => 'btn btn-outline-secondary',
        'encode' => false
    ])
    ?>
                </div>
                <?php endif; ?>

<?= Html::endForm() ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
                                    <?php if ($dataProvider->totalCount > 0): ?>
                <div class="row g-3" id="seeds-grid">
    <?php foreach ($dataProvider->models as $model): ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card h-100 seed-card">
                                <div class="card-header d-flex justify-content-between align-items-center p-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?= $model->type === 'vegetables' ? 'success' : ($model->type === 'flowers' ? 'primary' : 'info') ?> me-2">
                                <?= $model->getTypeLabel() ?>
                                        </span>
                                        <span class="priority-badge priority-<?= $model->priority >= 8 ? 'high' : ($model->priority >= 5 ? 'medium' : ($model->priority > 0 ? 'low' : 'none')) ?> small">
                                    <?= $model->priority ?>
                                        </span>
                                    </div>
                                    <span class="badge bg-<?= $model->status === 'available' ? 'success' : 'secondary' ?>">
                                <?= $model->getStatusLabel() ?>
                                    </span>
                                </div>

                                <?php if ($model->image_path): ?>
                                    <div class="card-img-container" style="height: 150px; overflow: hidden;">
            <?=
            Html::img($model->getImageUrl(), [
                'class' => 'card-img-top object-fit-cover',
                'style' => 'height: 100%; width: 100%;'
            ])
            ?>
                                    </div>
                                        <?php else: ?>
                                    <div class="card-img-container bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
        <?php endif; ?>

                                <div class="card-body p-3">
                                    <h6 class="card-title fw-bold mb-2"><?= Html::encode($model->name) ?></h6>

        <?php if ($model->description): ?>
                                        <p class="card-text text-muted small mb-2">
            <?= Html::encode(substr($model->description, 0, 80)) ?>
            <?= strlen($model->description) > 80 ? '...' : '' ?>
                                        </p>
                                                <?php endif; ?>

                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <small class="text-muted">Wysokość:</small><br>
                                            <span class="badge bg-<?= $model->height === 'high' ? 'warning' : 'secondary' ?> small">
        <?= $model->getHeightLabel() ?>
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Typ rośliny:</small><br>
                                            <span class="badge bg-<?= $model->plant_type === 'perennial' ? 'success' : 'warning' ?> small">
                                        <?= $model->getPlantTypeLabel() ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted">Okres wysiewu:</small><br>
                                        <span class="small">
<?= $model->getFormattedSowingDate('sowing_start') ?> - 
<?= $model->getFormattedSowingDate('sowing_end') ?>
</span>
                                        <?php if ($model->isInSowingPeriod()): ?>
                                            <span class="badge bg-success ms-1 small">Aktualny okres</span>
                                        <?php endif; ?>
                                    </div>

                                        <?php if ($model->expiry_date): ?>
                                        <div class="mb-3">
                                            <small class="text-muted">Data ważności:</small><br>
                                            <?php
                                            $today = new DateTime();
                                            $expiry = new DateTime($model->expiry_date);
                                            $interval = $today->diff($expiry);
                                            $daysLeft = $interval->invert ? -$interval->days : $interval->days;
                                            ?>
                                            <span class="small"><?= date('d.m.Y', strtotime($model->expiry_date)) ?></span>
                                            <?php if ($daysLeft < 0): ?>
                                                <span class="badge bg-danger ms-1 small">Wygasło</span>
                                            <?php elseif ($daysLeft <= 30): ?>
                                                <span class="badge bg-warning ms-1 small"><?= $daysLeft ?> dni</span>
                                            <?php elseif ($daysLeft <= 90): ?>
                                                <span class="badge bg-info ms-1 small"><?= $daysLeft ?> dni</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                </div>

                                <div class="card-footer bg-light p-2">
                                    <div class="btn-group w-100" role="group">
                                        <?=
                                        Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                                            'class' => 'btn btn-outline-info btn-sm',
                                            'title' => 'Zobacz',
                                            'encode' => false
                                        ])
                                        ?>
                                        <?=
                                        Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                                            'class' => 'btn btn-outline-primary btn-sm',
                                            'title' => 'Edytuj',
                                            'encode' => false
                                        ])
                                        ?>
                        <?=
                        Html::a('<i class="bi bi-copy"></i>', ['copy', 'id' => $model->id], [
                            'class' => 'btn btn-outline-warning btn-sm',
                            'title' => 'Kopiuj',
                            'encode' => false
                        ])
                        ?>
                        <?=
                        Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-outline-danger btn-sm',
                            'title' => 'Usuń',
                            'data-confirm' => 'Czy na pewno chcesz usunąć te nasiona?',
                            'data-method' => 'post',
                            'encode' => false
                        ])
                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <?=
                    LinkPager::widget([
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
                    ]);
                    ?>
                </div>
<?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Brak nasion</h4>
                    <p class="text-muted">Nie znaleziono nasion spełniających kryteria wyszukiwania.</p>
    <?=
    Html::a('<i class="bi bi-plus-circle me-2"></i>Dodaj pierwsze nasiona', ['create'], [
        'class' => 'btn btn-success btn-lg',
        'encode' => false
    ])
    ?>
                </div>
<?php endif; ?>
        </div>
    </div>

    <div class="row mt-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="bi bi-collection text-primary display-6"></i>
                    <h4 class="text-primary mt-2"><?= $dataProvider->totalCount ?></h4>
                    <p class="text-muted mb-0">Łącznie nasion</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="bi bi-check-circle text-success display-6"></i>
                    <h4 class="text-success mt-2"><?= Seed::find()->where(['status' => 'available'])->count() ?></h4>
                    <p class="text-muted mb-0">Dostępnych</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-calendar-plus text-info display-6"></i>
                    <h4 class="text-info mt-2"><?= count(Seed::getSowingSeeds()) ?></h4>
                    <p class="text-muted mb-0">Do wysiewu teraz</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle text-warning display-6"></i>
                    <h4 class="text-warning mt-2">
<?= Seed::find()->where(['<=', 'expiry_date', date('Y-m-d', strtotime('+3 months'))])->andWhere(['!=', 'expiry_date', null])->count() ?>
                    </h4>
                    <p class="text-muted mb-0">Wygasa w 3 mies.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-submit search form on filter change
        const filterSelects = document.querySelectorAll('#search-form select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function () {
                document.getElementById('search-form').submit();
            });
        });

        // Real-time search with debounce
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 2 || this.value.length === 0) {
                        document.getElementById('search-form').submit();
                    }
                }, 500);
            });
        }

        // Card hover effects
        const seedCards = document.querySelectorAll('.seed-card');
        seedCards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
            });
        });
    });
</script>

<style>
    .seed-card {
        transition: all 0.3s ease;
    }

    .seed-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .object-fit-cover {
        object-fit: cover;
    }

    .btn-group .btn {
        flex: 1;
    }
</style>