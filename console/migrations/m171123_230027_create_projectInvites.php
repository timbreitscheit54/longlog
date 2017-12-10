<?php

use yii\db\Migration;

class m171123_230027_create_projectInvites extends Migration
{
    public function up()
    {
        $this->createTable('projectInvites', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
            'projectId' => $this->integer()->unsigned()->notNull()->comment('Project'),
            'email' => $this->string()->notNull()->comment('Invited user email'),
            'code' => $this->char(32)->notNull()->comment('Invitation secret code'),
            'role' => 'ENUM("viewer", "admin") NOT NULL DEFAULT "viewer" COMMENT "Invited user project role"',
            'status' => 'ENUM("sent", "accepted") NOT NULL DEFAULT "sent" COMMENT "Invitation status"',
            'createdBy' => $this->integer()->unsigned()->comment('Who invited'),
            'sentAt' => $this->dateTime()->comment('Last email sent date'),
            'acceptedAt' => $this->dateTime()->comment('Accepted date'),
            // indexes
            'PRIMARY KEY (`id`)',
            'KEY `code_index` (`code`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('INVITE_PROJECT_FK', 'projectInvites', 'projectId', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('INVITE_SENDER_FK', 'projectInvites', 'createdBy', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('projectInvites');
    }
}
