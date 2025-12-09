<?php

use yii\grid\GridView;
use yii\bootstrap5\Html;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;

$this->title = 'Авторы';

echo Html::a('Добавить автора', ['add'], ['class' => 'btn btn-primary']);

echo GridView::widget([
    'options' => ['class' => ['mt-3']],
    'dataProvider' => $dp,
    'columns' => [
        ['class' => SerialColumn::class],
        'fio',
        ['class' => ActionColumn::class],

    ],
]);