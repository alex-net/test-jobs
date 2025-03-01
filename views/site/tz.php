<?php

use app\widgets\MarkdownWidget;

$this->title = 'Тестовое задание о коробках и товарах';

echo MarkdownWidget::widget(['fileMarkdownPath' => '@app/TZ.md']);