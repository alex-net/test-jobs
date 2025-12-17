<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use Yii;


class Book extends ActiveRecord
{
    const COVE_FOLDER = '@webroot/covers/';

    /**
     * поле загрузки обложки
     * @var [type]
     */
    public $image;

    /**
     * список авторов
     * @var array
     */
    public $authorList = [];

    /**
     * Список всех прикреплённых авторов
     * @return [type] [description]
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'aid'])->viaTable('{{%bab}}', ['bid' => 'id'])->indexBy('id');
    }


    public function rules()
    {
        return [
            [['name', 'descr'], 'string'],
            ['isbn', 'string', 'max' => 20],
            [['name', 'descr', 'isbn'], 'trim'],
            ['year', 'integer', 'min' => 1],
            ['image', 'image', 'extensions' => ['jpg', 'jpeg', 'png']],
            [['name', 'year', 'isbn'], 'required'],
            ['authorList', 'each', 'rule' => ['integer']],
            // ['authorList', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'year' => 'Год издания',
            'isbn' => 'ISBN',
            'descr' => 'Краткое описание',
            'image' => 'Обложка',
            'authorList' => 'Список авторов',
        ];
    }

    public function beforeValidate()
    {
        $this->image = UploadedFile::getInstance($this, 'image');
        return parent::beforeValidate();
    }


    /**
     * вернуть картинку прикреплённую к ниге
     * @return string url каринки для тега img
     */
    public function getCover()
    {
        if ($this->isNewRecord) {
            return;
        }
        $images = FileHelper::findFiles(Yii::getAlias(static::COVE_FOLDER), ['filter' => fn($path) => boolval(preg_match(sprintf('#/%d\.\w+$#i', $this->id), $path)),]);

        if ($images) {
            $images = substr(reset($images), strlen(Yii::getAlias('@webroot'))) ;
            return $images;
        }
    }

    public function afterSave($ins, $chA)
    {
        parent::afterSave($ins, $chA);

        // удалить все связки на авторов
        BookAuthorBinder::deleteAll(['bid' => $this->id]);
        // создать записи для новых связок
        foreach ($this->authorList as $authorId) {
            $binder = new BookAuthorBinder(['bid' => $this->id, 'aid' => $authorId]);
            $binder->save();
        }
        // загружена картинка
        if ($this->image) {
            // удалить старую картинку, если есть
            if ($this->cover) {
                FileHelper::unlink(Yii::getAlias('@webroot') . $this->cover);
            }
            $path = Yii::getAlias(static::COVE_FOLDER . $this->id . '.' . $this->image->extension);
            $this->image->saveAs($path);
        }
    }
}