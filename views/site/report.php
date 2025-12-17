<?php

use yii\grid\GridView;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Отчёт';
if ($year) {
    $this->title .= sprintf(' за %d год', $year);
}

$f = ActiveForm::begin(['method' => 'get', 'action' => ['report']]);
?>

<?= $f->field($filter, 'year');?>
<?= Html::submitButton('Применить', ['class' => 'btn btn-primary']);?>
<?php ActiveForm::end();?>

<?= GridView::widget([
    'dataProvider' => $filter->results,
    'columns' => [
        'id',
        'fio',
        [
            'label' => 'Число книг',
            'content' => fn($m) => count(array_filter($m->books, fn($b) => $year ? $b->year == $year : true )) ,
        ],

    ],
]);?>