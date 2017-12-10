<?php

namespace common\components\query;

/**
 * This is the ActiveQuery class for [[\common\models\Operation]].
 *
 * @see \common\models\Operation
 */
class OperationQuery extends \yii\db\ActiveQuery
{
    /**
     * Add general conditions
     *
     * @return $this
     */
    /*public function general()
    {
        $tableName = \common\models\Operation::tableName();

        $this->select([
            "$tableName.id",
            "$tableName.jobId",
            "$tableName.duration",
            "$tableName.payload",
            "$tableName.createdAt",
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
        $this->andWhere(['operations.status' => \common\models\Operation::STATUS_ACTIVE]);

        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \common\models\Operation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Operation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}