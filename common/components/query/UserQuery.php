<?php

namespace common\components\query;

/**
 * This is the ActiveQuery class for [[\common\models\User]].
 *
 * @see \common\models\User
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * Only active users
     *
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['deletedAt' => null]);
    }

    /**
     * @inheritdoc
     * @return \common\models\User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
