<?php

use yii\db\Migration;

class m250327_153252_urls_base extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%url}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string()->notNull()->unique()->comment('Исходный URL для перехода'),
            'urlHash' => $this->string(40)->notNull()->comment('Короткая ссылка'),
        ]);
        $this->createTable('{{%url_redirect}}', [
            'uid' => $this->integer()->notNull()->comment('Ссылка на url'),
            'ip' => $this->integer()->unsigned()->notNull()->comment('IP'),
            'time' => $this->integer()->notNull()->defaultExpression('unix_timestamp()')->comment('Время перехода по ссылке'),
        ]);
        $this->createIndex('ur-uid-ind', '{{%url_redirect}}', ['uid']);
        $this->addForeignKey('ur-uid-fk', '{{%url_redirect}}', ['uid'], '{{%url}}', ['id'], 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%url_redirect}}');
        $this->dropTable('{{%url}}');
    }
}
