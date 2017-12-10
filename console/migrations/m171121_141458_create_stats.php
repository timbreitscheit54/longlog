<?php

use yii\db\Migration;

class m171121_141458_create_stats extends Migration
{
    public function up()
    {
        $this->createTable('stats', [
            'jobId' => $this->integer(11)->unsigned()->notNull()->comment('Job'),
            'date' => $this->date()->notNull()->comment('Statistical day'),
            'avgDuration' => $this->decimal(9, 3)->unsigned()->notNull()->comment('Average duration'),
            'minOperationId' => $this->bigInteger(20)->unsigned()->comment('Fastest operation id'),
            'maxOperationId' => $this->bigInteger(20)->unsigned()->comment('Slowest operation id'),
            'operationsCount' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('Total operations count'),
            // indexes
            'PRIMARY KEY (`jobId`, `date`)',
            'KEY `date_index` (`date`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('STATS_JOB_FK', 'stats', 'jobId', 'jobs', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('STATS_MIN_OPERATION_FK', 'stats', 'minOperationId', 'operations', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('STATS_MAX_OPERATION_FK', 'stats', 'maxOperationId', 'operations', 'id', 'SET NULL', 'CASCADE');

        // For stats: indexing operations date
        $this->createIndex('createdAt_index', 'operations', 'createdAt');
    }

    public function down()
    {
        $this->dropIndex('createdAt_index', 'operations');

        $this->dropTable('stats');
    }
}
