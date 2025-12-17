<?php

use yii\grid\GridView;
use yii\bootstrap5\Html;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;

$this->title = 'Книги';

if (!Yii::$app->user->isGuest) {
    echo Html::a('Добавить книгу', ['add'], ['class' => 'btn btn-primary']);
}

echo GridView::widget([
    'options' => ['class' => ['mt-3']],
    'dataProvider' => $dp,
    'columns' => [
        ['class' => SerialColumn::class],
        'name', 'year', 'isbn',
        [
            'class' => ActionColumn::class,
            'template' => Yii::$app->user->isGuest ? '{view}' : '{view} {update} {delete}',
        ],
    ],
]);