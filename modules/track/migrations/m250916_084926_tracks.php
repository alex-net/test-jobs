<?php

use yii\db\Migration;

class m250916_084926_tracks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("create type trackStatus as enum ('new', 'in_progress', 'completed', 'failed', 'canceled');");
        $this->createTable('{{%track}}', [
            'id' => $this->primaryKey(),
            'track_number' => $this->string(20)->unique()->notNull()->comment('уникальный идентификатор или номер трека'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('now()')->comment('дата и время создания записи'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('now()')->comment('дата и время последнего обновления записи'),
            'status trackStatus not null',
        ]);
        $this->addCommentOnTable('{{%track}}', 'Треки');


        $this->execute("create type trackLogField as enum ('status', 'track_number');");
        $this->createTable('{{%track_log}}', [
            'id' => $this->primaryKey(),
            'track_id' => $this->integer()->notNull()->comment('Ссылка на ключ трека'),
            'changed_at' => $this->dateTime()->notNull()->defaultExpression('now()')->comment('Дата изменения'),
            'field trackLogField not null',
            'val_old' => $this->string(20)->comment('Старое значение'),
            'val_new' => $this->string(20)->notNull()->comment('Новое значение'),
        ]);
        $this->addForeignKey('log-bind-fk', '{{%track_log}}', ['track_id'], '{{%track}}', ['id'], 'cascade', 'cascade');
        $this->createIndex('log-field-ind', '{{%track_log}}', ['field']);
        $this->addCommentOnTable('{{%track_log}}', 'Лог изменения треков');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%track_log}}');
        $this->execute('drop type trackLogField');
        $this->dropTable('{{%track}}');
        $this->execute('drop type trackStatus');
    }
}
