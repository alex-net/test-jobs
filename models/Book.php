<?php

namespace app\models;

use yii\db\ActiveRecord;

class Book extends ActiveRecord
{
    use ContentTrait;

    // склонения текущего названия модели
    const NAMES = ['книгу', 'Книги'];
    // параметры связной сущности
    const COUNT_PARAMS = ['authors', 'Количество авторов'];
    // Название поля заголовка в текущей модели
    const TITLE_NAME = 'title';

    /**
     * Объявления правил валидации
     * @return array
     */
    public function rules()
    {
        return [
            ['title', 'trim'],
            ['title', 'string', 'max' => 100],
            [['title', 'uid'], 'required'],
            ['uid', 'exist', 'targetRelation' => 'user'],
        ];
    }

    /**
     * поле определяюще наименивание оюбъекта
     * @return text Значение наименования
     */
    public function getName()
    {
        return $this->title;
    }

    /**
     * Число связанных объектов. Для книг - авторы это
     * @return yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])->via('binder');
    }

    /**
     * Связка на промежуточную таблицу
     * @return yii\db\ActiveQuery
     */
    public function getBinder()
    {
        return $this->hasMany(AuthotBookBinder::class, ['book_id' => 'id']);
    }


    /**
     * Связка на пользователя
     * @return yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'uid']);
    }

}