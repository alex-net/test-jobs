<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\grid\ActionColumn;

$this->title = 'Треки';

echo Html::a('Добавить трек', ['add'], ['class' => ['btn', 'btn-primary']]);
echo GridView::widget([
    'dataProvider' => $dp,
    'columns' => [
        'id',
        'track_number',
        [
            'attribute' => 'status',
            'value' => 'statusName'
        ],
        [
            'attribute' => 'changes',
            'content' => fn($m) => Html::a($m->changes, ['changes', 'id' => $m->id]),
        ],
        'created_at',
        'updated_at',
        [
            'class' => ActionColumn::class,
        ]
    ],
]);