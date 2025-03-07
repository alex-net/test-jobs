<?php

namespace app\controllers;

use yii\web\Controller;
use app\components\ExternalPaymentSystem;

class ExternalPaymentSystemController extends Controller
{
    public function actionIndex()
    {
        $extSystem = new ExternalPaymentSystem();
        return $this->asJson($extSystem->generate());
    }
}