<?php

use app\widgets\MarkdownWidget;

$this->title = 'Тестовое задание';

echo MarkdownWidget::widget(['fileMarkdownPath' => '@app/TZ.md']);