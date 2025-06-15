<?php
/**
 * LOKALIZACJA: views/seed/view.php
 */

/* @var $this yii\web\View */
/* @var $model app\models\Seed */
/* @var $sownSeeds app\models\SownSeed[] */

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Nasiona', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Sprawdź czy nasiono jest w okresie wysiewu
$inSowingPeriod = $model->isInSowingPeriod();

// Oblicz dni do/od ważności
$expiryInfo = '';
if ($model->expiry_date) {
    $today = new DateTime();
    $expiry = new DateTime($model->expiry_date);
    $interval = $today->diff($expiry);
    $daysLeft = $interval->invert ? -$interval->days : $interval->days;
    
    if ($daysLeft < 0) {
        $expiryInfo = '<span class="label label-danger">Wygasło ' . abs($daysLeft) . ' dni temu</span>';
    } elseif ($daysLeft == 0) {
        $expiryInfo = '<span class="label label-danger">Wygasa dzisiaj!</span>';
    } elseif ($daysLeft <= 30) {
        $expiryInfo = '<span class="label label-warning">Pozostało ' . $daysLeft . ' dni</span>';
    } elseif ($daysLeft <= 90) {
        $expiryInfo = '<span class="label label-info">Pozostało ' . $daysLeft . ' dni</span>';
    } else {
        $expiryInfo = '<span class="label label-success">Ważne przez ' . $daysLeft . ' dni</span>';
    }
}
?>

<div class="seed-view">
    <div class="row">
        <div class="col-md-8">
            <h1>
                <?= Html::encode($this->title) ?>
                <?php if ($inSowingPeriod): ?>
                    <span class="label label-success">Okres wysiewu</span>
                <?php endif; ?>
            </h1>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <?= Html::a('✏️ Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('📋 Kopiuj', ['copy', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
                
                <?php if ($model->status === 'available'): ?>
                    <?= Html::a('❌ Oznacz jako zużyte', ['mark-as-used', 'id' => $model->id], [
                        'class' => 'btn btn-default',
                        'data' => ['confirm' => 'Czy na pewno oznaczyć jako zużyte?', 'method' => 'post'],
                    ]) ?>
                <?php else: ?>
                    <?= Html::a('✅ Oznacz jako dostępne', ['mark-as-available', 'id' => $model->id], [
                        'class' => 'btn btn-success',
                        'data' => ['method' => 'post'],
                    ]) ?>
                <?php endif; ?>
                
                <?= Html::a('🗑️ Usuń', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Czy na pewno chcesz usunąć to nasiono? Ta operacja jest nieodwracalna.',
                        'method' => 'post'
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Alert o okresie wysiewu -->
    <?php if ($inSowingPeriod): ?>
        <div class="alert alert-success">
            <h4>🌱 To nasiono jest w okresie wysiewu!</h4>
            <p>Możesz je teraz wysiać. Okres wysiewu: 
                <strong><?= date('d.m', strtotime($model->sowing_start)) ?> - <?= date('d.m', strtotime($model->sowing_end)) ?></strong>
            </p>
            <p>
                <?= Html::a('➕ Dodaj do wysiewu', ['/dashboard/index'], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Alert o ważności -->
    <?php if ($expiryInfo && (strpos($expiryInfo, 'danger') !== false || strpos($expiryInfo, 'warning') !== false)): ?>
        <div class="alert alert-<?= strpos($expiryInfo, 'danger') !== false ? 'danger' : 'warning' ?>">
            <h4>⚠️ Uwaga na datę ważności!</h4>
            <p><?= $expiryInfo ?></p>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <!-- Szczegóły nasiona -->
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-striped table-bordered detail-view'],
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'name',
                        'format' => 'html',
                        'value' => '<strong>' . Html::encode($model->name) . '</strong>',
                    ],
                    [
                        'attribute' => 'description',
                        'format' => 'ntext',
                        'visible' => !empty($model->description),
                    ],
                    [
                        'attribute' => 'type',
                        'format' => 'html',
                        'value' => function($model) {
                            $colors = ['vegetables' => 'success', 'flowers' => 'primary', 'herbs' => 'info'];
                            $color = $colors[$model->type] ?? 'default';
                            return '<span class="label label-' . $color . '">' . $model->getTypeLabel() . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'height',
                        'format' => 'html',
                        'value' => function($model) {
                            $color = $model->height === 'high' ? 'primary' : 'default';
                            return '<span class="label label-' . $color . '">' . $model->getHeightLabel() . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'plant_type',
                        'format' => 'html',
                        'value' => function($model) {
                            $color = $model->plant_type === 'perennial' ? 'success' : 'warning';
                            return '<span class="label label-' . $color . '">' . $model->getPlantTypeLabel() . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'sowing_start',
                        'format' => 'date',
                        'label' => 'Początek wysiewu',
                    ],
                    [
                        'attribute' => 'sowing_end',
                        'format' => 'date',
                        'label' => 'Koniec wysiewu',
                    ],
                    [
                        'label' => 'Okres wysiewu',
                        'format' => 'html',
                        'value' => date('d.m', strtotime($model->sowing_start)) . ' - ' . date('d.m', strtotime($model->sowing_end)) .
                                  ($inSowingPeriod ? ' <span class="label label-success">Aktualny</span>' : ''),
                    ],
                    [
                        'attribute' => 'expiry_date',
                        'format' => 'html',
                        'value' => $model->expiry_date ? 
                                  date('d.m.Y', strtotime($model->expiry_date)) . ' ' . $expiryInfo : 
                                  '<span class="text-muted">Brak daty</span>',
                    ],
                    [
                        'attribute' => 'purchase_year',
                        'visible' => !empty($model->purchase_year),
                    ],
                    [
                        'attribute' => 'priority',
                        'format' => 'html',
                        'value' => function($model) {
                            $color = 'default';
                            if ($model->priority >= 8) $color = 'danger';
                            elseif ($model->priority >= 5) $color = 'warning';
                            elseif ($model->priority > 0) $color = 'info';
                            
                            return '<span class="badge" style="background-color: ' . 
                                   ($color === 'danger' ? '#d9534f' : 
                                    ($color === 'warning' ? '#f0ad4e' : 
                                     ($color === 'info' ? '#5bc0de' : '#777'))) . '">' . 
                                   $model->priority . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'html',
                        'value' => function($model) {
                            $color = $model->status === 'available' ? 'success' : 'default';
                            return '<span class="label label-' . $color . '">' . $model->getStatusLabel() . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'label' => 'Data dodania',
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => 'datetime',
                        'label' => 'Ostatnia aktualizacja',
                    ],
                ],
            ]) ?>
        </div>
        
        <div class="col-md-4">
            <!-- Zdjęcie nasiona -->
            <?php if ($model->image_path): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">📷 Zdjęcie opakowania</h4>
                    </div>
                    <div class="panel-body text-center">
                        <?= Html::img($model->getImageUrl(), [
                            'class' => 'img-responsive',
                            'style' => 'max-width: 100%; border-radius: 8px; cursor: pointer;',
                            'onclick' => 'openImageModal(this.src)'
                        ]) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="panel panel-default">
                    <div class="panel-body text-center text-muted">
                        <i class="glyphicon glyphicon-camera" style="font-size: 48px;"></i>
                        <p>Brak zdjęcia opakowania</p>
                        <?= Html::a('➕ Dodaj zdjęcie', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Szybkie akcje -->
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">⚡ Szybkie akcje</h4>
                </div>
                <div class="panel-body">
                    <?php if ($inSowingPeriod && $model->status === 'available'): ?>
                        <p>
                            <?= Html::a('🌱 Wysiaj teraz', ['/dashboard/index'], [
                                'class' => 'btn btn-success btn-block'
                            ]) ?>
                        </p>
                    <?php endif; ?>
                    
                    <p>
                        <?= Html::a('📋 Utwórz kopię', ['copy', 'id' => $model->id], [
                            'class' => 'btn btn-warning btn-block'
                        ]) ?>
                    </p>
                    
                    <p>
                        <?= Html::a('📊 Zobacz statystyki', ['/seed/stats'], [
                            'class' => 'btn btn-info btn-block'
                        ]) ?>
                    </p>
                    
                    <p>
                        <?= Html::a('🔙 Powrót do listy', ['index'], [
                            'class' => 'btn btn-default btn-block'
                        ]) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Historia wysiewów -->
    <?php if (!empty($sownSeeds)): ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">📈 Historia wysiewów (<?= count($sownSeeds) ?>)</h3>
            </div>
            <div class="panel-body">
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
                    'tableOptions' => ['class' => 'table table-striped table-condensed'],
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
                                $color = $model->getStatusColor();
                                return '<span class="label label-' . $color . '">' . $model->getStatusLabel() . '</span>';
                            },
                            'label' => 'Status',
                        ],
                        [
                            'label' => 'Dni od wysiewu',
                            'value' => function($model) {
                                return $model->getDaysFromSowing();
                            },
                        ],
                        [
                            'attribute' => 'notes',
                            'format' => 'ntext',
                            'label' => 'Notatki',
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{note} {delete}',
                            'buttons' => [
                                'note' => function ($url, $model) {
                                    return Html::a('📝', ['/dashboard/add-note', 'id' => $model->id], [
                                        'title' => 'Dodaj notatkę',
                                        'class' => 'btn btn-xs btn-info'
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a('🗑️', ['/dashboard/delete-sown', 'id' => $model->id], [
                                        'title' => 'Usuń wysiew',
                                        'class' => 'btn btn-xs btn-danger',
                                        'data' => [
                                            'confirm' => 'Czy na pewno usunąć ten zapis wysiewu?',
                                            'method' => 'post'
                                        ]
                                    ]);
                                },
                            ],
                            'urlCreator' => function ($action, $model, $key, $index) {
                                return ['dashboard/' . $action, 'id' => $model->id];
                            }
                        ],
                    ],
                ]); ?>
                
                <!-- Statystyki kiełkowania dla tego nasiona -->
                <?php 
                $stats = \app\models\SownSeed::getGerminationStats($model->id);
                if ($stats['total'] > 0):
                ?>
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>📊 Statystyki kiełkowania dla tego nasiona:</strong>
                                <ul class="list-inline" style="margin: 5px 0 0 0;">
                                    <li>Łącznie: <strong><?= $stats['total'] ?></strong></li>
                                    <li>Wykiełkowało: <strong class="text-success"><?= $stats['germinated'] ?></strong></li>
                                    <li>Nie wykiełkowało: <strong class="text-danger"><?= $stats['not_germinated'] ?></strong></li>
                                    <li>Wskaźnik kiełkowania: <strong><?= $stats['germination_rate'] ?>%</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <h4>📝 Historia wysiewów</h4>
            <p>To nasiono nie było jeszcze wysiewane. Gdy dodasz je do wysiewu, tutaj pojawi się historia.</p>
            <?php if ($inSowingPeriod): ?>
                <p><?= Html::a('🌱 Wysiaj teraz', ['/dashboard/index'], ['class' => 'btn btn-success']) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal do podglądu zdjęcia -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Zdjęcie opakowania - <?= Html::encode($model->name) ?></h4>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-responsive" style="max-width: 100%;">
            </div>
        </div>
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    $('#modalImage').attr('src', imageSrc);
    $('#imageModal').modal('show');
}
</script>