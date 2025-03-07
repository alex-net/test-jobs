<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\bootstrap5\Html;

$this->title = 'Пользователи';

echo Html::a('Новый пользователь', ['update'], ['class' => 'btn btn-success']);

echo GridView::widget([
    'dataProvider' => $dp,
    'columns' => [
        'id',
        'email',
        'active:boolean',
        'isAdmin:boolean',
        ['attribute' => 'accounts', 'label' => 'Число счетов', 'value' => fn($m) => count($m->accounts) ],
        ['class' => ActionColumn::class]
    ],
]);