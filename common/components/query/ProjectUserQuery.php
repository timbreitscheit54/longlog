<?php

namespace common\components\query;

/**
 * This is the ActiveQuery class for [[\common\models\ProjectUser]].
 *
 * @see \common\models\ProjectUser
 */
class ProjectUserQuery extends \yii\db\ActiveQuery
{
    /**
     * Add general conditions
     *
     * @return $this
     */
    /*public function general()
    {
        $tableName = \common\models\ProjectUser::tableName();

        $this->select([
            "$tableName.projectId",
            "$tableName.userId",
            "$tableName.role",
            "$tableName.createdAt",
            "$tableName.updatedAt",
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
        $this->andWhere(['projectUsers.status' => \common\models\ProjectUser::STATUS_ACTIVE]);

        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \common\models\ProjectUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\ProjectUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}