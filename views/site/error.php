<?php
/**
 * =================================================================
 * LOKALIZACJA: views/site/error.php
 */
?>
<?php
use yii\helpers\Html;

$this->title = $name;
?>

<div class="site-error">
    <div class="jumbotron" style="background-color: #f8f8f8;">
        <div class="container text-center">
            <h1 style="font-size: 72px; color: #d9534f;">
                <?php if ($exception->statusCode == 404): ?>
                    🔍
                <?php elseif ($exception->statusCode == 403): ?>
                    🚫
                <?php elseif ($exception->statusCode >= 500): ?>
                    ⚠️
                <?php else: ?>
                    ❌
                <?php endif; ?>
            </h1>
            
            <h2><?= Html::encode($this->title) ?></h2>
            
            <?php if ($exception->statusCode == 404): ?>
                <p class="lead">Nie znaleziono strony, której szukasz.</p>
                <p>Sprawdź czy adres URL jest poprawny lub skorzystaj z nawigacji.</p>
            <?php elseif ($exception->statusCode == 403): ?>
                <p class="lead">Nie masz uprawnień do przeglądania tej strony.</p>
                <p>Zaloguj się lub skontaktuj z administratorem.</p>
            <?php elseif ($exception->statusCode >= 500): ?>
                <p class="lead">Wystąpił błąd serwera.</p>
                <p>Spróbuj ponownie za chwilę lub skontaktuj się z administratorem.</p>
            <?php else: ?>
                <p class="lead">Wystąpił nieoczekiwany błąd.</p>
            <?php endif; ?>

            <div class="alert alert-danger" style="margin-top: 20px;">
                <?= nl2br(Html::encode($message)) ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">🛠️ Co możesz zrobić?</h3>
                    </div>
                    <div class="panel-body text-center">
                        <div class="btn-group-vertical" style="width: 100%;">
                            <?= Html::a('🏠 Przejdź do dashboardu', ['/dashboard/index'], [
                                'class' => 'btn btn-primary btn-lg'
                            ]) ?>
                            
                            <?= Html::a('🌱 Lista nasion', ['/seed/index'], [
                                'class' => 'btn btn-success btn-lg'
                            ]) ?>
                            
                            <?= Html::a('🔙 Wróć do poprzedniej strony', 'javascript:history.back()', [
                                'class' => 'btn btn-default btn-lg'
                            ]) ?>
                            
                            <?php if (Yii::$app->user->isGuest): ?>
                                <?= Html::a('🔑 Zaloguj się', ['/site/login'], [
                                    'class' => 'btn btn-warning btn-lg'
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Informacje techniczne (tylko w trybie deweloperskim) -->
                <?php if (YII_ENV_DEV): ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">🔧 Informacje techniczne</h3>
                    </div>
                    <div class="panel-body">
                        <dl class="dl-horizontal">
                            <dt>Kod błędu:</dt>
                            <dd><?= $exception->statusCode ?></dd>
                            
                            <dt>Plik:</dt>
                            <dd><code><?= $exception->getFile() ?></code></dd>
                            
                            <dt>Linia:</dt>
                            <dd><?= $exception->getLine() ?></dd>
                            
                            <dt>Czas:</dt>
                            <dd><?= date('Y-m-d H:i:s') ?></dd>
                        </dl>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>