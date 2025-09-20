<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

trait ContentTrait
{

    /**
     * генерация пункта меню для сайта
     * @return [type] [description]
     */
    public static function menuItem()
    {
        $objQ = static::find();
        // для "не админов" фильтрация по их объектам
        if (!Yii::$app->user->can(User::ROLE_ADMIN)) {
            $objQ->where(['uid' => Yii::$app->user->id]);
        }

        $objCo = $objQ->count();
        $cl = explode("\\", static::class);
        $cl = end($cl);

        return [
            'label' => sprintf('%s (%d)', static::NAMES[1], $objCo),
            'url' => ['content-render/' .strtolower($cl)],
            'visible' => $objCo,
        ];
    }

    public function attributeLabels()
    {
        return [
            'uid' => 'Владелец записи',
            'name' => 'Наименование',
        ];
    }

    /**
     * Список объеков
     * @return ActiveDataProvider
     */
    public static function getList($uid = null)
    {
        $q = static::find()->with('user', 'binder', static::COUNT_PARAMS[0]);
        if (!Yii::$app->user->can(User::ROLE_ADMIN)) {
            $q->where(['uid' => Yii::$app->user->id]);
        } else {
            if ($uid) {
                $q->where(['uid' => $uid]);
            }
        }

        return new ActiveDataProvider([
            'query' => $q,
            'sort' => [
                'attributes' => [
                    'name' => [
                        'asc' => [static::TITLE_NAME => SORT_ASC],
                        'desc' => [static::TITLE_NAME => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                ],
            ],
        ]);
    }


    public function afterSave($ins, $chAttrs)
    {
        parent::afterSave($ins, $chAttrs);
        // обнова владельца в зависимых сущностях
        if (!$ins && array_key_exists('uid', $chAttrs)) {
            foreach ($this->{static::COUNT_PARAMS[0]} as $obj) {
                if ($obj->uid != $this->uid) {
                    $obj->uid = $this->uid;
                    $obj->save();
                }
            }
        }
    }
}