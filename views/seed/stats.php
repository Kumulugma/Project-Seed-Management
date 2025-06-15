<?php

use yii\helpers\Html;

$this->title = 'Statystyki nasion';
$this->params['breadcrumbs'][] = ['label' => 'Nasiona', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seed-stats">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 text-primary fw-bold">
                <i class="bi bi-graph-up me-2"></i><?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Lista nasion', ['index'], [
                    'class' => 'btn btn-primary',
                    'encode' => false
                ]) ?>
                <?= Html::a('<i class="bi bi-download me-1"></i>Eksport CSV', ['export'], [
                    'class' => 'btn btn-success',
                    'encode' => false
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-primary bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-collection text-primary display-6 mb-2"></i>
                    <h3 class="text-primary fw-bold"><?= $stats['total'] ?></h3>
                    <p class="text-muted mb-0">Łącznie nasion</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-success bg-success bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success display-6 mb-2"></i>
                    <h3 class="text-success fw-bold"><?= $stats['available'] ?></h3>
                    <p class="text-muted mb-0">Dostępnych</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-secondary bg-secondary bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle text-secondary display-6 mb-2"></i>
                    <h3 class="text-secondary fw-bold"><?= $stats['used'] ?></h3>
                    <p class="text-muted mb-0">Zużytych</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-warning bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle text-warning display-6 mb-2"></i>
                    <h3 class="text-warning fw-bold"><?= count($stats['expiring_soon']) ?></h3>
                    <p class="text-muted mb-0">Wygasa wkrótce</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row g-4 mb-4">
        <!-- Statistics by Type -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Według typu
                    </h5>
                </div>
                <div class="card-body">
                    <?php $totalForPercent = $stats['total'] > 0 ? $stats['total'] : 1; ?>
                    <?php foreach ($stats['by_type'] as $type => $count): ?>
                        <?php 
                        $percentage = round(($count / $totalForPercent) * 100, 1);
                        $color = $type === 'Warzywa' ? 'success' : ($type === 'Kwiaty' ? 'primary' : 'info');
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold"><?= $type ?></span>
                                <span class="badge bg-<?= $color ?>"><?= $count ?> (<?= $percentage ?>%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?= $color ?>" 
                                     style="width: <?= $percentage ?>%"
                                     role="progressbar"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Statistics by Height -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Według wysokości
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($stats['by_height'] as $height => $count): ?>
                        <?php 
                        $percentage = round(($count / $totalForPercent) * 100, 1);
                        $color = $height === 'Wysokie' ? 'danger' : 'secondary';
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold"><?= $height ?></span>
                                <span class="badge bg-<?= $color ?>"><?= $count ?> (<?= $percentage ?>%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?= $color ?>" 
                                     style="width: <?= $percentage ?>%"
                                     role="progressbar"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Statistics by Plant Type -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-flower2 me-2"></i>Typ rośliny
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($stats['by_plant_type'] as $plantType => $count): ?>
                        <?php 
                        $percentage = round(($count / $totalForPercent) * 100, 1);
                        $color = $plantType === 'Bylina' ? 'success' : 'warning';
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold"><?= $plantType ?></span>
                                <span class="badge bg-<?= $color ?> <?= $color === 'warning' ? 'text-dark' : '' ?>"><?= $count ?> (<?= $percentage ?>%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?= $color ?>" 
                                     style="width: <?= $percentage ?>%"
                                     role="progressbar"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Seeds Table -->
    <?php if (!empty($stats['expiring_soon'])): ?>
    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>Nasiona wygasające w ciągu 6 miesięcy
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nazwa</th>
                            <th>Typ</th>
                            <th>Data ważności</th>
                            <th>Pozostało dni</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['expiring_soon'] as $seed): ?>
                            <?php 
                            $today = new DateTime();
                            $expiry = new DateTime($seed->expiry_date);
                            $interval = $today->diff($expiry);
                            $daysLeft = $interval->invert ? -$interval->days : $interval->days;
                            ?>
                            <tr class="<?= $daysLeft < 0 ? 'table-danger' : ($daysLeft <= 30 ? 'table-warning' : '') ?>">
                                <td>
                                    <div class="fw-bold"><?= Html::encode($seed->name) ?></div>
                                    <?php if ($seed->description): ?>
                                        <small class="text-muted"><?= Html::encode(substr($seed->description, 0, 50)) ?><?= strlen($seed->description) > 50 ? '...' : '' ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $seed->type === 'vegetables' ? 'success' : ($seed->type === 'flowers' ? 'primary' : 'info') ?>">
                                        <?= $seed->getTypeLabel() ?>
                                    </span>
                                </td>
                                <td><?= date('d.m.Y', strtotime($seed->expiry_date)) ?></td>
                                <td>
                                    <?php if ($daysLeft < 0): ?>
                                        <span class="badge bg-danger">Wygasło <?= abs($daysLeft) ?> dni temu</span>
                                    <?php elseif ($daysLeft == 0): ?>
                                        <span class="badge bg-danger">Wygasa dzisiaj!</span>
                                    <?php else: ?>
                                        <span class="badge bg-<?= $daysLeft <= 30 ? 'danger' : 'warning' ?> <?= $daysLeft > 30 ? 'text-dark' : '' ?>">
                                            <?= $daysLeft ?> dni
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $seed->status === 'available' ? 'success' : 'secondary' ?>">
                                        <?= $seed->getStatusLabel() ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?= Html::a('<i class="bi bi-eye"></i>', ['/seed/view', 'id' => $seed->id], [
                                            'class' => 'btn btn-outline-info',
                                            'title' => 'Zobacz',
                                            'encode' => false
                                        ]) ?>
                                        <?= Html::a('<i class="bi bi-pencil"></i>', ['/seed/update', 'id' => $seed->id], [
                                            'class' => 'btn btn-outline-primary',
                                            'title' => 'Edytuj',
                                            'encode' => false
                                        ]) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Additional Statistics -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-range me-2"></i>Statystyki okresów wysiewu
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    // Calculate sowing periods statistics
                    $currentSeeds = \app\models\Seed::getSowingSeeds();
                    $springSeeds = \app\models\Seed::find()->where(['between', 'MONTH(sowing_start)', 3, 5])->count();
                    $summerSeeds = \app\models\Seed::find()->where(['between', 'MONTH(sowing_start)', 6, 8])->count();
                    $autumnSeeds = \app\models\Seed::find()->where(['between', 'MONTH(sowing_start)', 9, 11])->count();
                    $winterSeeds = \app\models\Seed::find()->where(['or', ['between', 'MONTH(sowing_start)', 12, 12], ['between', 'MONTH(sowing_start)', 1, 2]])->count();
                    ?>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-flower2 text-success fs-3"></i>
                                <h4 class="text-success mt-2"><?= $springSeeds ?></h4>
                                <small class="text-muted">Wiosna (03-05)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-sun text-warning fs-3"></i>
                                <h4 class="text-warning mt-2"><?= $summerSeeds ?></h4>
                                <small class="text-muted">Lato (06-08)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-tree text-info fs-3"></i>
                                <h4 class="text-info mt-2"><?= $autumnSeeds ?></h4>
                                <small class="text-muted">Jesień (09-11)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-secondary bg-opacity-10 rounded">
                                <i class="bi bi-snow text-secondary fs-3"></i>
                                <h4 class="text-secondary mt-2"><?= $winterSeeds ?></h4>
                                <small class="text-muted">Zima (12-02)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <strong><i class="bi bi-info-circle me-2"></i>Aktualnie do wysiewu:</strong>
                        <span class="badge bg-success ms-2"><?= count($currentSeeds) ?> nasion</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Przydatne informacje
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $highPrioritySeeds = \app\models\Seed::find()->where(['>=', 'priority', 8])->andWhere(['status' => 'available'])->count();
                    $mediumPrioritySeeds = \app\models\Seed::find()->where(['between', 'priority', 5, 7])->andWhere(['status' => 'available'])->count();
                    $lowPrioritySeeds = \app\models\Seed::find()->where(['between', 'priority', 1, 4])->andWhere(['status' => 'available'])->count();
                    $noPrioritySeeds = \app\models\Seed::find()->where(['priority' => 0])->andWhere(['status' => 'available'])->count();
                    
                    $thisYearSeeds = \app\models\Seed::find()->where(['purchase_year' => date('Y')])->count();
                    $lastYearSeeds = \app\models\Seed::find()->where(['purchase_year' => date('Y') - 1])->count();
                    ?>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <h6>Priorytety (dostępne):</h6>
                            <ul class="list-unstyled">
                                <li><span class="priority-badge priority-high me-2">8-10</span><?= $highPrioritySeeds ?></li>
                                <li><span class="priority-badge priority-medium me-2">5-7</span><?= $mediumPrioritySeeds ?></li>
                                <li><span class="priority-badge priority-low me-2">1-4</span><?= $lowPrioritySeeds ?></li>
                                <li><span class="priority-badge priority-none me-2">0</span><?= $noPrioritySeeds ?></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h6>Rok zakupu:</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-calendar-check text-success me-2"></i><?= date('Y') ?>: <strong><?= $thisYearSeeds ?></strong></li>
                                <li><i class="bi bi-calendar text-info me-2"></i><?= date('Y') - 1 ?>: <strong><?= $lastYearSeeds ?></strong></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <?= Html::a('<i class="bi bi-speedometer2 me-2"></i>Dashboard', ['/dashboard/index'], [
                            'class' => 'btn btn-primary',
                            'encode' => false
                        ]) ?>
                        <?= Html::a('<i class="bi bi-calendar3 me-2"></i>Kalendarz wysiewów', ['/dashboard/sowing-calendar'], [
                            'class' => 'btn btn-info',
                            'encode' => false
                        ]) ?>
                        <?= Html::a('<i class="bi bi-graph-up me-2"></i>Raport kiełkowania', ['/dashboard/germination-report'], [
                            'class' => 'btn btn-warning text-dark',
                            'encode' => false
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>