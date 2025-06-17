<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Logowanie';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    padding: 0;
    width: 100%;
    max-width: 400px;
    overflow: hidden;
}

.login-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 30px 40px;
    text-align: center;
}

.login-header h1 {
    margin: 0 0 10px 0;
    font-weight: 600;
}

.login-body {
    padding: 40px;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}

.forgot-password-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #6c757d;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s ease;
}

.forgot-password-link:hover {
    color: #28a745;
    text-decoration: underline;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1 class="h4 mb-0">
                <i class="bi bi-flower2 me-2"></i>System Zarządzania Nasionami
            </h1>
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
                        'placeholder' => 'Wprowadź nazwę użytkownika',
                    ])->label('<i class="bi bi-person me-2"></i>Nazwa użytkownika', ['encode' => false]) ?>
                </div>

                <div class="mb-3">
                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => 'Wprowadź hasło',
                    ])->label('<i class="bi bi-lock me-2"></i>Hasło', ['encode' => false]) ?>
                </div>

                <div class="mb-4">
                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}",
                        'labelOptions' => ['class' => 'form-check-label'],
                        'inputOptions' => ['class' => 'form-check-input'],
                    ]) ?>
                </div>

                <div class="d-grid">
                    <?= Html::submitButton(
                        '<i class="bi bi-box-arrow-in-right me-2"></i>Zaloguj się', 
                        [
                            'class' => 'btn btn-primary btn-lg',
                            'name' => 'login-button',
                            'encode' => false
                        ]
                    ) ?>
                </div>

            <?php ActiveForm::end(); ?>
            
            <?= Html::a(
                '<i class="bi bi-key me-2"></i>Zapomniałeś hasła?', 
                ['site/request-password-reset'], 
                ['class' => 'forgot-password-link', 'encode' => false]
            ) ?>
        </div>
    </div>
</div>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle me-2"></i>
                <strong class="me-auto">Sukces</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <div class="toast show" role="alert">
            <div class="toast-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong class="me-auto">Błąd</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        </div>
    </div>
<?php endif; ?>