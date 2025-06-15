<?php

use yii\helpers\Html;

$this->title = 'Kalendarz wysiewów ' . $year;
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/dashboard/index']];
$this->params['breadcrumbs'][] = $this->title;

$monthNames = [
    1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
    5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
    9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
];

$monthIcons = [
    1 => 'bi-snow', 2 => 'bi-snow', 3 => 'bi-flower1', 4 => 'bi-flower2',
    5 => 'bi-sun', 6 => 'bi-brightness-high', 7 => 'bi-brightness-high', 8 => 'bi-sun',
    9 => 'bi-tree', 10 => 'bi-tree', 11 => 'bi-cloud', 12 => 'bi-snow'
];

$currentMonth = (int)date('n');
$currentYear = (int)date('Y');
$isCurrentYear = ($year == $currentYear);
?>

<div class="sowing-calendar">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 text-primary fw-bold">
                <i class="bi bi-calendar3 me-2"></i><?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                <?= Html::a('<i class="bi bi-chevron-left"></i> ' . ($year - 1), ['sowing-calendar', 'year' => $year - 1], [
                    'class' => 'btn btn-outline-primary',
                    'encode' => false
                ]) ?>
                <?= Html::a('<i class="bi bi-house"></i>', ['/dashboard/index'], [
                    'class' => 'btn btn-primary',
                    'encode' => false,
                    'title' => 'Dashboard'
                ]) ?>
                <?= Html::a(($year + 1) . ' <i class="bi bi-chevron-right"></i>', ['sowing-calendar', 'year' => $year + 1], [
                    'class' => 'btn btn-outline-primary',
                    'encode' => false
                ]) ?>
            </div>
        </div>
    </div>

    <div class="alert alert-info mb-4">
        <div class="d-flex">
            <i class="bi bi-info-circle me-3 fs-5"></i>
            <div>
                <h6 class="mb-1">Jak czytać kalendarz</h6>
                <p class="mb-0">
                    <span class="badge bg-success me-2">Aktualny miesiąc</span>
                    <span class="badge bg-secondary me-2">Przeszłe miesiące</span>
                    <span class="badge bg-primary me-2">Przyszłe miesiące</span>
                    <br><small class="text-muted mt-1">Wszystkie nasiona są wyświetlane w kolumnach dla lepszej organizacji.</small>
                </p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($calendar as $month => $seeds): ?>
            <?php 
            $isPast = $isCurrentYear && $month < $currentMonth;
            $isCurrent = $isCurrentYear && $month == $currentMonth;
            $isFuture = $isCurrentYear && $month > $currentMonth;
            
            // Określ klasy CSS dla miesięcy
            if ($isPast) {
                $monthClass = 'border-secondary bg-light';
                $headerClass = 'bg-secondary text-white';
                $badgeClass = 'bg-dark text-white';
            } elseif ($isCurrent) {
                $monthClass = 'border-success shadow';
                $headerClass = 'bg-success text-white';
                $badgeClass = 'bg-white text-dark';
            } else {
                $monthClass = count($seeds) > 0 ? 'border-primary' : 'border-light';
                $headerClass = count($seeds) > 0 ? 'bg-primary text-white' : 'bg-light';
                $badgeClass = count($seeds) > 0 ? 'bg-white text-dark' : 'bg-secondary';
            }
            ?>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 <?= $monthClass ?>">
                    <div class="card-header <?= $headerClass ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="<?= $monthIcons[$month] ?> me-2"></i>
                                <?= $monthNames[$month] ?>
                                <?php if ($isCurrent): ?>
                                    <span class="badge bg-warning text-dark ms-2 small">TERAZ</span>
                                <?php endif; ?>
                            </h6>
                            <span class="badge <?= $badgeClass ?>">
                                <?= count($seeds) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body p-2 <?= $isPast ? 'opacity-75' : '' ?>">
                        <?php if (!empty($seeds)): ?>
                            <div class="seeds-grid" style="max-height: 400px; overflow-y: auto;">
                                <div class="row g-1">
                                    <?php foreach ($seeds as $index => $seed): ?>
                                        <?php 
                                        $isInPeriod = $seed->isInSowingPeriod($year . '-' . sprintf('%02d', $month) . '-15');
                                        $itemClass = $isInPeriod ? 'bg-success bg-opacity-10 border border-success' : 'bg-light';
                                        
                                        // Dodatkowe przyciemnienie dla przeszłych miesięcy
                                        if ($isPast) {
                                            $itemClass .= ' opacity-75';
                                        }
                                        ?>
                                        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                                            <div class="seed-item-card mb-1 p-2 rounded <?= $itemClass ?>" style="min-height: 120px;">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div class="flex-grow-1" style="min-width: 0;">
                                                        <?= Html::a(
                                                            Html::encode($seed->name),
                                                            ['/seed/view', 'id' => $seed->id],
                                                            [
                                                                'class' => 'text-decoration-none fw-bold',
                                                                'style' => 'font-size: 0.8rem; line-height: 1.2; display: block;',
                                                                'target' => '_blank',
                                                                'title' => $seed->name
                                                            ]
                                                        ) ?>
                                                    </div>
                                                    <div class="priority-indicator ms-1">
                                                        <span class="priority-badge priority-<?= $seed->priority >= 8 ? 'high' : ($seed->priority >= 5 ? 'medium' : ($seed->priority > 0 ? 'low' : 'none')) ?>" style="font-size: 0.7rem; min-width: 20px; height: 20px; line-height: 20px;">
                                                            <?= $seed->priority ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-1">
                                                    <span class="badge bg-<?= 
                                                        $seed->type === 'vegetables' ? 'success' : 
                                                        ($seed->type === 'flowers' ? 'primary' : 'info') 
                                                    ?>" style="font-size: 0.65rem;">
                                                        <?= substr($seed->getTypeLabel(), 0, 7) ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="mb-1">
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        <?= date('d.m', strtotime($seed->sowing_start)) ?> - 
                                                        <?= date('d.m', strtotime($seed->sowing_end)) ?>
                                                    </small>
                                                </div>
                                                
                                                <?php if ($seed->description && strlen($seed->description) > 0): ?>
                                                    <div class="mt-1">
                                                        <small class="text-muted" style="font-size: 0.65rem; line-height: 1.2;">
                                                            <?= Html::encode(substr($seed->description, 0, 40)) ?>
                                                            <?= strlen($seed->description) > 40 ? '...' : '' ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($seed->expiry_date): ?>
                                                    <?php 
                                                    $today = new DateTime();
                                                    $expiry = new DateTime($seed->expiry_date);
                                                    $interval = $today->diff($expiry);
                                                    $daysLeft = $interval->invert ? -$interval->days : $interval->days;
                                                    ?>
                                                    <?php if ($daysLeft <= 90): ?>
                                                        <div class="mt-1">
                                                            <span class="badge bg-<?= $daysLeft < 0 ? 'danger' : ($daysLeft <= 30 ? 'warning' : 'info') ?>" style="font-size: 0.6rem;">
                                                                <?php if ($daysLeft < 0): ?>
                                                                    Wygasło
                                                                <?php else: ?>
                                                                    <?= $daysLeft ?>d
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-flower1 display-1 opacity-25"></i>
                                <p class="mt-2 mb-0 small">Brak nasion do wysiewu</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (count($seeds) > 0): ?>
                        <div class="card-footer bg-light py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <?php 
                                    $highPriority = count(array_filter($seeds, function($s) { return $s->priority >= 8; }));
                                    $mediumPriority = count(array_filter($seeds, function($s) { return $s->priority >= 5 && $s->priority < 8; }));
                                    $expiringSoon = count(array_filter($seeds, function($s) { 
                                        if (!$s->expiry_date) return false;
                                        $today = new DateTime();
                                        $expiry = new DateTime($s->expiry_date);
                                        $interval = $today->diff($expiry);
                                        $daysLeft = $interval->invert ? -$interval->days : $interval->days;
                                        return $daysLeft <= 90;
                                    }));
                                    ?>
                                    <?php if ($highPriority > 0): ?>
                                        <span class="badge bg-danger small me-1"><?= $highPriority ?> pilne</span>
                                    <?php endif; ?>
                                    <?php if ($mediumPriority > 0): ?>
                                        <span class="badge bg-warning small me-1"><?= $mediumPriority ?> średnie</span>
                                    <?php endif; ?>
                                    <?php if ($expiringSoon > 0): ?>
                                        <span class="badge bg-info small"><?= $expiringSoon ?> wygasa</span>
                                    <?php endif; ?>
                                </small>
                                
                                <small class="text-muted">
                                    Razem: <?= count($seeds) ?>
                                </small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Legend and Tools -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Legenda
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6>Priorytety:</h6>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="priority-badge priority-high me-2">8-10</span>
                                    <small>Bardzo wysokie</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="priority-badge priority-medium me-2">5-7</span>
                                    <small>Średnie</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="priority-badge priority-low me-2">1-4</span>
                                    <small>Niskie</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Typy:</h6>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-success">Warzywa</span>
                                <span class="badge bg-primary">Kwiaty</span>
                                <span class="badge bg-info">Zioła</span>
                            </div>
                            
                            <div class="alert alert-success py-2 mb-0">
                                <small>
                                    <i class="bi bi-lightbulb me-1"></i>
                                    Zielone tło = aktualny okres wysiewu
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-tools me-2"></i>Narzędzia
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= Html::a(
                            '<i class="bi bi-plus-circle me-2"></i>Dodaj nasiona', 
                            ['/seed/create'], 
                            ['class' => 'btn btn-success', 'encode' => false]
                        ) ?>
                        <?= Html::a(
                            '<i class="bi bi-speedometer2 me-2"></i>Dashboard', 
                            ['/dashboard/index'], 
                            ['class' => 'btn btn-primary', 'encode' => false]
                        ) ?>
                        <?= Html::a(
                            '<i class="bi bi-graph-up me-2"></i>Raport kiełkowania', 
                            ['/dashboard/germination-report'], 
                            ['class' => 'btn btn-info', 'encode' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add smooth scrolling to current month on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentMonthCard = document.querySelector('.card.border-success.shadow');
    if (currentMonthCard) {
        setTimeout(() => {
            currentMonthCard.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }, 500);
    }
});
</script>

<style>
.opacity-75 {
    opacity: 0.75 !important;
}

.card.border-success.shadow {
    animation: pulse-success 2s ease-in-out;
}

@keyframes pulse-success {
    0%, 100% { 
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); 
    }
    50% { 
        box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); 
    }
}

.seeds-grid::-webkit-scrollbar {
    width: 4px;
}

.seeds-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.seeds-grid::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.seeds-grid::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.seed-item-card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.seed-item-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .seed-item-card {
        min-height: 100px !important;
    }
    
    .seeds-grid {
        max-height: 300px !important;
    }
}

@media (max-width: 576px) {
    .seed-item-card {
        min-height: 80px !important;
    }
}
</style>