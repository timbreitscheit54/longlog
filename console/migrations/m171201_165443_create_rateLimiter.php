<?php

use yii\db\Migration;

class m171201_165443_create_rateLimiter extends Migration
{
    public function up()
    {
        $this->createTable('rateLimiter', [
            'route' => $this->string(100)->notNull(),
            'userIp' => $this->integer()->unsigned()->notNull(),
            'time' => $this->integer()->unsigned()->notNull(),
            // indexes
            'KEY `main_index` (`route`, `userIp`, `time`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('rateLimiter');
    }
}
