<?php

use yii\db\Migration;
use app\models\User;

/**
 * миграция создания таблицы для хранения пользоватлей и одного пользователя ..
 */
class m251209_132808_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'login' => $this->string(20)->notNull()->comment('Логин'),
            'passHash' => $this->string(60)->notNull()->comment('Хеш пароля'),
        ]);

        // добавили индекс
        $this->createIndex('user-login-ind', '{{%user}}', ['login'], true);

        // создаём пользователя
        $u = new User([
            'login' => 'admin',
            'passHash' => Yii::$app->security->generatePasswordHash('admin'),
        ]);
        $u->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
