<?php

use yii\db\Migration;
use yii\helpers\FileHelper;
use app\models\Book;

class m251209_135841_books extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // таблица авторов
        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'fio' => $this->string()->notNull()->comment('ФИО автора'),
        ]);
        $this->addCommentOnTable('{{%author}}', 'Авторы книг');

        $this->createIndex('fio-author-ind', '{{%author}}', ['fio']);

        // таблица с книжками ..
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('Название'),
            'year' => $this->integer()->unsigned()->comment('Год издания'),
            'isbn' => $this->string(20)->notNull()->comment('ISBN'),
            'descr' => $this->text()->comment('Краткое описание'),
        ]);
        foreach (['name', 'year', 'isbn'] as $field) {
            $this->createIndex($field . '-book-ind', '{{%book}}', [$field]);
        }
        FileHelper::createDirectory(Yii::getAlias('@covers'));

        // таблица связка ..
        $this->createTable('{{%bab}}', [
            'bid' => $this->integer()->notNull()->comment('Ссылка на книгу'),
            'aid' => $this->integer()->notNull()->comment('Ссылка на автора'),
        ]);
        $this->createIndex('bid-bab-ind', '{{%bab}}', ['bid']);
        $this->createIndex('aid-bab-ind', '{{%bab}}', ['aid']);
        $this->addForeignKey('bid-bab-fk', '{{%bab}}', ['bid'], '{{%book}}', ['id'], 'cascade', 'cascade');
        $this->addForeignKey('aid-bab-fk', '{{%bab}}', ['aid'], '{{%author}}', ['id'], 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach (['bab', 'book', 'author'] as $tbl) {
           $this->dropTable('{{%' . $tbl . '}}');
        }
        FileHelper::removeDirectory(Yii::getAlias('@covers'));
    }

}
