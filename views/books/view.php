<?php

use yii\widgets\DetailView;

$this->title = 'Книжка '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Все книжки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'name', 'isbn', 'descr', 'year', 'authors:authorsList:Авторы', 'cover:image:обложка'
    ],
]);