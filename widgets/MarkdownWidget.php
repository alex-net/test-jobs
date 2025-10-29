<?php

namespace app\widgets;

use yii\base\Widget;
use Parsedown;
use Yii;

/**
 * Виджет генерирует контент из файлов markdown
 */
class MarkdownWidget extends Widget
{
    /**
     * путь к файлу в формате Markdown для отображения
     * @var stinng
     */
    public $fileMarkdownPath;

    public function run()
    {
        $path = Yii::getAlias($this->fileMarkdownPath);
        if (!file_exists($path)) {
            Yii::warning("file not found '{$this->fileMarkdownPath}'");
            return '';
        }
        $md = new Parsedown();
        // чтение контента
        $content = file_get_contents($path);
        // замена путей картинок. Картинки должны лежать в ./web
        $content = preg_replace('#(\!\[.*?\])\(web(.*?)\)#m', '$1($2)', $content);
        return $md->text($content);
    }
}