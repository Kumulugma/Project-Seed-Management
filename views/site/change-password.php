<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Zmiana hasła';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="change-password-page">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-key me-2"></i><?= Html::encode($this->title) ?>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Wskazówki:</h6>
                        <ul class="mb-0 small">
                            <li>Hasło powinno mieć minimum 6 znaków</li>
                            <li>Użyj kombinacji liter, cyfr i znaków specjalnych</li>
                            <li>Nie używaj prostych haseł typu "123456"</li>
                        </ul>
                    </div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'change-password-form',
                        'options' => ['class' => 'needs-validation', 'novalidate' => true],
                    ]); ?>

                    <div class="mb-3">
                        <?= $form->field($model, 'currentPassword')->passwordInput([
                            'placeholder' => 'Wpisz obecne hasło',
                            'class' => 'form-control',
                            'required' => true
                        ])->label('Obecne hasło <span class="text-danger">*</span>', ['encode' => false]) ?>
                    </div>

                    <div class="mb-3">
                        <?= $form->field($model, 'newPassword')->passwordInput([
                            'placeholder' => 'Wpisz nowe hasło (min. 6 znaków)',
                            'class' => 'form-control',
                            'required' => true,
                            'minlength' => 6
                        ])->label('Nowe hasło <span class="text-danger">*</span>', ['encode' => false]) ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'confirmPassword')->passwordInput([
                            'placeholder' => 'Powtórz nowe hasło',
                            'class' => 'form-control',
                            'required' => true
                        ])->label('Potwierdź hasło <span class="text-danger">*</span>', ['encode' => false]) ?>
                    </div>

                    <div class="d-grid gap-2">
                        <?= Html::submitButton(
                            '<i class="bi bi-check-circle me-2"></i>Zmień hasło', 
                            ['class' => 'btn btn-primary btn-lg']
                        ) ?>
                        
                        <?= Html::a(
                            '<i class="bi bi-x-circle me-2"></i>Anuluj', 
                            ['/dashboard/index'], 
                            ['class' => 'btn btn-outline-secondary btn-lg']
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <div class="alert alert-warning mt-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Uwaga:</strong> Po zmianie hasła zostaniesz automatycznie wylogowany 
                ze wszystkich urządzeń i będziesz musiał zalogować się ponownie.
            </div>
        </div>
    </div>
</div>