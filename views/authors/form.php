<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = $model->isNewRecord ? 'Новый автор' : 'Редактиование автора';
$this->params['breadcrumbs'][] = ['label' => 'Все авторы', 'url' => ['index']];

$f = ActiveForm::begin();
echo $f->field($model, 'fio', ['inputOptions' => ['autofocus' => true]]);

echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();
