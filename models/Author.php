<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

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

    public function getBookNums()
    {
        return $this->hasMany(BookAuthorBinder::class, ['aid' => 'id']);
    }

    /**
     * список книг автора
     * @return [type] [description]
     */
    public function getBooksDp()
    {
        return new ArrayDataProvider([
            'allModels' => $this->books,
        ]);
    }

    /**
     * список авторов для страницы отчёт
     * @return [type] [description]
     */
    public static function listForReport($year = null)
    {
        $q = static::find()->alias('a')->joinWith(['books b'])->groupBy('a.id')->with('bookNums')->orderBy(new Expression('count(b.id) desc'))->limit(10);
        $q->filterWhere(['b.year' => $year]);
        return new ActiveDataProvider([
            'query' => $q,
            'sort' => false,
            'pagination' => false,
        ]);
    }
}