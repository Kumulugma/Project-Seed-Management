<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

?>

<div class="seed-form">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate' => true],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{hint}\n{error}",
        ],
    ]); ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Podstawowe informacje
                    </h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'np. Pomidor Malinowy Ożarowski',
                        'class' => 'form-control',
                        'required' => true
                    ])->label('Nazwa nasion <span class="text-danger">*</span>', ['encode' => false]) ?>
                    
                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 3,
                        'placeholder' => 'Opcjonalny opis odmiany, charakterystyka, uwagi...',
                        'class' => 'form-control'
                    ]) ?>
                    
                    <?= $form->field($model, 'imageFile')->fileInput([
                        'accept' => 'image/*',
                        'class' => 'form-control'
                    ])->label('Zdjęcie opakowania') ?>
                    
                    <?php if ($model->image_path): ?>
                        <div class="mb-3">
                            <label class="form-label">Obecne zdjęcie:</label>
                            <div class="mt-2">
                                <?= Html::img($model->getImageUrl(), [
                                    'class' => 'img-thumbnail',
                                    'style' => 'max-width: 200px; max-height: 200px;'
                                ]) ?>
                                <p class="form-text mt-2">Wybierz nowe zdjęcie, aby zastąpić obecne</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-flower2 me-2"></i>Charakterystyka rośliny
                    </h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'type')->dropDownList(
                        $model->getTypeOptions(), 
                        ['prompt' => 'Wybierz typ rośliny', 'class' => 'form-select', 'required' => true]
                    )->label('Typ <span class="text-danger">*</span>', ['encode' => false]) ?>
                    
                    <?= $form->field($model, 'height')->dropDownList(
                        $model->getHeightOptions(), 
                        ['prompt' => 'Wybierz wysokość rośliny', 'class' => 'form-select', 'required' => true]
                    )->label('Wysokość <span class="text-danger">*</span>', ['encode' => false]) ?>
                    
                    <?= $form->field($model, 'plant_type')->dropDownList(
                        $model->getPlantTypeOptions(), 
                        ['prompt' => 'Wybierz typ rośliny', 'class' => 'form-select', 'required' => true]
                    )->label('Typ rośliny <span class="text-danger">*</span>', ['encode' => false]) ?>
                    
                    <div class="mb-3">
                        <?= $form->field($model, 'priority')->textInput([
                            'type' => 'number', 
                            'min' => 0, 
                            'max' => 10,
                            'placeholder' => '0-10',
                            'class' => 'form-control priority-input'
                        ])->hint('0 = najniższy priorytet, 10 = najwyższy priorytet') ?>
                        <div class="mt-2">
                            <span class="priority-badge priority-none">0</span>
                            <small class="text-muted ms-2">Podgląd priorytetu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3 me-2"></i>Okresy i daty
                    </h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'sowing_start')->input('date', [
                        'class' => 'form-control',
                        'required' => true
                    ])->label('Początek wysiewu <span class="text-danger">*</span>', ['encode' => false]) ?>
                    
                    <?= $form->field($model, 'sowing_end')->input('date', [
                        'class' => 'form-control',
                        'required' => true
                    ])->label('Koniec wysiewu <span class="text-danger">*</span>', ['encode' => false]) ?>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Wskazówka:</strong> Okres wysiewu może przechodzić przez nowy rok 
                            (np. 01.12 - 28.02 dla nasion wysiewanych zimą).
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-box me-2"></i>Informacje o opakowaniu
                    </h5>
                </div>
                <div class="card-body">
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
                            $model->isNewRecord ? ['index'] : ['view', 'id' => $model->id], 
                            ['class' => 'btn btn-outline-secondary btn-lg', 'encode' => false]
                        ) ?>
                        
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::a(
                                '<i class="bi bi-eye me-2"></i>Zobacz', 
                                ['view', 'id' => $model->id], 
                                ['class' => 'btn btn-info btn-lg', 'encode' => false]
                            ) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-suggestions based on plant type
    const typeSelect = document.getElementById('seed-type');
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            const type = this.value;
            const suggestions = {
                'vegetables': { height: 'high', plant_type: 'annual', priority: 7 },
                'flowers': { height: 'low', plant_type: 'annual', priority: 5 },
                'herbs': { height: 'low', plant_type: 'perennial', priority: 6 }
            };
            
            if (suggestions[type] && confirm('Czy chcesz zastosować typowe ustawienia dla typu: ' + type + '?')) {
                document.getElementById('seed-height').value = suggestions[type].height;
                document.getElementById('seed-plant_type').value = suggestions[type].plant_type;
                const priorityInput = document.getElementById('seed-priority');
                priorityInput.value = suggestions[type].priority;
                priorityInput.dispatchEvent(new Event('change'));
            }
        });
    }
    
    // Image preview
    const imageInput = document.getElementById('seed-imagefile');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const existingPreview = document.getElementById('image-preview');
                    if (existingPreview) existingPreview.remove();
                    
                    const preview = document.createElement('div');
                    preview.id = 'image-preview';
                    preview.className = 'mt-3';
                    preview.innerHTML = `
                        <label class="form-label">Podgląd nowego zdjęcia:</label>
                        <div>
                            <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    `;
                    imageInput.parentNode.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Priority badge update
    const priorityInput = document.getElementById('seed-priority');
    if (priorityInput) {
        priorityInput.addEventListener('input', function() {
            updatePriorityBadge(this);
        });
        
        // Initial update
        updatePriorityBadge(priorityInput);
    }
    
    function updatePriorityBadge(input) {
        const value = parseInt(input.value) || 0;
        const badge = input.closest('.card-body').querySelector('.priority-badge');
        
        if (badge) {
            badge.textContent = value;
            badge.className = 'priority-badge';
            
            if (value >= 8) {
                badge.classList.add('priority-high');
            } else if (value >= 5) {
                badge.classList.add('priority-medium');
            } else if (value > 0) {
                badge.classList.add('priority-low');
            } else {
                badge.classList.add('priority-none');
            }
        }
    }
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>