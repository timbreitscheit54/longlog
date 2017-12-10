<?php

use yii\db\Schema;
use yii\db\Migration;

class m171104_122757_create_secure_keys_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%secure_keys}}', [
            'id' => 'BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
            'userId' => "INT(11) UNSIGNED NOT NULL COMMENT 'User'",
            'type' => "TINYINT(3) NOT NULL COMMENT 'Key assignment(1-activation, 2-email changing, 3-password reset)'",
            'code' => "CHAR(32) NOT NULL COMMENT 'Secret code'",
            'status' => "ENUM('new','used','forgotten') NOT NULL COMMENT 'Status'",
            'validTo' => "DATETIME NOT NULL COMMENT 'Expiration date'",
            'updatedAt' => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date of key usage'",
            'PRIMARY KEY (`id`)',
            'KEY `MAIN_INDEX` (`userId`,`type`)',
        ], $tableOptions);

        $this->addForeignKey('SECURE_KEY_USER_FK', 'secure_keys', 'userId', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%secure_keys}}');
    }
}
