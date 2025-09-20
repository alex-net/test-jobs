<?php

use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use yii\bootstrap5\Html;
use app\models\Author;
use app\models\User;

$oClass = "app\\models\\" . ucfirst($this->context->action->id);
$actionId = $this->context->action->id;

$this->title = sprintf('Все %s пользователя', mb_strtolower($oClass::NAMES[1]));
if (Yii::$app->user->can(User::ROLE_ADMIN)) {
    echo Html::a('Добавть ' . $oClass::NAMES[0], ['new-' . $actionId], ['class' => ['btn', 'btn-primary']]);
}
echo GridView::widget([
    'dataProvider' => $oClass::getList(Yii::$app->request->get('uid')),
    'pager' => [
        'class' => \yii\bootstrap5\LinkPager::class,
    ],
    'columns' => [
        ['class' => SerialColumn::class],
        [
            'attribute' => 'user.login',
            'label' => 'Владелец',
            'visible' => Yii::$app->user->can(User::ROLE_ADMIN),
        ],
        [
            'attribute' => 'name',
            'label' => 'Наименование',
            'format' => 'html',
            'value' => fn($m) => Html::a($m->name, [$this->context->action->id, 'id' => $m->id]),
        ],
        [
            'content' => function($m) {
                $count = count($m->{$m::COUNT_PARAMS[0]});
                if (Yii::$app->user->can(User::ROLE_ADMIN)) {
                    $count = Html::a($count, ['binder-' . $m::COUNT_PARAMS[0], 'id' => $m->id]);
                }
                return $count;
            },
            'label' => $oClass::COUNT_PARAMS[1],
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{update} {delete}',
            'urlCreator' => fn($action, $model) => $action . '-' . $actionId.'?id='. $model->id,
            'visible' => Yii::$app->user->can(User::ROLE_ADMIN),
        ]
    ],
]);