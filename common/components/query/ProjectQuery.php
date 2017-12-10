<?php

namespace common\components\query;

/**
 * This is the ActiveQuery class for [[\common\models\Project]].
 *
 * @see \common\models\Project
 */
class ProjectQuery extends \yii\db\ActiveQuery
{
    /**
     * Add general conditions
     *
     * @return $this
     */
    /*public function general()
    {
        $tableName = \common\models\Project::tableName();

        $this->select([
            "$tableName.id",
            "$tableName.name",
            "$tableName.token",
            "$tableName.ownerId",
            "$tableName.deletedAt",
            "$tableName.createdAt",
            "$tableName.updatedAt",
        ]);

        $this->orderBy(["$tableName.created_at" => SORT_DESC]);

        return $this;
    }*/

    /**
     * Filter only active projects
     *
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['projects.deletedAt' => null]);
    }

    /**
     * @inheritdoc
     * @return \common\models\Project[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Project|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return \yii\db\BatchQueryResult|\common\models\Project[]|array
     */
    public function each($batchSize = 100, $db = null)
    {
        return parent::each($batchSize, $db);
    }
}
