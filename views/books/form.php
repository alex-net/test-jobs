<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->registerJsFile('@web/js/book-form.js', ['depends' => [
    \app\assets\AppAsset::class,
    \app\assets\Select2Asset::class,
]]);

$this->title = $model->isNewRecord ? 'Новая книга' : 'Редактиование книги';
$this->params['breadcrumbs'][] = ['label' => 'Все книги', 'url' => ['index']];

$f = ActiveForm::begin();?>
<div class="row mb-3">
    <?= $f->field($model, 'name', ['options' => ['class' => 'col']]); ?>
    <?= $f->field($model, 'year', ['options' => ['class' => 'col']])->input('number', ['min' => 0]); ?>
    <?= $f->field($model, 'isbn', ['options' => ['class' => 'col']]); ?>
</div>
<div class="row mb-3">
    <?= $f->field($model, 'authorList', ['options' => ['class' => 'col']])->dropDownList(array_map(fn($el) => $el->fio, $model->authors) , ['multiple' => 'multiple']) ?>
    <?= $f->field($model, 'descr', ['options' => ['class' => 'col']])->textarea(); ?>
    <div class="col">
        <?php if ($model->cover): ?>
            <?= Html::img($model->cover, ['width' => 100]) ?>
        <?php endif ?>
        <?= $f->field($model, 'image')->fileinput(); ?>
    </div>

</div>


<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
<?php ActiveForm::end();?>
