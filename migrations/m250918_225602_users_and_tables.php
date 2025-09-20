<?php

use yii\db\Migration;
use app\models\User;

class m250918_225602_users_and_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // таблица с пользователями
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'login' => $this->string(20)->notNull()->comment('Логин пользователя'),
            'passHash' => $this->string(60)->notNull()->comment('хеш пароля'),
            'role' => $this->string(10)->notNull()->comment('Роль пользователя'),
        ]);
        $this->createIndex('login-users-ind', '{{%users}}', ['login']);


        // доп таблицы пользователей
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->comment('Ссылка на пользователя'),
            'title' => $this->string(100)->notNull()->comment('Название книги'),
        ]);
        $this->addForeignKey('users-books-fk', '{{%book}}', ['uid'], '{{%users}}', ['id'], 'cascade', 'cascade');

        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->comment('Ссылка на пользователя'),
            'full_name' => $this->string(50)->notNull()->comment('Наименование автора'),
        ]);
        $this->addForeignKey('users-authors-fk', '{{%author}}', ['uid'], '{{%users}}', ['id'], 'cascade', 'cascade');

        $this->createTable('{{%book_to_author}}', [
            'id' => $this->primaryKey(),
            'book_id' => $this->integer()->notNull()->comment('Ссылка на книгу'),
            'author_id' => $this->integer()->notNull()->comment('Ссылка на автовра'),
        ]);
        $this->addForeignKey('books-binder-fk', '{{%book_to_author}}', ['book_id'], '{{%book}}', ['id'], 'cascade', 'cascade');
        $this->addForeignKey('authors-binder-fk', '{{%book_to_author}}', ['author_id'], '{{%author}}', ['id'], 'cascade', 'cascade');
        foreach ([
            'book' => ['uid', 'title'],
            'author' => ['uid', 'full_name'],
            'book_to_author' => ['book_id', 'author_id'],
        ] as $tbl => $fields) {
            foreach ($fields as $field) {
                $this->createIndex("$tbl-$field-ind", "{{%$tbl}}", [$field]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach (['book_to_author', 'author', 'book', 'users'] as $tbl) {
            $this->dropTable("{{%$tbl}}");
        }
    }

}
