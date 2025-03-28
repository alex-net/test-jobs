<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * класс валидации ссылки
 *
 * @property int $id Идентификатор записи
 * @property string $url Исходная ссылка для перехода
 * @property string $urlHash Короткая ссылка сгенеренная для переходна на исходный url
 */
class UrlLink extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%url}}';
    }

    public static function findByHash($hash)
    {
        return static::find()->where(['urlHash' => $hash])->one();
    }

    public function rules()
    {
        return [
            ['url', 'required'],
            ['url', 'url'],
            ['url', 'unique'],
            ['url', 'testAccess'],
        ];
    }

    public function beforeSave($ins)
    {
        if (!parent::beforeSave($ins)) {
            return false;
        }
        $this->urlHash = sha1($this->url);
        return true;
    }

    public function attributeLabels()
    {
        return [
            'url' => 'Ссылка на ресурс',
            'urlHash' => 'Элемент короткой ссылоки',
        ];
    }

    /**
     * Валидатор проверяющий ссылку на досупность
     * @param  string $attr Наименование атрибута, содержащего ссылку ..
     */
    public function testAccess($attr)
    {
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($curl);
        if (curl_errno($curl)) {
            $this->addError($attr, 'Ссылка не доступна');
        }
        curl_close($curl);
    }

    /**
     * Связь на просмотры ссылки ....
     * @return [type] [description]
     */
    public function getRedirects()
    {
        return $this->hasMany(UrlRedirect::class, ['uid' => 'id']);
    }
}