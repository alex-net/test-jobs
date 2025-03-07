<?php

namespace app\components;

use yii\base\Behavior;
use Yii;
use app\models\Operation;

/**
 * поведение для работы с сигнатурой даннных и транзакциями
 */
class SignaturaDeectBehavior extends Behavior
{
    /**
     * Генерирует сигнаруру на основе полученных данных
     * @param  array $data Входные данные для генерации сигнатуры
     * @return string       сигнатура
     */
    public function detectSignatura($data)
    {
        $s = '';
        foreach (['account_id', 'amount', 'transaction_id', 'user_id'] as $key) {
            $s .= $data[$key] ?? '';
        }
        $s .= ENV_DATA['EXTERNAL_SYSTEM_SECRET_KEY'] ?? '';
        return hash('sha256', $s);
    }

    /**
     * генерация случайного номера транзакции
     * @return string
     */
    public function getTransactionId()
    {
        do {
            preg_match('#^(.{8})(.{4})(.{4})(.{4})(.{12})$#', md5(Yii::$app->security->generateRandomKey()), $m);
            array_shift($m);
            $id = implode('-', $m);
        } while (Operation::existTransId($id));
        return $id;
    }
}