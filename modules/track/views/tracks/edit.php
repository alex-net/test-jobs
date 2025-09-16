<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = $model->isNewRecord ? 'Новый трек' : sprintf('Редактирование трека "%s"', $model->track_number);

$this->params['breadcrumbs'][] = ['label' => 'Все треки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$f = ActiveForm::begin();?>
<div class="row mb-2">
    <?= $f->field($model, 'track_number', ['options' => ['class' => 'col']]);?>
    <?= $f->field($model, 'status', ['options' => ['class' => 'col']])->dropDownlist($model::STATUSES, ['prompt' => '-- Не указано --']);?>
</div>
<?= Html::submitButton('Сохранить', ['class' => ['btn', 'btn-primary']]); ?>

<?php ActiveForm::end();?>