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
}