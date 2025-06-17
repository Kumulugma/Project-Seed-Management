<?php
/**
 * LOKALIZACJA: views/seed/_form.php
 * POPRAWIONY FORMULARZ Z POLAMI MM-DD
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Seed */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="seed-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false, // Wyłącz AJAX walidację
        'enableClientValidation' => true,
    ]); ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Podstawowe informacje
                    </h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true, 
                        'placeholder' => 'Nazwa nasiona',
                        'class' => 'form-control',
                        'required' => true
                    ])->label('Nazwa <span class="text-danger">*</span>', ['encode' => false]) ?>

                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 3, 
                        'placeholder' => 'Opis odmiany, charakterystyczne cechy...',
                        'class' => 'form-control'
                    ]) ?>

                    <?= $form->field($model, 'notes')->textarea([
                        'rows' => 2, 
                        'placeholder' => 'Dodatkowe notatki, uwagi...',
                        'class' => 'form-control'
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-image me-2"></i>Zdjęcie opakowania
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php if ($model->image_path): ?>
                        <img src="<?= $model->getImageUrl() ?>" class="img-thumbnail mb-3" style="max-width: 150px;">
                        <br>
                    <?php endif; ?>
                    
                    <?= $form->field($model, 'imageFile')->fileInput([
                        'accept' => 'image/*',
                        'class' => 'form-control'
                    ])->label('Wybierz zdjęcie') ?>
                    
                    <small class="text-muted">
                        Formaty: JPG, PNG, GIF (max 2MB)
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-sliders me-2"></i>Parametry rośliny
                    </h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'type')->dropDownList(
                        $model->getTypeOptions(),
                        [
                            'prompt' => 'Wybierz typ...',
                            'class' => 'form-select',
                            'required' => true
                        ]
                    )->label('Typ <span class="text-danger">*</span>', ['encode' => false]) ?>

                    <?= $form->field($model, 'height')->dropDownList(
                        $model->getHeightOptions(),
                        [
                            'prompt' => 'Wybierz wysokość...',
                            'class' => 'form-select',
                            'required' => true
                        ]
                    )->label('Wysokość rośliny <span class="text-danger">*</span>', ['encode' => false]) ?>

                    <?= $form->field($model, 'plant_type')->dropDownList(
                        $model->getPlantTypeOptions(),
                        [
                            'prompt' => 'Wybierz typ rośliny...',
                            'class' => 'form-select',
                            'required' => true
                        ]
                    )->label('Typ rośliny <span class="text-danger">*</span>', ['encode' => false]) ?>

                    <div class="priority-field">
                        <?= $form->field($model, 'priority')->textInput([
                            'type' => 'number', 
                            'min' => 0, 
                            'max' => 10, 
                            'value' => $model->priority ?: 0,
                            'class' => 'form-control',
                            'onchange' => 'updatePriorityPreview(this.value)'
                        ])->hint('0 = najniższy priorytet, 10 = najwyższy priorytet') ?>
                        <div class="mt-2">
                            <span class="priority-badge priority-none" id="priority-preview">0</span>
                            <small class="text-muted ms-2">Podgląd priorytetu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3 me-2"></i>Okresy i daty
                    </h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'sowing_start')->textInput([
                        'placeholder' => 'MM-DD (np. 03-15)',
                        'pattern' => '(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])',
                        'class' => 'form-control',
                        'required' => true,
                        'maxlength' => 5,
                        'title' => 'Format: MM-DD (miesiąc-dzień, np. 03-15 dla 15 marca)'
                    ])->label('Początek wysiewu <span class="text-danger">*</span>', ['encode' => false]) ?>
                    
                    <?= $form->field($model, 'sowing_end')->textInput([
                        'placeholder' => 'MM-DD (np. 05-30)',
                        'pattern' => '(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])',
                        'class' => 'form-control',
                        'required' => true,
                        'maxlength' => 5,
                        'title' => 'Format: MM-DD (miesiąc-dzień, np. 05-30 dla 30 maja)'
                    ])->label('Koniec wysiewu <span class="text-danger">*</span>', ['encode' => false]) ?>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Format:</strong> MM-DD (miesiąc-dzień)<br>
                            <strong>Przykłady:</strong><br>
                            • <code>03-15</code> = 15 marca<br>
                            • <code>05-30</code> = 30 maja<br>
                            • <code>12-01</code> do <code>02-28</code> = grudzień-luty<br>
                            <small class="text-muted">Okres może przechodzić przez nowy rok.</small>
                        </small>
                    </div>

                    <?= $form->field($model, 'expiry_date')->input('date', [
                        'class' => 'form-control'
                    ]) ?>
                    
                    <?= $form->field($model, 'purchase_year')->textInput([
                        'type' => 'number', 
                        'min' => 2000, 
                        'max' => date('Y') + 1,
                        'placeholder' => 'Rok zakupu',
                        'class' => 'form-control'
                    ]) ?>
                    
                    <?= $form->field($model, 'status')->dropDownList(
                        $model->getStatusOptions(),
                        ['class' => 'form-select']
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <div class="btn-group btn-group-lg" role="group">
                        <?= Html::submitButton(
                            $model->isNewRecord ? '<i class="bi bi-plus-circle me-2"></i>Dodaj nasiona' : '<i class="bi bi-check-circle me-2"></i>Zapisz zmiany', 
                            [
                                'class' => $model->isNewRecord ? 'btn btn-success btn-lg' : 'btn btn-primary btn-lg',
                                'encode' => false
                            ]
                        ) ?>
                        
                        <?= Html::a(
                            '<i class="bi bi-x-circle me-2"></i>Anuluj', 
                            $model->isNewRecord ? ['/seed/index'] : ['view', 'id' => $model->id], 
                            [
                                'class' => 'btn btn-secondary btn-lg',
                                'encode' => false
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
function updatePriorityPreview(value) {
    const preview = document.getElementById('priority-preview');
    preview.textContent = value;
    
    // Usuń wszystkie klasy priority
    preview.className = 'priority-badge';
    
    // Dodaj odpowiednią klasę na podstawie wartości
    if (value >= 8) {
        preview.classList.add('priority-high');
    } else if (value >= 5) {
        preview.classList.add('priority-medium');
    } else if (value > 0) {
        preview.classList.add('priority-low');
    } else {
        preview.classList.add('priority-none');
    }
}

// Inicjalizuj podgląd priorytetu przy ładowaniu strony
document.addEventListener('DOMContentLoaded', function() {
    const priorityInput = document.querySelector('input[name="Seed[priority]"]');
    if (priorityInput) {
        updatePriorityPreview(priorityInput.value || 0);
    }
    
    // Dodaj walidację po stronie klienta dla dat MM-DD
    const sowingInputs = document.querySelectorAll('input[name="Seed[sowing_start]"], input[name="Seed[sowing_end]"]');
    sowingInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateMonthDayInput(this);
        });
        
        input.addEventListener('blur', function() {
            formatMonthDayInput(this);
        });
    });
});

function validateMonthDayInput(input) {
    const value = input.value;
    const pattern = /^(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/;
    
    // Usuń poprzednie ostrzeżenia
    const existingError = input.parentNode.querySelector('.validation-error');
    if (existingError) {
        existingError.remove();
    }
    
    if (value && !pattern.test(value)) {
        input.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback validation-error';
        errorDiv.textContent = 'Format: MM-DD (np. 03-15)';
        input.parentNode.appendChild(errorDiv);
    } else {
        input.classList.remove('is-invalid');
    }
}

function formatMonthDayInput(input) {
    let value = input.value.replace(/[^\d-]/g, ''); // Usuń wszystko oprócz cyfr i myślnika
    
    // Automatyczne formatowanie podczas pisania
    if (value.length === 3 && value.indexOf('-') === -1) {
        value = value.slice(0, 2) + '-' + value.slice(2);
    }
    
    // Dodaj zera wiodące jeśli potrzeba
    if (value.length === 5) {
        const parts = value.split('-');
        if (parts.length === 2) {
            const month = parts[0].padStart(2, '0');
            const day = parts[1].padStart(2, '0');
            value = month + '-' + day;
        }
    }
    
    input.value = value;
    validateMonthDayInput(input);
}
</script>