<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;
$formatter = new IntlDateFormatter('pl_PL', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$formatter->setPattern('EEEE, d MMMM yyyy');
?>

<div class="dashboard-index">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1 class="h3 text-success fw-bold mb-1">
                <i class="bi bi-speedometer2 me-2"></i><?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted small mb-0"><?= $formatter->format(new DateTime()) ?></p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group btn-group-sm" role="group">
                <?= Html::a('<i class="bi bi-calendar3 me-1"></i>Kalendarz', ['/dashboard/sowing-calendar'], [
                    'class' => 'btn btn-outline-info btn-sm',
                    'encode' => false
                ]) ?>
                <?= Html::a('<i class="bi bi-graph-up me-1"></i>Raporty', ['/dashboard/germination-report'], [
                    'class' => 'btn btn-outline-warning btn-sm',
                    'encode' => false
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Compact Stats Row -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-primary bg-opacity-10 text-center py-2">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-collection text-primary me-2 fs-5"></i>
                        <div>
                            <h4 class="text-primary fw-bold mb-0" data-stat="total_seeds"><?= $stats['total_seeds'] ?></h4>
                            <small class="text-muted">Łącznie</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-success bg-opacity-10 text-center py-2">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-check-circle text-success me-2 fs-5"></i>
                        <div>
                            <h4 class="text-success fw-bold mb-0" data-stat="available_seeds"><?= $stats['available_seeds'] ?></h4>
                            <small class="text-muted">Dostępne</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-info bg-opacity-10 text-center py-2">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-calendar-plus text-info me-2 fs-5"></i>
                        <div>
                            <h4 class="text-info fw-bold mb-0" data-stat="sown_today"><?= $stats['sown_today'] ?></h4>
                            <small class="text-muted">Dzisiaj</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-warning bg-opacity-10 text-center py-2">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-hourglass-split text-warning me-2 fs-5"></i>
                        <div>
                            <h4 class="text-warning fw-bold mb-0" data-stat="pending_germination"><?= $stats['pending_germination'] ?></h4>
                            <small class="text-muted">Kiełkuje</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-3 mb-3">
        <!-- Search and Quick Actions -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-search me-2"></i>Wyszukiwarka nasion
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="position-relative mb-3">
                        <input type="text" id="seed-search" class="form-control" 
                               placeholder="Wpisz nazwę lub opis (min. 2 znaki)...">
                        <div id="search-results" class="search-results"></div>
                    </div>
                    
                    <h6 class="mb-2">Szybkie akcje:</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <?= Html::a('<i class="bi bi-plus-circle me-1"></i>Dodaj', ['/seed/create'], [
                                'class' => 'btn btn-success btn-sm w-100',
                                'encode' => false
                            ]) ?>
                        </div>
                        <div class="col-6">
                            <?= Html::a('<i class="bi bi-list-ul me-1"></i>Lista', ['/seed/index'], [
                                'class' => 'btn btn-primary btn-sm w-100',
                                'encode' => false
                            ]) ?>
                        </div>
                        <div class="col-6">
                            <?= Html::a('<i class="bi bi-graph-up me-1"></i>Stats', ['/seed/stats'], [
                                'class' => 'btn btn-info btn-sm w-100',
                                'encode' => false
                            ]) ?>
                        </div>
                        <div class="col-6">
                            <?= Html::a('<i class="bi bi-download me-1"></i>CSV', ['/seed/export'], [
                                'class' => 'btn btn-outline-secondary btn-sm w-100',
                                'encode' => false
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Expiring Seeds -->
        <div class="col-lg-6">
            <?php if (!empty($expiringSoon)): ?>
            <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Wygasają wkrótce (<?= count($expiringSoon) ?>)
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php foreach (array_slice($expiringSoon, 0, 5) as $seed): ?>
                                    <?php 
                                    $today = new DateTime();
                                    $expiry = new DateTime($seed->expiry_date);
                                    $interval = $today->diff($expiry);
                                    $daysLeft = $interval->invert ? -$interval->days : $interval->days;
                                    ?>
                                    <tr>
                                        <td class="align-middle">
                                            <strong class="small"><?= Html::encode($seed->name) ?></strong><br>
                                            <small class="text-muted"><?= date('d.m.Y', strtotime($seed->expiry_date)) ?></small>
                                        </td>
                                        <td class="align-middle text-end">
                                            <?php if ($daysLeft < 0): ?>
                                                <span class="badge bg-danger small">Wygasło</span>
                                            <?php elseif ($daysLeft <= 30): ?>
                                                <span class="badge bg-warning small"><?= $daysLeft ?>d</span>
                                            <?php else: ?>
                                                <span class="badge bg-info small"><?= $daysLeft ?>d</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($expiringSoon) > 5): ?>
                        <div class="text-center mt-2">
                            <small class="text-muted">... i <?= count($expiringSoon) - 5 ?> więcej</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-check-circle me-2"></i>Status nasion
                    </h6>
                </div>
                <div class="card-body p-3 text-center">
                    <i class="bi bi-emoji-smile text-success" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Wszystkie nasiona mają aktualne daty ważności!</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Seeds Content Row -->
    <div class="row g-3">
        <!-- Seeds to Sow -->
        <div class="col-lg-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-seed me-2"></i>Do wysiewu (<?= count($currentSeeds) ?>)
                        </h6>
                        <small>Obecny okres</small>
                    </div>
                </div>
                <div class="card-body p-3">
                    <?php if (!empty($currentSeeds)): ?>
                        <?php $form = ActiveForm::begin([
                            'action' => ['/dashboard/sowing-pdf'], 
                            'method' => 'post',
                            'options' => ['data-ajax' => 'true']
                        ]); ?>
                        
                        <div class="table-responsive mb-2" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 30px;">
                                            <input type="checkbox" class="form-check-input select-all" data-target=".seed-checkbox">
                                        </th>
                                        <th>Nazwa</th>
                                        <th style="width: 60px;">Typ</th>
                                        <th style="width: 40px;">P</th>
                                    </tr>
                                </thead>
                                <tbody id="current-seeds-table">
                                    <?php foreach ($currentSeeds as $seed): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="seeds[]" value="<?= $seed->id ?>" 
                                                       class="form-check-input seed-checkbox">
                                            </td>
                                            <td>
                                                <div class="fw-bold small"><?= Html::encode($seed->name) ?></div>
                                                <small class="text-muted">
                                                    <?= date('d.m', strtotime($seed->sowing_start)) ?> - <?= date('d.m', strtotime($seed->sowing_end)) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $seed->type === 'vegetables' ? 'success' : ($seed->type === 'flowers' ? 'primary' : 'info') ?> small">
                                                    <?= substr($seed->getTypeLabel(), 0, 3) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="priority-badge priority-<?= $seed->priority >= 8 ? 'high' : ($seed->priority >= 5 ? 'medium' : ($seed->priority > 0 ? 'low' : 'none')) ?> small">
                                                    <?= $seed->priority ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex gap-1">
                            <?= Html::submitButton('<i class="bi bi-file-pdf me-1"></i>PDF + Dodaj', [
                                'class' => 'btn btn-success btn-sm',
                                'data-original-text' => 'PDF + Dodaj do wysiewu',
                                'encode' => false
                            ]) ?>
                            <?= Html::button('<i class="bi bi-arrow-clockwise"></i>', [
                                'class' => 'btn btn-outline-secondary btn-sm', 
                                'onclick' => 'location.reload()',
                                'title' => 'Odśwież',
                                'encode' => false
                            ]) ?>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                    <?php else: ?>
                        <div class="alert alert-info py-2 mb-0">
                            <h6 class="mb-1"><i class="bi bi-info-circle me-2"></i>Brak nasion</h6>
                            <p class="mb-2 small">W obecnym okresie (<?= date('d.m') ?>) nie ma nasion do wysiewu.</p>
                            <div class="d-flex gap-1">
                                <?= Html::a('<i class="bi bi-calendar3 me-1"></i>Kalendarz', ['/dashboard/sowing-calendar'], [
                                    'class' => 'btn btn-primary btn-sm',
                                    'encode' => false
                                ]) ?>
                                <?= Html::a('<i class="bi bi-plus-circle me-1"></i>Dodaj', ['/seed/create'], [
                                    'class' => 'btn btn-success btn-sm',
                                    'encode' => false
                                ]) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Check Germination -->
        <div class="col-lg-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-eye me-2"></i>Sprawdź kiełkowanie (<?= count($sownSeeds) ?>)
                        </h6>
                        <small>Ostatnio wysiałe</small>
                    </div>
                </div>
                <div class="card-body p-3">
                    <?php if (!empty($sownSeeds)): ?>
                        <?php $form = ActiveForm::begin([
                            'action' => ['/dashboard/update-germination'], 
                            'method' => 'post',
                            'options' => ['data-ajax' => 'true']
                        ]); ?>
                        
                        <div class="table-responsive mb-2" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Nazwa</th>
                                        <th style="width: 80px;">Status</th>
                                        <th style="width: 30px;">E</th>
                                    </tr>
                                </thead>
                                <tbody id="sown-seeds-table">
                                    <?php foreach ($sownSeeds as $sownSeed): ?>
                                        <?php 
                                        $daysFromSowing = $sownSeed->getDaysFromSowing();
                                        $shouldGerminate = $sownSeed->shouldBeGerminated(7);
                                        ?>
                                        <tr class="<?= $shouldGerminate ? 'table-warning' : '' ?>">
                                            <td>
                                                <div class="fw-bold small"><?= Html::encode($sownSeed->seed->name) ?></div>
                                                <small class="text-muted">
                                                    <?= date('d.m', strtotime($sownSeed->sown_date)) ?> 
                                                    (<?= $daysFromSowing ?>d)
                                                </small>
                                            </td>
                                            <td>
                                                <select name="germination[<?= $sownSeed->id ?>]" class="form-select form-select-sm">
                                                    <?php foreach ($sownSeed->getStatusOptions() as $value => $label): ?>
                                                        <option value="<?= $value ?>" <?= $value === $sownSeed->status ? 'selected' : '' ?>>
                                                            <?= substr($label, 0, 8) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="labels[]" value="<?= $sownSeed->id ?>" 
                                                       class="form-check-input" title="Etykieta">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex gap-1">
                            <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i>Status', [
                                'class' => 'btn btn-primary btn-sm',
                                'data-original-text' => 'Aktualizuj status',
                                'encode' => false
                            ]) ?>
                            <?= Html::button('<i class="bi bi-printer me-1"></i>Etykiety', [
                                'class' => 'btn btn-warning btn-sm', 
                                'id' => 'print-labels-btn',
                                'encode' => false
                            ]) ?>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                        
                        <?php $labelsForm = ActiveForm::begin([
                            'action' => ['/dashboard/print-labels'], 
                            'method' => 'post', 
                            'id' => 'labels-form'
                        ]); ?>
                        <div id="selected-labels"></div>
                        <?php ActiveForm::end(); ?>
                        
                    <?php else: ?>
                        <div class="alert alert-info py-2 mb-0">
                            <h6 class="mb-1"><i class="bi bi-info-circle me-2"></i>Brak do sprawdzenia</h6>
                            <p class="mb-2 small">Nie ma nasion oczekujących na sprawdzenie kiełkowania.</p>
                            <?= Html::a('<i class="bi bi-graph-up me-1"></i>Pełny raport', ['/dashboard/germination-report'], [
                                'class' => 'btn btn-primary btn-sm',
                                'encode' => false
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const labelCheckboxes = document.querySelectorAll('input[name="labels[]"]');
    labelCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedLabels);
    });

    function updateSelectedLabels() {
        const selected = Array.from(document.querySelectorAll('input[name="labels[]"]:checked'))
            .map(checkbox => '<input type="hidden" name="seeds[]" value="' + checkbox.value + '">')
            .join('');
        document.getElementById('selected-labels').innerHTML = selected;
    }

    document.getElementById('print-labels-btn')?.addEventListener('click', function() {
        if (document.querySelectorAll('input[name="labels[]"]:checked').length === 0) {
            alert('Wybierz nasiona do wydruku etykiet');
            return;
        }
        updateSelectedLabels();
        document.getElementById('labels-form').submit();
    });
});
</script>