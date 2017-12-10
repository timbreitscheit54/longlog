<?php

use yii\db\Migration;

class m171105_042405_create_projectUsers extends Migration
{
    public function up()
    {
        $this->createTable('projectUsers', [
            'projectId' => $this->integer(11)->unsigned()->notNull()->comment('Project'),
            'userId' => $this->integer(11)->unsigned()->notNull()->comment('User'),
            'role' => 'ENUM("viewer", "admin") NOT NULL COMMENT "User project role"',
            'createdAt' => $this->dateTime()->notNull(),
            'updatedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            // indexes
            'PRIMARY KEY (`projectId`, `userId`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('PROJECT_USERS_USER_FK', 'projectUsers', 'projectId', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('PROJECT_USERS_PROJECT_FK', 'projectUsers', 'userId', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('projectUsers');
    }
}
