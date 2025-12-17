<?php

namespace app\components;

use yii\i18n\Formatter;
use yii\helpers\Html;
use Yii;

/**
 * расширение класса Formatter ..
 */
class SiteFormatter extends Formatter
{
    /**
     * вывод списка авторов в виде ссылок через запятую
     * @param  Author[] $arr   Массив для вывода сущностей Author
     * @param  string $sepor Разделитель
     * @return string
     */
    public function asAuthorsList($arr, $sepor = ', ')
    {
        $list = [];
        foreach ($arr as $author) {
            $list[] = Html::a($author->fio, ['authors/view', 'id' => $author->id]);
        }

        return implode($sepor, $list);
    }
}