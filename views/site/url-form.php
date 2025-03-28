<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Ссылка для сохранения';
$form = ActiveForm::begin([
    'options' => ['class' => ['url-sender']],
    'enableClientScript' => false,
] );
?>

<div class="row">
    <?= $form->field($model, 'url', [
        'options' => ['class' => ['col', 'form-group']],
        'inputOptions' => ['class' => ['form-control'], 'placeholder' => $model->getAttributeLabel('url')],
    ])->label(false) ?>
    <?= Html::submitButton('Сохранить', ['class' => ['btn', 'btn-primary', 'col-2', 'form-group']]) ?>
</div>

<?php ActiveForm::end() ?>