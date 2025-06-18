<?php
/**
 * LOKALIZACJA: views/seed/_form.php
 * POPRAWIONY FORMULARZ Z POLAMI MM-DD I COMPANY BEZ BLOKOWANIA
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
        'enableAjaxValidation' => false,
        'enableClientValidation' => false, // WYŁĄCZONE - to powodowało blokowanie
        'validateOnSubmit' => false,
        'validateOnChange' => false,
        'validateOnBlur' => false,
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

                    <?= $form->field($model, 'company')->textInput([
                        'maxlength' => true, 
                        'placeholder' => 'Nazwa firmy/producenta (np. Vilmorin, Torseed, W. Legutko)',
                        'class' => 'form-control',
                        'list' => 'company-suggestions'
                    ])->label('Firma/Producent') ?>
                    
                    <!-- Datalist dla podpowiedzi firm -->
                    <datalist id="company-suggestions">
                        <?php foreach (\app\models\Seed::getCompanyOptions() as $company): ?>
                            <option value="<?= Html::encode($company) ?>">
                        <?php endforeach; ?>
                    </datalist>

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

            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Charakterystyka rośliny
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'type')->dropDownList(
                                $model->getTypeOptions(),
                                [
                                    'class' => 'form-select',
                                    'prompt' => 'Wybierz typ...'
                                ]
                            )->label('Typ <span class="text-danger">*</span>', ['encode' => false]) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'height')->dropDownList(
                                $model->getHeightOptions(),
                                [
                                    'class' => 'form-select',
                                    'prompt' => 'Wybierz wysokość...'
                                ]
                            )->label('Wysokość rośliny <span class="text-danger">*</span>', ['encode' => false]) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'plant_type')->dropDownList(
                                $model->getPlantTypeOptions(),
                                [
                                    'class' => 'form-select',
                                    'prompt' => 'Wybierz typ rośliny...'
                                ]
                            )->label('Typ rośliny <span class="text-danger">*</span>', ['encode' => false]) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Priorytet (0-10)</label>
                            <div class="d-flex align-items-center gap-3">
                                <?= $form->field($model, 'priority', ['template' => '{input}'])->textInput([
                                    'type' => 'range',
                                    'min' => 0,
                                    'max' => 10,
                                    'value' => $model->priority ?: 0,
                                    'class' => 'form-range',
                                    'oninput' => 'updatePriorityPreview(this.value)'
                                ]) ?>
                                <span id="priority-preview" class="priority-badge priority-none">0</span>
                            </div>
                            <small class="text-muted">Wyższy priorytet = ważniejsze nasiona</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event me-2"></i>Okresy i daty
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

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-image me-2"></i>Zdjęcie opakowania
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($model->image_path && file_exists(Yii::getAlias('@webroot') . '/uploads/' . $model->image_path)): ?>
                        <div class="text-center mb-3">
                            <img src="/uploads/<?= Html::encode($model->image_path) ?>" 
                                 class="img-thumbnail" 
                                 style="max-height: 200px; max-width: 100%;"
                                 alt="Aktualne zdjęcie">
                            <p class="small text-muted mt-2">Aktualne zdjęcie</p>
                        </div>
                    <?php endif; ?>

                    <?= $form->field($model, 'imageFile')->fileInput([
                        'class' => 'form-control',
                        'accept' => 'image/*'
                    ])->label('Nowe zdjęcie') ?>
                    
                    <small class="text-muted">
                        Obsługiwane formaty: JPG, PNG, GIF<br>
                        Maksymalny rozmiar: 2MB
                    </small>
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
    if (!preview) return;
    
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
    
    // Walidacja dat MM-DD (nieblokująca)
    const sowingInputs = document.querySelectorAll('input[name="Seed[sowing_start]"], input[name="Seed[sowing_end]"]');
    sowingInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateMonthDayInput(this);
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
    
    input.classList.remove('is-invalid');
    
    if (value && !pattern.test(value)) {
        input.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback validation-error';
        errorDiv.textContent = 'Format: MM-DD (np. 03-15)';
        input.parentNode.appendChild(errorDiv);
    }
}
</script>