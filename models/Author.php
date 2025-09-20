<?php

namespace app\models;

use yii\db\ActiveRecord;

class Author extends ActiveRecord
{
    use ContentTrait;

    const NAMES = ['aвтора', 'Авторы'];
    const COUNT_PARAMS = ['books', 'Количество книг'];
    const TITLE_NAME = 'full_name';

    public function rules()
    {
        return [
            ['full_name', 'trim'],
            ['full_name', 'string', 'max' => 50],
            [['full_name', 'uid'], 'required'],
            // ['uid', 'integer'],
            ['uid', 'exist', 'targetRelation' => 'user'],
        ];
    }

    public function getName()
    {
        return $this->full_name;
    }

    /**
     * Число связанных объектов. Для книг - авторы это
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])->via('binder');
    }


    public function getBinder()
    {
        return $this->hasMany(AuthotBookBinder::class, ['author_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'uid']);
    }

}