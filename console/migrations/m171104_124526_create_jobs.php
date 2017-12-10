<?php

use yii\db\Migration;

class m171104_124526_create_jobs extends Migration
{
    public function up()
    {
        $this->createTable('jobs', [
            'id' => 'INT(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'projectId' => $this->integer(11)->unsigned()->notNull()->comment('Project'),
            'key' => $this->string(255)->notNull()->comment('Unique job identifier'),
            'title' => $this->string(255)->comment('Custom title'),
            'critDuration' => $this->decimal(9, 3)->unsigned()->comment('Critical job duration seconds'),
            'createdAt' => $this->dateTime()->notNull(),
            // indexes
            'PRIMARY KEY (`id`)',
            'UNIQUE KEY `UNIQUE_PROJECT_KEY` (`projectId`, `key`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('JOB_PROJECT_FK', 'jobs', 'projectId', 'projects', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('jobs');
    }
}
