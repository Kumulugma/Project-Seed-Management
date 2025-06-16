<?php

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);

$this->registerCsrfMetaTags();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= Html::encode($this->title) ?> - System Zarządzania Nasionami</title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header class="no-print">
    <?php
    NavBar::begin([
        'brandLabel' => '<i class="bi bi-flower2 me-2"></i>' . Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow',
        ],
        'collapseOptions' => [
            'class' => 'collapse navbar-collapse',
        ],
        'togglerOptions' => [
            'class' => 'navbar-toggler',
            'data-bs-toggle' => 'collapse',
            'data-bs-target' => '#navbarNav',
        ],
    ]);
    
    $menuItems = [];
    
    if (Yii::$app->user->isGuest) {
        $menuItems[] = [
            'label' => '<i class="bi bi-box-arrow-in-right me-1"></i>Logowanie', 
            'url' => ['/site/login'], 
            'encode' => false
        ];
    } else {
        $menuItems[] = [
            'label' => '<i class="bi bi-speedometer2 me-1"></i>Dashboard', 
            'url' => ['/dashboard/index'], 
            'encode' => false
        ];
        
        $menuItems[] = [
            'label' => '<i class="bi bi-seed me-1"></i>Nasiona', 
            'encode' => false,
            'items' => [
                [
                    'label' => '<i class="bi bi-list-ul me-2"></i>Lista nasion', 
                    'url' => ['/seed/index'], 
                    'encode' => false
                ],
                [
                    'label' => '<i class="bi bi-plus-circle me-2"></i>Dodaj nasiona', 
                    'url' => ['/seed/create'], 
                    'encode' => false
                ],
                '<div class="dropdown-divider"></div>',
                [
                    'label' => '<i class="bi bi-graph-up me-2"></i>Statystyki', 
                    'url' => ['/seed/stats'], 
                    'encode' => false
                ],
                [
                    'label' => '<i class="bi bi-download me-2"></i>Eksport CSV', 
                    'url' => ['/seed/export'], 
                    'encode' => false
                ],
            ]
        ];
        
        $menuItems[] = [
            'label' => '<i class="bi bi-file-earmark-text me-1"></i>Raporty', 
            'encode' => false,
            'items' => [
                [
                    'label' => '<i class="bi bi-graph-up me-2"></i>Raport kiełkowania', 
                    'url' => ['/dashboard/germination-report'], 
                    'encode' => false
                ],
                [
                    'label' => '<i class="bi bi-calendar3 me-2"></i>Kalendarz wysiewów', 
                    'url' => ['/dashboard/sowing-calendar'], 
                    'encode' => false
                ],
            ]
        ];
        
        $menuItems[] = [
            'label' => '<i class="bi bi-person-circle me-1"></i>' . Yii::$app->user->identity->username, 
            'encode' => false,
            'items' => [
                [
                    'label' => '<i class="bi bi-key me-2"></i>Zmień hasło', 
                    'url' => ['/site/change-password'], 
                    'encode' => false
                ],
                [
                    'label' => '<i class="bi bi-info-circle me-2"></i>O systemie', 
                    'url' => ['/site/about'], 
                    'encode' => false
                ],
                '<div class="dropdown-divider"></div>',
                [
                    'label' => '<i class="bi bi-box-arrow-right me-2"></i>Wyloguj',
                    'url' => ['/site/logout'],
                    'encode' => false,
                    'linkOptions' => [
                        'data-method' => 'post'
                    ]
                ]
            ]
        ];
    }
    
    if (!Yii::$app->user->isGuest) {
    // Dodaj przed menu użytkownika
    $menuItems[] = [
        'label' => '<button class="btn btn-outline-light btn-sm dark-mode-toggle" onclick="toggleDarkMode()" title="Przełącz tryb">' .
                  '<i class="bi bi-moon" id="dark-mode-icon"></i>' .
                  '</button>',
        'encode' => false,
        'linkOptions' => ['style' => 'padding:0; background:transparent; border:none;']
    ];
}
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto', 'id' => 'navbarNav'],
        'items' => $menuItems,
        'activateParents' => true,
        'encodeLabels' => false,
    ]);
    
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0 main-container">
    <div class="container-fluid px-4">
        <div class="alert-container"></div>
        
        <?php 
        // Ukryj breadcrumb na stronie login
        $currentRoute = Yii::$app->controller->route;
        if ($currentRoute !== 'site/login'): 
        ?>
        
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'options' => ['class' => 'breadcrumb mt-3 mb-4 bg-white'],
            'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
            'activeItemTemplate' => "<li class=\"breadcrumb-item active\">{link}</li>\n",
        ]) ?>
        
        <?php endif; ?>
        
        <?= Alert::widget() ?>
        
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto no-print bg-light border-top">
    <div class="container-fluid px-4">
        <div class="row py-3">
            <div class="col-md-12 text-center">
                <p class="text-muted mb-0">
                    &copy; System Zarządzania Nasionami <?= date('Y') ?>
                </p>
            </div>
        </div>
    </div>
</footer>
    <script>
// Dark Mode Toggle Functionality
function toggleDarkMode() {
    const body = document.body;
    const icon = document.getElementById('dark-mode-icon');
    const isDark = body.classList.contains('dark-mode');
    
    if (isDark) {
        // Przełącz na light mode
        body.classList.remove('dark-mode');
        icon.classList.remove('bi-sun');
        icon.classList.add('bi-moon');
        localStorage.setItem('darkMode', 'false');
    } else {
        // Przełącz na dark mode
        body.classList.add('dark-mode');
        icon.classList.remove('bi-moon');
        icon.classList.add('bi-sun');
        localStorage.setItem('darkMode', 'true');
    }
}

// Inicjalizacja dark mode przy ładowaniu strony
document.addEventListener('DOMContentLoaded', function() {
    const savedMode = localStorage.getItem('darkMode');
    const icon = document.getElementById('dark-mode-icon');
    
    if (savedMode === 'true') {
        document.body.classList.add('dark-mode');
        if (icon) {
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
        }
    }
    
    // Dodaj loading effect do wszystkich linków
    document.querySelectorAll('a, form').forEach(element => {
        element.addEventListener('click', function(e) {
            // Nie dodawaj loading do dropdown toggle i podobnych
            if (!this.hasAttribute('data-bs-toggle') && 
                !this.classList.contains('dark-mode-toggle') &&
                !this.classList.contains('dropdown-toggle')) {
                
                // Dodaj loading do elementu
                this.classList.add('loading');
                
                // Usuń loading po 3 sekundach (na wypadek błędu)
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 3000);
            }
        });
    });
});

// Loading dla AJAX requestów (jeśli używasz)
document.addEventListener('ajaxStart', function() {
    document.body.classList.add('loading');
});

document.addEventListener('ajaxComplete', function() {
    document.body.classList.remove('loading');
});
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>