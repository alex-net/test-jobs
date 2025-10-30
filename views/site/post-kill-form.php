<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\ButtonGroup;

$form = ActiveForm::begin() ?>
Запись "<?= Yii::$app->formatter->asTeaser($model->message) ?>" от <?= Yii::$app->formatter->asDateTime($model->created) ?> будет удалена. Продолжить?<br>
    <?= ButtonGroup::widget([
        'buttons' => [
            ['label' => 'Удалить', 'options' => ['class' => 'btn btn-danger', 'type' => 'submit']],
            Html::a('Отмена', ['index'], ['class' => 'btn btn-primary']),
        ]
    ])?>
<?php ActiveForm::end() ?>