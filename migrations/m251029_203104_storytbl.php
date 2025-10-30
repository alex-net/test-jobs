<?php

use yii\db\Migration;

class m251029_203104_storytbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story_valut}}', [
            'id' => $this->primaryKey(),
            'author' => $this->string(15)->notNull()->comment('Имя автора'),
            'message' => $this->text()->comment('Текст сообщения'),
            'mail' => $this->string(150)->notNull()->comment('Почта'),
            'created' => $this->integer()->defaultExpression('extract(epoch from now())::integer')->comment('Дата создания'),
            'ip' => $this->string(40)->notNull()->comment('IP'),
        ]);
        foreach (['author', 'mail', 'created', 'ip'] as $field) {
            $this->createIndex('story-valut-' . $field . '-ind', '{{%story_valut}}', [$field]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%story_valut}}');
    }
}
