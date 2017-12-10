<?php

namespace common\components\query;

/**
 * This is the ActiveQuery class for [[\common\models\Job]].
 *
 * @see \common\models\Job
 */
class JobQuery extends \yii\db\ActiveQuery
{
    /**
     * Add general conditions
     *
     * @return $this
     */
    /*public function general()
    {
        $tableName = \common\models\Job::tableName();

        $this->select([
            "$tableName.id",
            "$tableName.projectId",
            "$tableName.key",
            "$tableName.title",
            "$tableName.critDuration",
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
        $this->andWhere(['jobs.status' => \common\models\Job::STATUS_ACTIVE]);

        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \common\models\Job[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Job|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}