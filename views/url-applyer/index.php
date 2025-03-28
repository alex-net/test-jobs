<?php

use yii\grid\GridView;
use yii\bootstrap5\Html;

$this->title = 'Сохранённые ссылки';

echo GridView::widget([
    'dataProvider' => $dp,
    'columns' => [
        'id', 'url:url', 'urlHash',
        [
            'label' => 'Переходы',
            'content' => fn($m) => Html::a(count($m->redirects), ['detail', 'id' => $m->id]) ,

        ]
    ],
]);