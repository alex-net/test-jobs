<?php

namespace app\components;

use yii\i18n\Formatter as YiiFormatter;
use yii\helpers\Inflector;
use Vehsamrak\Phpluralize\Pluralizer;




class Formatter extends YiiFormatter
{
    /**
     * формтирование ip
     * @param  string $ip
     * @return string
     */
    public function asHiddenIp($ip)
    {
        // определить тип IP
        if (strpos($ip, '.')) { // v4
            return implode('.', array_pad(array_slice(explode('.', $ip), 0, 2), 4, '**'));
        }
        // v6
        return implode(':', array_map(fn($x)=> ltrim($x, '0'), array_pad(array_slice(explode(':', $ip), 0, 4), 8, '****')));
    }

    /**
     * форматирование количества постов
     * @param  int $n Число постов
     * @return string
     */
    public function asPostsCount($n, $words = [])
    {
        $pluralizer = new Pluralizer();
        $words = array_pad($words, 3, '');
        array_unshift($words, $n);
        return sprintf('%d %s', $n, call_user_func_array([$pluralizer, 'pluralize'], $words));
    }

    public function asTeaser($text, $max = 20)
    {
        $text = htmlspecialchars($text);
        $endedBy = strlen($text) > $max ? '...': '';
        return substr($text, 0, min($max, strlen($text))) . $endedBy;
    }
}