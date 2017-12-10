<?php

namespace common\components\query;

/**
 * This is the ActiveQuery class for [[\common\models\ProjectInvite]].
 *
 * @see \common\models\ProjectInvite
 */
class ProjectInviteQuery extends \yii\db\ActiveQuery
{
    /**
     * Add general conditions
     *
     * @return $this
     */
    /*public function general()
    {
        $tableName = \common\models\ProjectInvite::tableName();

        $this->select([
            "$tableName.id",
            "$tableName.projectId",
            "$tableName.email",
            "$tableName.code",
            "$tableName.role",
            "$tableName.status",
            "$tableName.createdBy",
            "$tableName.sentAt",
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
        return $this->andWhere(['projectInvites.status' => \common\models\ProjectInvite::STATUS_ACTIVE]);
    }*/

    /**
     * @inheritdoc
     * @return \common\models\ProjectInvite[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\ProjectInvite|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return \yii\db\BatchQueryResult|\common\models\ProjectInvite[]|array
     */
    public function each($batchSize = 100, $db = null)
    {
        return parent::each($batchSize, $db);
    }
}
