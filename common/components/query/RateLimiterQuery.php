<?php

namespace common\components\query;

/**
 * This is the ActiveQuery class for [[\common\models\RateLimiter]].
 *
 * @see \common\models\RateLimiter
 */
class RateLimiterQuery extends \yii\db\ActiveQuery
{
    /**
     * Add general conditions
     *
     * @return $this
     */
    /*public function general()
    {
        $tableName = \common\models\RateLimiter::tableName();

        $this->select([
            "$tableName.route",
            "$tableName.userIp",
            "$tableName.time",
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
        return $this->andWhere(['rateLimiter.status' => \common\models\RateLimiter::STATUS_ACTIVE]);
    }*/

    /**
     * @inheritdoc
     * @return \common\models\RateLimiter[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\RateLimiter|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return \yii\db\BatchQueryResult|\common\models\RateLimiter[]|array
     */
    public function each($batchSize = 100, $db = null)
    {
        return parent::each($batchSize, $db);
    }
}
