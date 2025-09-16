<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

$this->title = sprintf('Просмотр трека "%s"', $model->track_number);
$this->params['breadcrumbs'][] = ['label' => 'Все треки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'track_number',
        [
            'attribute' => 'status',
            'value' => 'statusName'
        ],
        'created_at',
        'updated_at',
        [
            'attribute' => 'changes',
            'format' => 'raw',
            'value' => fn($m) => Html::a($m->changes, ['changes', 'id' => $m->id]),
        ]
    ],
]);