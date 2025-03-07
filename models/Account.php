<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Expression;


/**
 * Класс счёта пользователя
 *
 * @property int $id Первичный ключ
 * @property int $uid Ссылка на пользователя
 * @property int $created Дата создания счёта
 * @property double $balance Баланс счёта
 */
class Account extends ActiveRecord
{
    /**
     * сценарий для обновления (пересчёта) баланса счёта
     */
    const SCENARIO_BALANCE_UPD = 'balance update';

    public function rules()
    {
        return [
            [['id', 'created'], 'integer'],
            ['uid', 'required'],
            ['created', 'default', 'value' => time()],
            ['uid', 'exist', 'targetRelation' => 'user'],
            ['balance', 'double', 'on' => static::SCENARIO_BALANCE_UPD],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№ счёта',
            'created' => 'Создан',
            'balance' => 'Сумма на счёте',
        ];
    }

    /**
     * получение связанного со счётом пользователья
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'uid']);
    }

    /**
     * получение связанных со счётом операций
     * @return ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operation::class, ['aid' => 'id']);
    }

    /**
     * Пересчёт баланса по связанным операциям ..
     */
    public function refreshBlance()
    {
        $this->scenario = static::SCENARIO_BALANCE_UPD;
        $this->balance = $this->getOperations()->sum('amount');
        $this->save();
    }


}