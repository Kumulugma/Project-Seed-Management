<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Logowanie';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1 class="h4 mb-0">System Zarządzania Nasionami</h1>
            <p class="mb-0 opacity-75">Zaloguj się do swojego konta</p>
        </div>
        
        <div class="login-body">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'form-label fw-semibold'],
                    'inputOptions' => ['class' => 'form-control'],
                ],
            ]); ?>

                <div class="mb-3">
                    <?= $form->field($model, 'username')->textInput([
                        'autofocus' => true,
                        'placeholder' => 'Nazwa użytkownika',
                    ]) ?>
                </div>

                <div class="mb-3">
                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => 'Hasło',
                    ]) ?>
                </div>

                <div class="mb-4">
                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}",
                        'labelOptions' => ['class' => 'form-check-label'],
                        'inputOptions' => ['class' => 'form-check-input'],
                    ]) ?>
                </div>

                <div class="d-grid">
                    <?= Html::submitButton('Zaloguj się', [
                        'class' => 'btn btn-primary btn-lg',
                        'name' => 'login-button'
                    ]) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>