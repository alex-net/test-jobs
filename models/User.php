<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\data\ActiveDataProvider;
use Yii;


/**
 * класс определяющий сущность юзеря ..
 *
 * @property int $id Номер пользователя
 * @property string $email Почта
 * @property string $fullName Польное имя
 * @property string $password Хэш пароля
 * @property bool $active Активный пользоатель
 * @property int $created Дата создания пользователя
 * @property string $note Заметка админа.
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ADMIN_ROLE = 'admin';
    /**
     * прежний пароль пользователя
     * @var string
     */
    private $oldPasswordHash;

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return hash('sha256', $this->id . '-' . $this->password . '#' . $this->email);
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($pass)
    {
        return hash('sha256', $pass) == $this->password;
    }

    public function afterFind()
    {
        // сохраняем пароль чтобы потом его восстановить
        $this->oldPasswordHash = $this->password;
    }

    public function afterSave($ins, $attrs)
    {
        parent::afterSave($ins, $attrs);
        $adminRole = Yii::$app->authManager->getRole(static::ADMIN_ROLE);
        if ($this->isAdmin) {
            Yii::$app->authManager->assign($adminRole, $this->id);
        } else {
            Yii::$app->authManager->revoke($adminRole, $this->id);
        }
    }
    public function afterDelete()
    {
        parent::afterDelete();
        $adminRole = Yii::$app->authManager->getRole(static::ADMIN_ROLE);
        Yii::$app->authManager->revoke($adminRole, $this->id);
    }

    public function rules()
    {
        return [
            [['email', 'fullName', 'note', 'password'], 'trim'],
            ['email', 'string'],
            ['email', 'email'],
            ['email', 'unique'],
            ['fullName', 'string', 'max' => 120],
            [['email', 'fullName'], 'required'],
            ['active', 'boolean'],
            ['note', 'string'],
            ['created', 'integer'],
            ['created', 'default', 'value' => time(),],
            ['password', 'string', 'min' => 8],
            ['password', 'makeHash', 'skipOnEmpty' => false],
            ['password', 'required', 'when' => fn($m) => $m->isNewRecord, 'enableClientValidation' => false ],
            ['isAdmin', 'boolean'],
            ['isAdmin', 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Почта',
            'fullName' => 'Полное имя пользователя',
            'note' => 'Заметка',
            'password' => 'Пароль',
            'active' => 'Активный',
            'created' => 'Дата создания',
            'isAdmin' => 'Является админом',
        ];
    }

    /**
     * хешировать введённый пароль или восстановить старый
     * @param  string $attr имя атрибута
     */
    public function makeHash($attr)
    {
        $this->$attr = $this->$attr ? hash('sha256', $this->$attr) : $this->oldPasswordHash;
    }

    /**
     * Сптсок счетов
     * @return ActiveQuery объект запроса
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['uid' => 'id']);
    }

    /**
     * список счетов
     * @return ActiveDataProvider Объект для отображения в GridView
     */
    public function getAccountsList()
    {
        return new ActiveDataProvider([
            'query' => $this->getAccounts(),
        ]);
    }
}
