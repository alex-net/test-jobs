<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = $model->isNewRecord ? 'Новый пользователь' : $model->fullName . ' (Редактирование)';

$this->params['breadcrumbs'][] = ['label' => 'Все пользователи', 'url' => ['index']];

$f = ActiveForm::begin();?>

<div class="row">
    <?= $f->field($model, 'email', ['options' => ['class' => 'col']]) ?>
    <?= $f->field($model, 'fullName', ['options' => ['class' => 'col']]) ?>
    <?= $f->field($model, 'password', ['options' => ['class' => 'col']])->passwordInput() ?>
</div>
<?= $f->field($model, 'note')->textarea() ?>

<div class="row">
    <?= $f->field($model, 'active', ['options' => ['class' => 'col']])->checkbox() ?>
    <?= $f->field($model, 'isAdmin', ['options' => ['class' => 'col']])->checkbox() ?>
</div>
<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end();?>
