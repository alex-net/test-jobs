<?php

namespace app\modules\track\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Модель Трек
 * @property int $id Первичный ключ
 * @property string $track_number Уникальный номер трека
 * @property datetime $created_at Дата/время добавление трека
 * @property datetime $updated_at Дата/время Обновления трека
 * @property string $status Текущий статус трека
 */
class Track extends ActiveRecord
{
    /**
     * Набор используемых статусов трека
     */
    const STATUSES = [
        'new' => 'Новый',
        'in_progress' => 'В обработке',
        'completed' => 'Успешное завершение',
        'failed' => 'Есть ошибки',
        'canceled' => 'Отменён',
    ];

    /**
     * Наименование полей
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'track_number' => 'Номер трека',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлён',
            'changes' => 'Изменения',
        ];
    }

    /**
     * Набор правил валидации
     * @return array
     */
    public function rules()
    {
        return [
            ['track_number', 'trim'],
            ['track_number', 'string', 'max' => 20],
            [['track_number', 'status'], 'required'],
            ['track_number', 'unique'],
            [['created_at', 'updated_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['created_at', 'updated_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            ['status', 'in', 'range' => array_keys(static::STATUSES)],
        ];
    }

    public function getStatusName()
    {
        return static::STATUSES[$this->status];
    }

    /**
     * Корректировка даты обновления трека
     * @param  bool $ins     Признак добавления новой записи
     * @param  array $chAttrs Набор изменённых атрибутов
     */
    public function afterSave($ins, $chAttrs)
    {
        if ($chAttrs) {
            $this->updateAttributes(['updated_at' => date('Y-m-d H:i:s')]);
            foreach (array_intersect(array_keys($chAttrs), TrackLog::LOG_OF_FIELDS) as $f) {
                $logRecord = new TrackLog([
                    'track_id' => $this->id,
                    'field' => $f,
                    'val_old' => $chAttrs[$f],
                    'val_new' => $this->{$f},
                ]);
                $logRecord->save();
            }
        }
        parent::afterSave($ins, $chAttrs);
    }

    /**
     * поиск треков по заданным параметрам ..
     * @param  array  $fields Набор фильтров
     * @return yii\db\ActiveQuery
     */
    public static function search($fields = [])
    {
        $q = static::find();
        $fields = array_filter($fields);
        if (!empty($fields['status'])) {
            $stl = is_array($fields['status']) ? $fields['status']: explode(',', $fields['status']);
            $stl = array_filter(array_map('trim', $stl));
            $stl = array_intersect(array_keys(static::STATUSES), $stl);
            if ($stl) {
                $q->andWhere(['status' => $stl]);
            }
        }
        return $q;
    }

    /**
     * Связка на модель лога
     * @return  \yii\db\ActiveQuery
     */
    public function getLog()
    {
        return $this->hasMany(TrackLog::class, ['track_id' => 'id']);
    }


    /**
     * Количество изменени. Отталкиваемся от разницы даты/времени. Два и более измнения с одной датой/временм считаются одним ю
     * @return Количестов изменений
     */
    public function getChanges()
    {
        return count(array_unique(ArrayHelper::getColumn($this->log, 'changed_at')));
    }
}