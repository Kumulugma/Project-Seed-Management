<?php
/**
 * LOKALIZACJA: views/site/about.php
 */

use yii\helpers\Html;

$this->title = 'O systemie';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="about-page">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 text-primary fw-bold">
                <i class="bi bi-info-circle me-2"></i><?= Html::encode($this->title) ?>
            </h1>
            <p class="lead text-muted">Profesjonalne narzędzie do zarządzania prywatnym zapasem nasion, planowania wysiewów i śledzenia kiełkowania.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                <?= Html::a('<i class="bi bi-speedometer2 me-2"></i>Dashboard', ['/dashboard/index'], [
                    'class' => 'btn btn-success',
                    'encode' => false
                ]) ?>
                <?= Html::a('<i class="bi bi-plus-circle me-2"></i>Dodaj nasiona', ['/seed/create'], [
                    'class' => 'btn btn-outline-success',
                    'encode' => false
                ]) ?>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>Funkcjonalności systemu
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="feature-group">
                                <h5 class="text-success">
                                    <i class="bi bi-database me-2"></i>Zarządzanie nasionami
                                </h5>
                                <ul class="list-unstyled ps-4">
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Kompletna baza danych nasion</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Zdjęcia opakowań</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Daty ważności i zakupu</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Okresy wysiewu</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Priorytety i kategorie</li>
                                </ul>
                            </div>

                            <div class="feature-group mt-4">
                                <h5 class="text-primary">
                                    <i class="bi bi-calendar3 me-2"></i>Planowanie wysiewów
                                </h5>
                                <ul class="list-unstyled ps-4">
                                    <li><i class="bi bi-check-circle text-primary me-2"></i>Dashboard z aktualnymi nasionami</li>
                                    <li><i class="bi bi-check-circle text-primary me-2"></i>Kalendarz wysiewów</li>
                                    <li><i class="bi bi-check-circle text-primary me-2"></i>Automatyczne przypomnienia</li>
                                    <li><i class="bi bi-check-circle text-primary me-2"></i>Generowanie planów PDF</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-group">
                                <h5 class="text-warning">
                                    <i class="bi bi-graph-up me-2"></i>Śledzenie kiełkowania
                                </h5>
                                <ul class="list-unstyled ps-4">
                                    <li><i class="bi bi-check-circle text-warning me-2"></i>Historia wysiewów</li>
                                    <li><i class="bi bi-check-circle text-warning me-2"></i>Status kiełkowania</li>
                                    <li><i class="bi bi-check-circle text-warning me-2"></i>Statystyki powodzenia</li>
                                    <li><i class="bi bi-check-circle text-warning me-2"></i>Notatki i obserwacje</li>
                                </ul>
                            </div>

                            <div class="feature-group mt-4">
                                <h5 class="text-info">
                                    <i class="bi bi-printer me-2"></i>Etykiety i raporty
                                </h5>
                                <ul class="list-unstyled ps-4">
                                    <li><i class="bi bi-check-circle text-info me-2"></i>Generowanie etykiet 4x1.5cm</li>
                                    <li><i class="bi bi-check-circle text-info me-2"></i>Kody wysiewu</li>
                                    <li><i class="bi bi-check-circle text-info me-2"></i>Raporty kiełkowania</li>
                                    <li><i class="bi bi-check-circle text-info me-2"></i>Eksport danych CSV</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Statystyki systemu
                    </h4>
                </div>
                <div class="card-body">
                    <?php
                    $totalSeeds = \app\models\Seed::find()->count();
                    $availableSeeds = \app\models\Seed::find()->where(['status' => 'available'])->count();
                    $sownTotal = \app\models\SownSeed::find()->count();
                    $germinatedTotal = \app\models\SownSeed::find()->where(['status' => 'germinated'])->count();
                    ?>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h3 text-primary mb-1"><?= $totalSeeds ?></div>
                                <small class="text-muted">Nasion ogółem</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h3 text-success mb-1"><?= $availableSeeds ?></div>
                                <small class="text-muted">Dostępnych</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h3 text-info mb-1"><?= $sownTotal ?></div>
                                <small class="text-muted">Wysianych</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h3 text-warning mb-1"><?= $germinatedTotal ?></div>
                                <small class="text-muted">Wykiełkowanych</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="progress" style="height: 8px;">
                            <?php
                            $percentage = $sownTotal > 0 ? round(($germinatedTotal / $sownTotal) * 100) : 0;
                            ?>
                            <div class="progress-bar bg-success" style="width: <?= $percentage ?>%"></div>
                        </div>
                        <small class="text-muted">Skuteczność kiełkowania: <?= $percentage ?>%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technology Info -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Technologie
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Backend:</dt>
                        <dd class="col-sm-8">PHP 8.0+ z Yii Framework 2.0</dd>
                        
                        <dt class="col-sm-4">Frontend:</dt>
                        <dd class="col-sm-8">Bootstrap 5 + Custom CSS/JS</dd>
                        
                        <dt class="col-sm-4">Baza danych:</dt>
                        <dd class="col-sm-8">MySQL 8.0+</dd>
                        
                        <dt class="col-sm-4">Ikony:</dt>
                        <dd class="col-sm-8">Bootstrap Icons</dd>
                        
                        <dt class="col-sm-4">PDF:</dt>
                        <dd class="col-sm-8">mPDF</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Wskazówki użycia
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Regularnie sprawdzaj daty ważności nasion
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Używaj priorytetów do organizacji wysiewów
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Drukuj etykiety dla lepszej organizacji
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Śledź statystyki kiełkowania dla lepszych wyników
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Używaj skrótu <kbd>Ctrl+Shift+D</kbd> dla dark mode
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Version Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informacje o wersji
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Wersja:</dt>
                                <dd class="col-sm-8"><span class="badge bg-primary">1.0.0</span></dd>
                                
                                <dt class="col-sm-4">Data wydania:</dt>
                                <dd class="col-sm-8"><?= date('d.m.Y') ?></dd>
                                
                                <dt class="col-sm-4">Framework:</dt>
                                <dd class="col-sm-8">Yii Framework 2.0 + Bootstrap 5</dd>
                                
                                <dt class="col-sm-4">Licencja:</dt>
                                <dd class="col-sm-8">Użytek prywatny</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-light border mb-0">
                                <h6 class="mb-3">
                                    <i class="bi bi-keyboard me-2"></i>Skróty klawiszowe:
                                </h6>
                                <ul class="mb-0 small">
                                    <li><kbd>Ctrl+Shift+D</kbd> - Przełącz tryb ciemny</li>
                                    <li><kbd>Ctrl+Shift+T</kbd> - Demo toast (development)</li>
                                    <li><kbd>Alt+H</kbd> - Przejdź do strony głównej</li>
                                    <li><kbd>Alt+S</kbd> - Dodaj nowe nasiona</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>