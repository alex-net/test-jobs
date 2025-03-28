<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * модель записи лога посечений ссылок
 *
 * @property int $uid Ссылка на запрашиваемый url
 * @property long $ip числовое представление IP клиента
 * @property int $time Время запроса
 */
class UrlRedirect extends ActiveRecord
{
    public function rules()
    {
        return [
            [['uid', 'ip', 'time'], 'integer']
        ];
    }
}