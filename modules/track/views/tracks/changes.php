<?php

use yii\grid\GridView;

$this->title = sprintf('История изменений трека "%s"', $model->track_number);

$this->params['breadcrumbs'][] = ['label' => 'Все треки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => sprintf('Просмотр трека "%s"', $model->track_number), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

echo GridView::widget([
    'dataProvider' => $dp,
    'columns' => [
        'changed_at',
        'field',
        'val_old',
        'val_new',
    ],
]);