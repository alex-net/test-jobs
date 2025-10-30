<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;

$form = ActiveForm::begin() ?>
    <?php if (empty($editOnly)): ?>
        <?= $form->field($model, 'author') ?>
        <?= $form->field($model, 'mail') ?>
    <?php endif ?>
    <?= $form->field($model, 'message')->textarea() ?>
    <?= Captcha::widget([
        'model' => $model,
        'attribute' => 'captcha',
        'template' => '<div class="form-group">{image} {input}</div>',
    ]);
    ?>
    <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>