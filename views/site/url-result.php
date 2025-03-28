<?php

use yii\bootstrap5\Html;
use yii\helpers\Url;
use splitbrain\phpQRCode\QRCode;
$url = Url::to(['url-applyer/redirect', 'hash' => $model->urlHash], true);
?>

<div class="alert alert-success" role="alert">
  <?= Html::a('Ссылка', $model->url, ['target' => '_black']) ?> добавлеена в базу как <?= Html::a($url, $url, ['target' => '_black']) ?>
</div>
<div class="text-center" style="width:200px; margin: 0 auto;">
    <?= QRCode::svg($url) ?>
</div>
