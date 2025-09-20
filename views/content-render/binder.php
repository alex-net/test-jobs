<?php

use yii\widgets\DetailView;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;


use app\models\Author;
use app\models\Book;
use app\models\User;


if ($model instanceof Book) {
    $this->title = 'Назначение авторов для книги '. $model->title;
} else {
    $this->title = 'Назначение книг для автора '. $model->full_name;
}

$this->params['breadcrumbs'][] = ['label' => 'Все книги', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'name',
        [
            'attribute' => 'uid',
            'visible' => Yii::$app->user->can(User::ROLE_ADMIN),
        ]
    ],
]);?>

<?php $f = ActiveForm::begin();?>
<div class="form-group">
    <?= Html::label($lClass::NAMES[1], 'binders', ['class' => 'form-label']);?>
    <?= Html::dropDownList('binders', $ddSelected, $ddList, ['multiple' => true, 'prompt' => 'Не указано', 'class' => 'form-control', 'size' => 10]);?>
</div>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);?>
<?php ActiveForm::end(); ?>
