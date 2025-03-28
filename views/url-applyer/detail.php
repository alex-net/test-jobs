<?php

use yii\grid\GridView;
use yii\grid\SerialColumn;

$this->title = 'Детали по ссылке '. $model->url;

$this->params['breadcrumbs'][] = ['label' => 'Все ссылки', 'url' => ['index']];

echo GridView::widget([
    'dataProvider' => $dp,
    'columns' => [
        ['class' => SerialColumn::class],
        ['attribute' => 'ip', 'content' => fn($m) => long2ip($m->ip)],
        ['attribute' => 'time', 'content' => fn($m) => date('c', $m->time), 'label' => 'Время визита'],

    ],
]);