<?php

namespace api\models;

use common\components\validators\DurationValidator;
use common\models\Job;
use common\models\Operation;
use yii\base\Model;

class NewLogForm extends Model
{
    /**
     * Project secret token
     *
     * @var string
     */
    public $projectToken;
    /**
     * Some job name
     *
     * @var string
     */
    public $jobName;
    /**
     * Job duration in seconds (decimal: 999999.999)
     *
     * @var string
     */
    public $duration;
    /**
     * Job operation payload
     *
     * @var string
     */
    public $payload;
    /**
     * Enviroment key
     *
     * @var string
     */
    public $environment;
    /**
     * Custom log time (unix-timestamp)
     *
     * @var integer
     */
    public $timestamp;

    /**
     * Project id
     *
     * @var int
     */
    public $projectId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['projectToken', 'jobName', 'duration'], 'required'],
            // integer
            [['timestamp'], 'integer'],
            // duration
            [['duration'], DurationValidator::className()],
            // project token
            [['projectToken'], 'string', 'length' => 32],
            // job name
            [['jobName'], 'string', 'min' => 2, 'max' => 255],
            // payload
            [['payload'], 'string', 'max' => 255],
            // environment
            [['environment'], 'string', 'max' => 255],
        ];
    }

    /**
     * Save new log record to DB
     *
     * @return boolean
     */
    public function save()
    {
        // Lock tables write, prevent duplication entry errors
        Operation::lockTable('WRITE', [Operation::tableName(), Job::tableName()]);

        // Create new job if needed
        $jobId = Job::getIdByKey($this->projectId, $this->jobName);

        $operation = new Operation();
        $operation->jobId = $jobId;
        $operation->duration = $this->duration;
        $operation->payload = $this->payload;

        // Operation datetime (custom or current time)
        $operation->createdAt = date('Y-m-d H:i:s', $this->timestamp ? $this->timestamp : time());

        $result = $operation->save(false);

        // Unlock tables
        Operation::unlockTable();

        return $result;
    }
}
