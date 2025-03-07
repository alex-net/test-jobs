<?php

use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\bootstrap5\Html;
use bestyii\bootstrap\icons\assets\BootstrapIconAsset;

BootstrapIconAsset::register($this);

if (Yii::$app->user->id == $model->id) {
    $this->title = 'Профиль';
} else {
    $this->params['breadcrumbs'][] = ['label' => 'Все пользователи', 'url' => ['index']];
    $this->title = $model->fullName . ' (Просмотр)';
}

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'fullName',
        'email',
        'active',
        'created:date',
        ['attribute' => 'note', 'visible' => Yii::$app->user->can(Yii::$app->user->identity::ADMIN_ROLE),],
    ],
]);

echo Html::tag('h4', 'Счета');
echo GridView::widget([
    // 'caption' => 'Счета',
    'dataProvider' => $model->accountsList,
    'columns' => [
        'id:raw',
        'created:datetime',
        'balance:decimal',
        [
            'class' => ActionColumn::class,
            'visible' => Yii::$app->user->id == $model->id,
            'template' => '{view-account}',
            'buttons' => [
                'view-account' => function($url, $model, $key) {
                    return Html::a('', ['view-account', 'aid' => $model->id], ['title' => 'посмотреть платежи', 'class' => 'bi bi-cash-stack']);
                }
            ],
        ]
    ],
]);