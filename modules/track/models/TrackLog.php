<?php

namespace app\modules\track\models;

use yii\db\ActiveRecord;

/**
 * Модель лога изменения трека
 * @property int $id Первичный ключик
 * @property int $track_id Ссылка на модель лога
 * @property datetime $changed_at Дата и время изменения
 * @property string $field Имя изменяемого поля
 * @property string $val_old Старое значение
 * @property string $val_new Новое значение
 */
class TrackLog extends ActiveRecord
{
    const LOG_OF_FIELDS = ['status', 'track_number'];

    /**
     * Описание правил валидаци
     * @return array
     */
    public function rules()
    {
        return [
            [['track_id', 'field'], 'required'],
            ['track_id', 'exist', 'targetRelation' => 'track'],
            ['field', 'in', 'range' => static::LOG_OF_FIELDS],
            [['val_old', 'val_new'], 'string'],
            ['changed_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['changed_at', 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }

    /**
     * подписи полей
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'changed_at' => 'Дата изменения',
            'field' => 'Изменённое поле',
            'val_old' => 'Старое значение',
            'val_new' => 'Новое значение',
        ];
    }

    /**
     * Связка на модель текра
     * @return yii\db\ActiveQuery
     */
    public function getTrack()
    {
        return $this->hasOne(Track::class, ['id' => 'track_id']);
    }
}