<?php

use yii\grid\GridView;

$this->title = 'Автор ' . $model->fio;

$this->params['breadcrumbs'][] = ['label' => 'Все авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


echo GridView::widget([
    'caption' => 'Все книги автора',
    'dataProvider' => $model->booksDp,
    'columns' => ['name', 'isbn', 'year', 'authors:authorsList:Авторы'],

]);
