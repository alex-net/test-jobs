<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Класс операции по счёту
 *
 * @property int $id Ключ операции
 * @property int $aid Ссылка на счёт используемый в операции
 * @property float $amount сумма операции
 * @property string $transId ID транзакции
 */
class Operation extends ActiveRecord
{
    /**
     * проверка наличия ключа транзакции в базе
     *
     * @param  string $id Ключ транзакции
     * @return bool Результат поиска транзакции в базе.  true = найдена в базе
     */
    public static function existTransId($id)
    {
        return static::find()->where(['transId' => $id])->exists();
    }

    public function rules()
    {
        return [
            [['id', 'created'], 'integer'],
            ['amount', 'double'],
            ['transId', 'string'],
            ['created', 'default', 'value' => time()],
            [['transId', 'amount', 'aid'], 'required'],
            ['aid', 'exist', 'targetRelation' => 'account'],
            ['transId', 'match', 'pattern' => '#^.{8}-.{4}-.{4}-.{4}-.{12}$#'],
            ['transId', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'transId' => 'Номер транзакции',
            'amount' => 'Сумма',
            'created' => 'Дата',
            'aid' => 'связанный счёт',
        ];
    }

    /**
     * Связка со счётом
     * @return ActiveQuery Запрос на получение модели счёта
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'aid']);
    }

    /**
     * Выполнение доп.операций после сохранения
     * @param  bool $ins     Признак вставки
     * @param  array $chAttrs Набор аттрибутов модели подвергшихся изменению
     */
    public function afterSave($ins, $chAttrs)
    {
        parent::afterSave($ins, $chAttrs);
        if (array_key_exists('amount', $chAttrs)) {
            $this->account->refreshBlance();
        }

    }
}