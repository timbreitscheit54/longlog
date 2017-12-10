<?php

namespace common\helpers;

use common\helpers\enum\ProjectUserRole;
use common\models\ProjectUser;
use Yii;
use yii\base\InvalidParamException;

class ProjectHelper
{
    /**
     * Check that $userId has view access $projectId
     *
     * @param integer $projectId
     * @param int|null $userId Optional user id, by default using current logged-in user id
     *
     * @return bool
     */
    public static function checkViewAccess($projectId, $userId = null)
    {
        return static::checkAccess($projectId, [ProjectUserRole::VIEWER, ProjectUserRole::ADMIN], $userId);
    }

    /**
     * Check that $userId has admin access to $projectId
     *
     * @param integer $projectId
     * @param int|null $userId Optional user id, by default using current logged-in user id
     *
     * @return bool
     */
    public static function checkManageAccess($projectId, $userId = null)
    {
        return static::checkAccess($projectId, ProjectUserRole::ADMIN, $userId);
    }

    /**
     * Check that $userId have access(by $roles) to $projectId
     *
     * @param integer $projectId
     * @param array|string $roles Role name or Role names array
     * @param int|null $userId    Optional user id, by default using current logged-in user id
     *
     * @return bool
     */
    public static function checkAccess($projectId, $roles, $userId = null)
    {
        if (!$userId) {
            $userId = UserHelper::getCurrentId();
        }
        if (!$userId) {
            throw new InvalidParamException('$userId must be specified');
        }
        if (!$roles) {
            throw new InvalidParamException('$roles cannot be empty');
        }

        return ProjectUser::find()->where([
            'projectId' => $projectId,
            'userId' => $userId,
            'role' => $roles,
        ])->exists();
    }
}
