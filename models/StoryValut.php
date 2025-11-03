<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use Yii;
use HTMLPurifier_Config;

/**
 * модель таблицы StoryValut
 *
 * @property int $id Номер записи в базе
 * @property string $author Имя автора
 * @property string $message Сообщение пользователя
 * @property string $mail Почта автора
 * @property string $created Создание записи Дата/время
 * @property string $ip IP адрес автора
 */
class StoryValut extends ActiveRecord
{
    const ACTION_TERMS = [
        'edit' => 12,
        'kill' => 14,
    ];
    const OVERAGE_LIMIT_CREATEATED_EVENT = 'overage-limit-createated';
    const SCENARIO_ONLY_EDIT = 'edit message';

    /**
     * Капча
     * @var string
     */
    public $captcha;

    public static function tableName()
    {
        return '{{%story_valut}}';
    }

    /**
     * список доступных сценариев валидации
     * @return [type] [description]
     */
    public function scenarios()
    {
        return  array_merge(parent::scenarios(), [
            static::SCENARIO_ONLY_EDIT => ['message'],
        ]);
    }

    /**
     * правила валидации
     * @return [type] [description]
     */
    public function rules()
    {
        return [
            [['author', 'message', 'mail'], 'trim'],
            ['author', 'filter', 'filter' => 'htmlspecialchars'],
            ['author', 'string', 'max' => 15, 'min' => 2],
            ['message', 'string', 'max' => 1000, 'min' => 5],
            ['message', 'filter', 'filter' => fn($text) => HtmlPurifier::process($text, [
                'HTML.Allowed' => 'b,i,s',
            ])],
            ['mail', 'string', 'max' => 150],
            ['mail', 'email'],
            ['created', 'default', 'value' => time()],
            ['ip', 'ip'],
            ['captcha', 'captcha'],
            [['author', 'message', 'mail', 'ip', 'captcha'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'author' => 'Имя автора',
            'mail' => 'Email',
            'message' => 'Сообщение',
        ];
    }

    public static function list()
    {
        return new ActiveDataProvider([
            'query' => static::find()->with('countAll'),
            'sort' => [
                'attributes' => ['created'],
                'defaultOrder' => ['created' => SORT_DESC],
            ],
        ]);
    }

    /**
     * поиск записи по ключу из адресной строки ( редактирование / удаление)
     * @param  string $key Ключ для поиска
     * @return array|null      Массив содержит найденную модель (поле model) и действие из ключа (поле action)
     */
    public static function getPostByKey($key)
    {
        $key = \yii::$app->security->decryptByKey($key, $_SERVER['PHP_SHA256']);
        if (!$key) { // ключ не распознан
            return ;
        }
        list($action, $id) = array_slice(explode('--', $key), 1, 2);

        if (!isset(static::ACTION_TERMS[$action])) { // неверная операция
            return;
        }
        $model = static::findOne($id);
        if (!$model || (time() - $model->created) / 3600 > static::ACTION_TERMS[$action]) { // не найдена модель в базе или превышен лимит на операцию
            return;
        }

        return ['action' => $action, 'model' => $model];
    }


    public function getCountAll()
    {
        return $this->hasMany(static::class, ['ip' => 'ip']);
    }

    /**
     * генерация ссылок на редактирование и удаление записи
     * @return array
     */
    public function getLinks()
    {
        $links = [];
        foreach (static::ACTION_TERMS as $action => $term) {
            $key = \yii::$app->security->encryptByKey(rand() . '--' . $action . '--' . $this->id . '--' . rand(), $_SERVER['PHP_SHA256']);
            $links[$action] = [
                'url' => Url::to(['site/post', 'key' => $key], 1),
                'until' => date('c', time() + $term * 3600),
            ];
        }
        return $links;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($ins)
    {
        if (!parent::beforeSave($ins)) {
            return false;
        }

        $lastPost = static::find()->where(['ip' => $this->ip])->orderBy(['created' => SORT_DESC])->one();
        // заблокировать сохранение новой записи если прошлая запись была опубликована менее чем 3 мин назад
        if ($lastPost && time() - $lastPost->created < 180) {
            $lastPost->trigger(static::OVERAGE_LIMIT_CREATEATED_EVENT);
            return false;
        }
        return true;
    }
}