<?php

use yii\helpers\Html;

$this->title = 'O systemie';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="about-page">
    <div class="hero-section bg-success text-white rounded-3 p-5 mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="bi bi-flower2 me-3"></i>System Zarządzania Nasionami
                </h1>
                <p class="lead mb-4">Profesjonalne narzędzie do zarządzania prywatnym zapasem nasion, planowania wysiewów i śledzenia kiełkowania.</p>
                <div class="d-flex gap-3">
                    <?= Html::a('<i class="bi bi-speedometer2 me-2"></i>Dashboard', ['/dashboard/index'], [
                        'class' => 'btn btn-light btn-lg',
                        'encode' => false
                    ]) ?>
                    <?= Html::a('<i class="bi bi-plus-circle me-2"></i>Dodaj nasiona', ['/seed/create'], [
                        'class' => 'btn btn-outline-light btn-lg',
                        'encode' => false
                    ]) ?>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-gear-fill" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white">
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
                            <div class="stat-item text-center p-3 bg-primary bg-opacity-10 rounded">
                                <h3 class="text-primary mb-1"><?= $totalSeeds ?></h3>
                                <small class="text-muted">Nasion w bazie</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center p-3 bg-success bg-opacity-10 rounded">
                                <h3 class="text-success mb-1"><?= $availableSeeds ?></h3>
                                <small class="text-muted">Dostępnych</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center p-3 bg-info bg-opacity-10 rounded">
                                <h3 class="text-info mb-1"><?= $sownTotal ?></h3>
                                <small class="text-muted">Łącznie wysiewów</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center p-3 bg-warning bg-opacity-10 rounded">
                                <h3 class="text-warning mb-1"><?= $germinatedTotal ?></h3>
                                <small class="text-muted">Wykiełkowało</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle me-2"></i>System działa poprawnie</h6>
                            <p class="mb-0 small">Ostatnia aktualizacja: <?= date('d.m.Y H:i') ?></p>
                        </div>
                    </div>
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
                            <div class="alert alert-info mb-0">
                                <h6><i class="bi bi-lightbulb me-2"></i>Wskazówki:</h6>
                                <ul class="mb-0 small">
                                    <li>Regularnie sprawdzaj daty ważności nasion</li>
                                    <li>Używaj priorytetów do organizacji wysiewów</li>
                                    <li>Drukuj etykiety dla lepszej organizacji</li>
                                    <li>Śledź statystyki kiełkowania</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>