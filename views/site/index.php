<?php

/** @var yii\web\View $this */

use yii\widgets\ListView;

$this->title = 'StoryValut';
?>
<div class="site-index row">
    <div class="col-6">
        <?= ListView::widget([
            'dataProvider' => $model::list(),
            'itemView' => 'sv-teaser',
            'itemOptions' => ['class' => 'card card-default my-2'],
        ]) ?>
    </div>
    <div class="col-6">
        <?= $this->render('post-edit-form', ['model' => $model])?>
    </div>
</div>
