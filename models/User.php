<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use Yii;

class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_ADMIN = 'admin';
    const ROLES = [
        self::ROLE_ADMIN => 'Админ',
        'user 1' => 'Роль 1',
        'role 2' => 'Роль 2',
    ];

    public static function tableName()
    {
        return '{{%users}}';
    }

    public function rules()
    {
        return [
            [['login', 'passHash'], 'trim'],
            ['login', 'string', 'max' => 20],
            ['login', 'unique'],
            ['passHash', 'string', 'max' => 60, 'min' => 60],
            [['login', 'passHash', 'role'], 'required'],
            ['role', 'in', 'range' => array_keys(static::ROLES)],
        ];
    }

    public function attributeLabels()
    {
        return [
            'login' => 'Логин',
            'role' => 'Роль',
            // 'books' =>
        ];
    }

    /**
     * Связка на книжки пользоателя
     * @return yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['uid' => 'id']);
    }

    /**
     * Связка на авторов пользователя
     * @return yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['uid' => 'id']);
    }

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

    public function getUsername()
    {
        return $this->login;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['login' => $username]);
    }

    /**
     * пользователи в виде списка
     * @return array
     */
    public static function asList()
    {
        $list = static::find()->select(['id', 'login'])->asArray()->indexBy('id')->all();
        return ArrayHelper::getColumn($list, 'login');
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
        $aKey = Yii::$app->session->get('aKey');
        if (!isset($aKey)) {
            $aKey = Yii::$app->security->encryptByPassword($this->login . '-'. Yii::$app->security->generateRandomString(), $this->passHash);
            Yii::$app->session->set('aKey', $aKey);
        }
        return $aKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->passHash === Yii::$app->security->generatePasswordHash($password);
    }

    public function afterSave($ins, $chAttrs)
    {
        parent::afterSave($ins, $chAttrs);
        if ($ins) { // новый пользователь
            $this->generateContent();
        }
    }

    /**
     * генерация полезного контента по книжкам и авторам
     * @return [type] [description]
     */
    protected function generateContent()
    {
        // генерим книжки
        $books = [];
        $bookMax = rand(3, 20);
        for ($i = 0; $i < $bookMax; $i++) {
            $book = new Book([
                'title' => Yii::$app->security->generateRandomString(rand(10, 95)),
                'uid' => $this->id,
            ]);
            if ($book->save()) {
                $books[$book->id] = $book->id;
            }
        }

        // генерим авторов
        $authors = [];
        $authorsMax = rand(3, 30);
        for ($i = 0; $i < $authorsMax; $i++) {
            $author = new Author([
                'full_name' => Yii::$app->security->generateRandomString(rand(10, 45)),
                'uid' => $this->id,
            ]);
            if ($author->save()) {
                $authors[$author->id] = $author->id;
            }
        }

        // генерим связки ...(у каждой книжки должен быть хотябы один автор )
        foreach ($books as $bId) {
            $aIds =  array_rand($authors, rand(1, 3));
            if (!is_array($aIds)) {
                $aIds = [$aIds];
            }
            foreach ($aIds as $aId) {
                $bind = new AuthotBookBinder([
                    'book_id' => $bId,
                    'author_id' => $authors[$aId],
                ]);
                $bind->save();
            }
        }
    }
}
