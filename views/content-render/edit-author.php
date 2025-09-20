<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\models\User;

$this->title = $model->isNewRecord ? 'Новый автор' : 'Редактирование автора';

$this->params['breadcrumbs'][] = ['label' => 'Все авторы', 'url' => ['author']];
$this->params['breadcrumbs'][] = $this->title;

$f = ActiveForm::begin();?>

<?= $f->field($model, 'full_name') ?>
<?= $f->field($model, 'uid')->dropDownList(User::asList()) ?>

<?= Html::submitButton('Сохранить', ['class' => ['btn', 'btn-primary']]) ?>

<?php ActiveForm::end() ?>