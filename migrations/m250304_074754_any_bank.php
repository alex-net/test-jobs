<?php

use yii\db\Migration;
use app\models\User;

class m250304_074754_any_bank extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(50)->notNull()->unique()->comment('Почта'),
            'fullName' => $this->string(120)->notNull()->comment('Полное имя'),
            'password' => $this->string(64)->notNull()->comment('Пароль'),
            'active' => $this->boolean()->defaultValue(false)->comment('Активный пользователь'),
            'created' => $this->integer()->unsigned()->defaultExpression('extract(epoch from current_timestamp)::integer'),
            'isAdmin' => $this->boolean()->defaultValue(false)->comment('Признак админа'),
            'note' => $this->text()->comment('примечание'),
        ]);

        $this->addCommentOnTable('{{%user}}', 'Таблиа пользователей');
        $this->createIndex('email-user-ind', '{{%user}}', ['email'], true);
        $this->createIndex('email-active-user-ind', '{{%user}}', ['email', 'active'], true);
        $this->createIndex('name-user-ind', '{{%user}}', ['fullName']);

        // создаём роль админа
        $adminRole = Yii::$app->authManager->createRole(User::ADMIN_ROLE);
        $adminRole->description = 'Админская роль';
        Yii::$app->authManager->add($adminRole);

        // добвляем админа
        $user = new User([
            'email' => 'admin@any-bank.ru',
            'fullName' => 'Админ',
            'password' => 'admin-pass',
            'isAdmin' => true,
            'active' => true,
        ]);
        $user->save();

        // добвляем Обычного пользователя
        $user = new User([
            'email' => 'user@any-bank.ru',
            'fullName' => 'Пользователь',
            'password' => 'user-pass',
            'active' => true,
        ]);
        $user->save();

        $this->createTable('{{%account}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->comment('Ссылка на пользователя'),
            'created' => $this->integer()->unsigned()->defaultExpression('extract(epoch from current_timestamp)::integer')->comment('Дата создания'),
            'balance' => $this->double(2)->defaultValue(0)->comment('Текущий баланс по счёту'),
        ]);

        $this->addCommentOnTable('{{%account}}', 'Таблиа счетов');
        $this->createIndex('uid-account-ind', '{{%account}}', ['uid']);
        $this->createIndex('created-account-ind', '{{%account}}', ['created']);
        $this->addForeignKey('user-acc-fk', '{{%account}}', ['uid'], '{{%user}}', ['id'], 'cascade', 'cascade');

        $this->createTable('{{%operation}}', [
            'id' => $this->primaryKey(),
            'created' => $this->integer()->unsigned()->defaultExpression('extract(epoch from current_timestamp)::integer'),
            'aid' => $this->integer()->notNull()->unsigned()->comment('Номер счёта'),
            'amount' => $this->float(2)->notNull()->comment('Размер операции'),
            'transId' => $this->string(36)->notNull()->unique()->comment('Номер транзакции'),
        ]);

        $this->addCommentOnTable('{{%operation}}', 'Таблиа платежей');
        $this->createIndex('created-operation-ind', '{{%operation}}', ['created']);
        $this->createIndex('transid-operation-ind', '{{%operation}}', ['transId'], true);
        $this->createIndex('aid-operation-ind', '{{%operation}}', ['aid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%operation}}');
        $this->dropTable('{{%account}}');
        $this->dropTable('{{%user}}');
        Yii::$app->authManager->removeAll();
    }
}
