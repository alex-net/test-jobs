<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginAndRegistrationForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginAndRegistrationForm extends Model
{
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_REGISTRATION = 'registration';

    public $username;
    public $password;
    public $rememberMe = true;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            ['username', 'string', 'max' => 20, 'on' => static::SCENARIO_REGISTRATION],
            // ['username', 'unique', 'on' => static::SCENARIO_REGISTRATION],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean', 'on' => static::SCENARIO_LOGIN],
            // password is validated by validatePassword()
            ['password', 'validatePassword', 'on' => static::SCENARIO_LOGIN],
            ['username', 'checkExist', 'on' => static::SCENARIO_REGISTRATION],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            // !$user->validatePassword($this->password)
            if (!$user || !Yii::$app->security->validatePassword($this->password, $user->passHash)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * попытка создать юзеря
     */
    public function checkExist($attr)
    {
        if (User::find()->where(['login' => $this->$attr])->count()) {
            foreach (['username', 'password'] as $f) {
                $this->addError($f, 'Ошибка в логине или пароле');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @param array  $post Данные post запроса
     * @return bool whether the user is logged in successfully
     */
    public function todo($post)
    {
        if ($this->load($post) && $this->validate()) {

            if ($this->scenario == static::SCENARIO_LOGIN) {
                return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
            }
            if ($this->scenario == static::SCENARIO_REGISTRATION) {
                $user = new User([
                    'login' => $this->username,
                    'passHash' => Yii::$app->security->generatePasswordHash($this->password),
                    'role' => array_rand(User::ROLES),
                ]);
                return $user->save();
            }
        }
    }

    /**
     * забрать юзера по логину ..
     * @return [type] [description]
     */
    protected function getUser()
    {
        static $_user = null;
        if (!$_user) {
            $_user = User::findOne(['login' => $this->username]);
        }
        return $_user;
    }
}
