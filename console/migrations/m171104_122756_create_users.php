<?php

use yii\db\Migration;

class m171104_122756_create_users extends Migration
{
    public function up()
    {
        $this->createTable('users', [
            'id' => 'INT(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'name' => $this->string()->notNull(),
            'email' => $this->string(100)->notNull()->unique(),
            'password' => $this->string(100)->notNull(),
            'newEmail' => $this->string(100)->comment('New email value before confirmation'),
            'role' => 'ENUM("viewer", "manager", "admin") NOT NULL',
            'authKey' => $this->char(32)->notNull()->unique()->comment('"remember me" authentication key'),
            'accessToken' => $this->char(32)->notNull()->unique()->comment('API access token'),
            'status' => $this->boolean()->notNull()->comment('Status: 0-new, 1-confirmed'),
            'language' => $this->string(10)->comment('Selected language'),
            'deletedAt' => $this->dateTime()->comment('Deleted date'),
            'createdAt' => $this->dateTime()->notNull()->comment('Registration date'),
            'updatedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            // indexes
            'PRIMARY KEY (`id`)',
            'KEY `deletedAt_index` (`deletedAt`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('users');
    }
}
