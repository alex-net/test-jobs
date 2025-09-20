<?php

namespace app\components;

use yii\base\Action;
use yii\helpers\Html;
use Yii;
use app\widgets\MarkdownWidget;


class MarkdownAction extends Action
{
    public $title;
    public $mdPath;

    public function run()
    {
        Yii::$app->view->title = $this->title;
        return $this->controller->renderContent(Html::tag('h1', $this->title). MarkdownWidget::widget([
            'fileMarkdownPath' => $this->mdPath,
        ]));
    }
}