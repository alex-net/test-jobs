<?php

namespace app\models;

use yii\db\ActiveRecord;

class AuthotBookBinder extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%book_to_author}}';
    }

    public function rules()
    {
        return [
            [['book_id', 'author_id'], 'required'],
            ['book_id', 'exist', 'targetRelation' => 'book'],
            ['author_id', 'exist', 'targetRelation' => 'author'],
        ];
    }

    /**
     * Связка на модель Книг
     * @return yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * Связка на модель авторов
     * @return yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

}