<?php

namespace app\models;

use yii\db\ActiveRecord;

class Author extends ActiveRecord
{
    public function rules()
    {
        return [
            ['fio', 'string'],
            ['fio', 'trim'],
            ['fio', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО',
        ];
    }

    /**
     * запрос всех книжек приереплённых к автору
     * @return [type] [description]
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'bid'])->viaTable('{{%bab}}', ['aid' => 'id']);
    }
}