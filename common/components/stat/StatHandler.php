<?php

namespace common\components\stat;

use common\models\Job;
use common\models\Operation;
use common\models\Stat;
use yii\base\BaseObject;
use yii\db\Query;

/**
 * Collect and save stat to DB
 *
 * @property-read array $errors Error messages while handling
 */
class StatHandler extends BaseObject
{
    /**
     * Max data biffer size, if buffer reached it value - data will be inserted to DB.
     * Value 0 meaning insert each row without buffering.
     *
     * @var int
     */
    public $bufferSize = 500;

    /**
     * Haldling job id
     *
     * @var integer
     */
    protected $jobId;
    /**
     * Stat day date
     *
     * @var string "Y-m-d"
     */
    protected $date;
    /**
     * Inserting data buffer
     *
     * @var array
     */
    protected $dataBuffer = [];
    /**
     * Error messages while handling
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Collect and save job stat for $date
     *
     * @param Job $job
     * @param string $date "Y-m-d"
     *
     * @return bool TRUE if success, FALSE otherwise, see [getErrors()] for details
     */
    public function processJob(Job $job, $date)
    {
        $this->errors = [];
        $this->jobId = (int)$job->id;
        $this->date = $date;

        $transaction = Stat::getDb()->beginTransaction();
        try {
            $this->clearPreviousStats();
            $this->collectStats();

            $transaction->commit();
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            $transaction->rollBack();

            return false;
        }

        return true;
    }

    /**
     * Clear previous stat values (before insert new data)
     *
     * @return int Deleted items count
     */
    protected function clearPreviousStats()
    {
        return Stat::deleteAll(['jobId' => $this->jobId, 'date' => $this->date]);
    }

    /**
     * Select job stat values for date = $this->date and save
     */
    protected function collectStats()
    {
        // SELECT FROM operations WHERE `jobId` = 123 AND `createdAt` BETWEEN "2017-11-22 00:00:00" AND "2017-11-22 23:59:59"
        $baseSelectQuery = (new Query())->from(Operation::tableName())->where(
            ['and', ['jobId' => $this->jobId], ['between', 'createdAt', $this->date . ' 00:00:00', $this->date . ' 23:59:59']]
        );

        // do not use PHP7 syle (clone Object)->select(...) for PHP5 compatibility
        $avgDurationQuery = clone $baseSelectQuery;
        $avgDurationQuery->select(['AVG(`duration`)']);

        $minOperationQuery = clone $baseSelectQuery;
        $minOperationQuery->select(['id'])->orderBy(['duration' => SORT_ASC])->limit(1);

        $maxOperationQuery = clone $baseSelectQuery;
        $maxOperationQuery->select(['id'])->orderBy(['duration' => SORT_DESC])->limit(1);

        $operationsCountQuery = clone $baseSelectQuery;
        $operationsCountQuery->select(['COUNT(*)']);

        $query = (new Query())->select([
            'avgDuration' => $avgDurationQuery,
            'minOperationId' => $minOperationQuery,
            'maxOperationId' => $maxOperationQuery,
            'operationsCount' => $operationsCountQuery,
        ]);

        $result = $query->one();
        $this->insertData($result);
    }

    /**
     * Finishing stat handler
     */
    public function finish()
    {
        // Insert last stat data
        $this->insertData([], 0);

        $this->jobId = $this->date = null;
        $this->dataBuffer = [];
    }

    /**
     * Insert stat data item to the db
     *
     * @param array $data         ['avgDuration' => 10.99, 'minOperationId' => 2, 'maxOperationId' => 5, 'operationsCount' => 20]
     * @param int|null $batchSize Max data biffer size, if buffer reached it value - data will be inserted to DB.
     *                            By default(NULL) using $this->bufferSize value.
     *                            Value 0 meaning insert each row without buffering.
     */
    protected function insertData(array $data, $batchSize = null)
    {
        if ($data) {
            $this->dataBuffer[] = [
                $this->jobId, // jobId
                $this->date, // date
                round($data['avgDuration'], 3), // avgDuration
                $data['minOperationId'] ? (int)$data['minOperationId'] : null, // minOperationId
                $data['maxOperationId'] ? (int)$data['maxOperationId'] : null, // maxOperationId
                (int)$data['operationsCount'], // operationsCount
            ];
        }
        if ($batchSize === null) {
            $batchSize = $this->bufferSize;
        }

        $itemsCount = count($this->dataBuffer);
        // If no data or buffer size not reached - skip
        if ($itemsCount == 0 || $itemsCount < $batchSize) {
            return;
        }

        // Buffer is full -> insert to DB
        \Yii::$app->db->createCommand()->batchInsert(Stat::tableName(),
            ['jobId', 'date', 'avgDuration', 'minOperationId', 'maxOperationId', 'operationsCount'],
            $this->dataBuffer
        )->execute();

        // Clear buffer
        $this->dataBuffer = [];
    }

    /**
     * Get errors array
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
