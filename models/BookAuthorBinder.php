<?php

namespace app\models;

use yii\db\ActiveRecord;

class BookAuthorBinder extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%bab}}';
    }

    public function rules()
    {
        return [
            [['bid', 'aid'], 'integer'],
            [['bid', 'aid'], 'required'],
        ];
    }

    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'bid']);
    }

    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'aid']);
    }
}