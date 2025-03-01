<?php

namespace app\components;

use yii\base\Action;

class SimplePageAction extends Action
{
    public function run()
    {
        return $this->controller->render($this->id);
    }
}