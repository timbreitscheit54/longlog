<?php

use yii\db\Migration;

class m171104_122818_create_projects extends Migration
{
    public function up()
    {
        $this->createTable('projects', [
            'id' => 'INT(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'name' => $this->string()->notNull()->comment('Project name'),
            'token' => $this->char(32)->notNull()->unique()->comment('Secret token'),
            'ownerId' => $this->integer(11)->unsigned()->comment('Project owner user'),
            'deletedAt' => $this->dateTime()->comment('Deleted date'),
            'createdAt' => $this->dateTime()->notNull(),
            'updatedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            // indexes
            'PRIMARY KEY (`id`)',
            'KEY `deletedAt_index` (`deletedAt`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('OWNER_USER_FK', 'projects', 'ownerId', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('projects');
    }
}
