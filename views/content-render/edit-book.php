<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\models\User;

$this->title = $model->isNewRecord ? 'Новая книга' : 'Редактирование книги';

$this->params['breadcrumbs'][] = ['label' => 'Все книги', 'url' => ['book']];
$this->params['breadcrumbs'][] = $this->title;

$f = ActiveForm::begin();?>

<?= $f->field($model, 'title') ?>
<?= $f->field($model, 'uid')->dropDownList(User::asList()) ?>

<?= Html::submitButton('Сохранить', ['class' => ['btn', 'btn-primary']]) ?>

<?php ActiveForm::end() ?>