<?php
/**
 * LOKALIZACJA: views/site/request-password-reset.php
 * POPRAWIONY - bez zmiennej $user
 */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Resetowanie hasła';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.reset-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.reset-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    padding: 0;
    width: 100%;
    max-width: 450px;
    overflow: hidden;
}

.reset-header {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
    padding: 30px 40px;
    text-align: center;
}

.reset-body {
    padding: 40px;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    color: white;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
    color: white;
}

.btn-outline-secondary {
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #6c757d;
    text-decoration: none;
    font-size: 14px;
}

.back-link:hover {
    color: #495057;
    text-decoration: underline;
}
</style>

<div class="reset-container">
    <div class="reset-card">
        <div class="reset-header">
            <h1 class="h4 mb-0">
                <i class="bi bi-key me-2"></i>Resetowanie hasła
            </h1>
            <p class="mb-0 opacity-75">Wprowadź swój adres email</p>
        </div>
        
        <div class="reset-body">
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                Wprowadź adres email przypisany do Twojego konta. Otrzymasz link do resetowania hasła.
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'request-password-reset-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'form-label fw-semibold'],
                    'inputOptions' => ['class' => 'form-control'],
                ],
            ]); ?>

                <div class="mb-4">
                    <?= $form->field($model, 'email')->input('email', [
                        'autofocus' => true,
                        'placeholder' => 'np. admin@example.com',
                        'required' => true
                    ])->label('<i class="bi bi-envelope me-2"></i>Adres email', ['encode' => false]) ?>
                </div>

                <div class="d-grid gap-2">
                    <?= Html::submitButton(
                        '<i class="bi bi-send me-2"></i>Wyślij link resetowania', 
                        ['class' => 'btn btn-warning btn-lg', 'encode' => false]
                    ) ?>
                    
                    <?= Html::a(
                        '<i class="bi bi-arrow-left me-2"></i>Powrót do logowania', 
                        ['site/login'], 
                        ['class' => 'btn btn-outline-secondary btn-lg', 'encode' => false]
                    ) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>