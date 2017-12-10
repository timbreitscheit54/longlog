<?php

use yii\db\Migration;

class m171104_125253_create_operations extends Migration
{
    public function up()
    {
        $this->createTable('operations', [
            'id' => 'BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
            'jobId' => $this->integer()->unsigned()->notNull()->comment('Job'),
            'duration' => $this->decimal(9, 3)->unsigned()->notNull()->comment('Duration seconds'),
            'payload' => $this->string(255)->comment('Some working data'),
            'createdAt' => $this->dateTime()->notNull()->comment('Record created time'),
            // indexes
            'PRIMARY KEY (`id`)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('JOB_OPERATION_FK', 'operations', 'jobId', 'jobs', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('operations');
    }
}
