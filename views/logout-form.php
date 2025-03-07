<?php
use yii\bootstrap5\Html;
?>
<?= Html::beginForm(['/users/logout'])?>
<?= Html::submitButton('Выход', ['class' => 'dropdown-item btn btn-link logout']) ?>
<?= Html::endForm()?>