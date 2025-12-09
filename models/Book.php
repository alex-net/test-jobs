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


    public function rules()
    {
        return [
            [['name', 'descr'], 'string'],
            ['isbn', 'string', 'max' => 20],
            [['name', 'descr', 'isbn'], 'trim'],
            ['year', 'integer', 'min' => 0],
            ['image', 'image', 'extensions' => ['jpg', 'jpeg', 'png']],
            [['name', 'year', 'isbn'], 'required'],
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
        ];
    }

    public function beforeValidate()
    {
        $this->image = UploadedFile::getInstance($this, 'image');
        return parent::beforeValidate();
    }

    public function getCover()
    {
        $images = FileHelper::findFiles(Yii::getAlias(static::COVE_FOLDER), ['filter' => fn($path) => preg_match(sprintf('#\/%d\.\w+$#i', $this->id), $path)]);
        if ($images) {
            $images = substr(reset($images), strlen(Yii::getAlias('@webroot'))) ;
            return $images;
        }
    }

    public function afterSave($ins, $chA)
    {
        parent::afterSave($ins, $chA);
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