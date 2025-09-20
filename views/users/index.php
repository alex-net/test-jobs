<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\bootstrap5\Html;

$this->title = 'Пользователи';

echo GridView::widget([
    'dataProvider' => $dp,
    'columns' => [
        'id', 'login', 'role',
        [
            'attribute' => 'books',
            'label' => 'Книги',
            'format' => 'html',
            'value' => fn($m) => Html::a(count($m->books), ['content-render/book', 'uid' => $m->id]),
        ],
        [
            'attribute' => 'authors',
            'label' => 'Авторы',
            'format' => 'html',
            'value' => fn($m) => Html::a(count($m->authors), ['content-render/author', 'uid' => $m->id])
        ],
    ],
]);