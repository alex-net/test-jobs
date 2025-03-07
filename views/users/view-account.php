<?php

use yii\grid\GridView;

$this->title = 'Просмотр счёта ' . $acc->id;

$this->params['breadcrumbs'][] = [
    'label' => 'Профиль',
    'url' => ['users/profile'],
];
$this->params['breadcrumbs'][] = $this->title;

echo GridView::widget([
    'dataProvider' => $operations,
    'columns' => [
        'created:datetime',
        'amount:decimal',
        'transId:raw',
    ],
]);