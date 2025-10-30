<div class="card-body">
    <h5 class="card-title"><?= $model->author ?></h5>
    <p><?= $model->message ?></p>
    <p>
        <small class="text-muted">
            <?= Yii::$app->formatter->asRelativeTime($model->created) ?> | <?= Yii::$app->formatter->asHiddenIp($model->ip) ?> | <?= Yii::$app->formatter->asPostsCount(count($model->countAll), ['пост', 'поста', 'постов'])?>
        </small>
    </p>
</div>
