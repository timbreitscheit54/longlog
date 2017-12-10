<?php

namespace common\components\query;

use common\models\Job;
use common\models\Operation;

/**
 * This is the ActiveQuery class for [[Stat]].
 *
 * @see Stat
 */
class StatQuery extends \yii\db\ActiveQuery
{
    /**
     * Add general conditions
     *
     * @return $this
     */
    /*public function general()
    {
        $tableName = Stat::tableName();

        $this->select([
            "$tableName.id",
            "$tableName.date",
            "$tableName.avgDuration",
            "$tableName.minOperationId",
            "$tableName.maxOperationId",
            "$tableName.operationsCount",
        ]);

        $this->orderBy(["$tableName.created_at" => SORT_DESC]);

        return $this;
    }*/

    /**
     * Filter only active data
     *
     * @return $this
     */
    /*public function active()
    {
        $this->andWhere(['stats.status' => Stat::STATUS_ACTIVE]);

        return $this;
    }*/

    /**
     * Filter projects where have at least one operation in the period $since - now
     *
     * @param string $since Datetime string: "Y-m-d H:i:s"
     *
     * @return $this
     */
    public function andWhereHasOperations($since)
    {
        $operationsQuery = Operation::find()
            ->select(['operations.jobId'])
            ->where('operations.jobId = jobs.id')
            ->andWhere(['>=', 'operations.createdAt', $since])
            ->groupBy(['operations.jobId']);

        $jobsQuery = Job::find()
            ->select(['jobs.projectId'])
            ->where('jobs.projectId = projects.id')
            ->andWhere(['in', 'jobs.id', $operationsQuery])
            ->groupBy(['jobs.projectId']);

        return $this->andWhere(['in', 'projects.id', $jobsQuery]);
    }

    /**
     * @inheritdoc
     * @return Stat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Stat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
