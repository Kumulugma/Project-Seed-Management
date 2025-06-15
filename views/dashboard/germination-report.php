<?php

use yii\helpers\Html;

$this->title = 'Raport kiełkowania';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/dashboard/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="germination-report">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 text-primary fw-bold">
                <i class="bi bi-graph-up me-2"></i><?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                <?= Html::a('<i class="bi bi-house me-1"></i>Dashboard', ['/dashboard/index'], [
                    'class' => 'btn btn-primary',
                    'encode' => false
                ]) ?>
                <?= Html::a('<i class="bi bi-calendar3 me-1"></i>Kalendarz', ['/dashboard/sowing-calendar'], [
                    'class' => 'btn btn-info',
                    'encode' => false
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Main Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-primary bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-collection text-primary display-6 mb-2"></i>
                    <h3 class="text-primary fw-bold"><?= $stats['total'] ?></h3>
                    <p class="text-muted mb-0">Łącznie wysianych</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-success bg-success bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success display-6 mb-2"></i>
                    <h3 class="text-success fw-bold"><?= $stats['germinated'] ?></h3>
                    <p class="text-muted mb-0">Wykiełkowało</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-danger bg-danger bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle text-danger display-6 mb-2"></i>
                    <h3 class="text-danger fw-bold"><?= $stats['not_germinated'] ?></h3>
                    <p class="text-muted mb-0">Nie wykiełkowało</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-warning bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split text-warning display-6 mb-2"></i>
                    <h3 class="text-warning fw-bold"><?= $stats['sown'] ?></h3>
                    <p class="text-muted mb-0">Oczekuje</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Germination Rate -->
    <?php if (($stats['germinated'] + $stats['not_germinated']) > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Ogólny wskaźnik kiełkowania
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php 
                    $rate = $stats['germination_rate'];
                    $color = $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger');
                    $colorText = $rate >= 60 ? ($color === 'warning' ? 'text-dark' : '') : '';
                    ?>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="text-<?= $color ?> <?= $colorText ?>">
                                <?= $rate ?>% 
                                <?php if ($rate >= 80): ?>
                                    <i class="bi bi-emoji-smile"></i>
                                <?php elseif ($rate >= 60): ?>
                                    <i class="bi bi-emoji-neutral"></i>
                                <?php else: ?>
                                    <i class="bi bi-emoji-frown"></i>
                                <?php endif; ?>
                            </h2>
                            <p class="text-muted mb-0">
                                Na podstawie <?= $stats['germinated'] + $stats['not_germinated'] ?> 
                                nasion z określonym statusem
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-<?= $color ?>" 
                                     style="width: <?= $rate ?>%"
                                     role="progressbar">
                                    <span class="fw-bold <?= $color === 'warning' ? 'text-dark' : '' ?>"><?= $rate ?>%</span>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <?php if ($rate >= 80): ?>
                                    Doskonały wynik! 🎉
                                <?php elseif ($rate >= 60): ?>
                                    Dobry wynik, można poprawić 📈
                                <?php else: ?>
                                    Wynik wymaga uwagi 🔍
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics by Type -->
    <?php if (!empty($typeStats)): ?>
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-bar-chart me-2"></i>Statystyki według typu roślin
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php foreach ($typeStats as $type => $data): ?>
                    <?php 
                    $typeColor = $type === 'Warzywa' ? 'success' : ($type === 'Kwiaty' ? 'primary' : 'info');
                    $rateColor = $data['rate'] >= 70 ? 'success' : ($data['rate'] >= 50 ? 'warning' : 'danger');
                    $rateColorText = $rateColor === 'warning' ? 'text-dark' : '';
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-<?= $typeColor ?> h-100">
                            <div class="card-header bg-<?= $typeColor ?> text-white text-center">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-<?= $type === 'Warzywa' ? 'carrot' : ($type === 'Kwiaty' ? 'flower1' : 'leaf') ?> me-2"></i>
                                    <?= $type ?>
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <h4 class="text-<?= $rateColor ?> <?= $rateColorText ?>"><?= $data['rate'] ?>%</h4>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $rateColor ?>" 
                                         style="width: <?= $data['rate'] ?>%"></div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <small class="text-muted">Łącznie</small>
                                        <div class="fw-bold"><?= $data['total'] ?></div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-success">Kiełk.</small>
                                        <div class="fw-bold text-success"><?= $data['germinated'] ?></div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-danger">Nie</small>
                                        <div class="fw-bold text-danger"><?= $data['not_germinated'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Detailed History -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Historia wysiewów
                </h5>
                <span class="badge bg-light text-dark"><?= count($sownSeeds) ?> rekordów</span>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($sownSeeds)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30px;">#</th>
                                <th>Nazwa nasiona</th>
                                <th style="width: 100px;">Typ</th>
                                <th style="width: 120px;">Data wysiewu</th>
                                <th style="width: 100px;">Dni</th>
                                <th style="width: 100px;">Kod</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 200px;">Notatki</th>
                                <th style="width: 100px;">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($sownSeeds, 0, 50) as $index => $sownSeed): ?>
                                <?php 
                                $statusColor = $sownSeed->getStatusColor();
                                $statusIcon = '';
                                switch($sownSeed->status) {
                                    case 'germinated': $statusIcon = '✅'; break;
                                    case 'not_germinated': $statusIcon = '❌'; break;
                                    default: $statusIcon = '⏳';
                                }
                                
                                $daysFromSowing = $sownSeed->getDaysFromSowing();
                                $shouldCheck = $sownSeed->shouldBeGerminated(7);
                                ?>
                                <tr class="<?= $shouldCheck && $sownSeed->status === 'sown' ? 'table-warning' : '' ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="fw-bold"><?= Html::encode($sownSeed->seed->name) ?></div>
                                        <small class="text-muted"><?= $sownSeed->seed->getTypeLabel() ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $sownSeed->seed->type === 'vegetables' ? 'success' : ($sownSeed->seed->type === 'flowers' ? 'primary' : 'info') ?>">
                                            <?= substr($sownSeed->seed->getTypeLabel(), 0, 4) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div><?= date('d.m.Y', strtotime($sownSeed->sown_date)) ?></div>
                                        <small class="text-muted"><?= date('l', strtotime($sownSeed->sown_date)) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $daysFromSowing > 14 ? 'info' : ($daysFromSowing > 7 ? 'warning' : 'secondary') ?>">
                                            <?= $daysFromSowing ?>
                                        </span>
                                        <?php if ($shouldCheck && $sownSeed->status === 'sown'): ?>
                                            <br><small class="text-warning">Sprawdź!</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="small"><?= Html::encode($sownSeed->sowing_code) ?></code>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $statusColor ?>">
                                            <?= $statusIcon ?> <?= substr($sownSeed->getStatusLabel(), 0, 8) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($sownSeed->notes): ?>
                                            <span class="text-truncate d-block" style="max-width: 150px;" title="<?= Html::encode($sownSeed->notes) ?>">
                                                <?= Html::encode(substr($sownSeed->notes, 0, 30)) ?>
                                                <?= strlen($sownSeed->notes) > 30 ? '...' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <small class="text-muted">Brak</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?= Html::a('<i class="bi bi-eye"></i>', ['/seed/view', 'id' => $sownSeed->seed->id], [
                                                'class' => 'btn btn-outline-info',
                                                'title' => 'Zobacz nasiono',
                                                'target' => '_blank',
                                                'encode' => false
                                            ]) ?>
                                            <?= Html::a('<i class="bi bi-pencil"></i>', ['/dashboard/add-note', 'id' => $sownSeed->id], [
                                                'class' => 'btn btn-outline-warning',
                                                'title' => 'Notatka',
                                                'encode' => false
                                            ]) ?>
                                            <?= Html::a('<i class="bi bi-trash"></i>', ['/dashboard/delete-sown', 'id' => $sownSeed->id], [
                                                'class' => 'btn btn-outline-danger',
                                                'title' => 'Usuń',
                                                'data-confirm' => 'Czy na pewno usunąć ten zapis?',
                                                'encode' => false
                                            ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (count($sownSeeds) > 50): ?>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Wyświetlono pierwszych 50 rekordów z <?= count($sownSeeds) ?>. 
                        Użyj filtrów lub wyszukiwarki, aby zawęzić wyniki.
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-seed text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Brak danych o wysiewach</h4>
                    <p class="text-muted">Nie ma jeszcze żadnych zapisów wysiewów nasion.</p>
                    <?= Html::a('<i class="bi bi-speedometer2 me-2"></i>Przejdź do dashboardu', ['/dashboard/index'], [
                        'class' => 'btn btn-primary btn-lg',
                        'encode' => false
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Szybkie akcje
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <?= Html::a(
                                '<i class="bi bi-speedometer2 me-2"></i>Dashboard', 
                                ['/dashboard/index'], 
                                ['class' => 'btn btn-primary w-100', 'encode' => false]
                            ) ?>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <?= Html::a(
                                '<i class="bi bi-calendar3 me-2"></i>Kalendarz wysiewów', 
                                ['/dashboard/sowing-calendar'], 
                                ['class' => 'btn btn-info w-100', 'encode' => false]
                            ) ?>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <?= Html::a(
                                '<i class="bi bi-graph-up me-2"></i>Statystyki nasion', 
                                ['/seed/stats'], 
                                ['class' => 'btn btn-warning w-100 text-dark', 'encode' => false]
                            ) ?>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <?= Html::a(
                                '<i class="bi bi-plus-circle me-2"></i>Dodaj nasiona', 
                                ['/seed/create'], 
                                ['class' => 'btn btn-success w-100', 'encode' => false]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>