<?php

namespace common\helpers;

use common\helpers\enum\UserRole;
use Yii;
use common\models\User;

class UserHelper
{
    /**
     * Safety getting current authenticated user id
     *
     * @param int|null|string $defaultValue
     *
     * @return int|null|string
     */
    public static function getCurrentId($defaultValue = null)
    {
        return Yii::$app->has('user') ? (int)Yii::$app->user->getId() : $defaultValue;
    }

    /**
     * Checks if current user has access to project management
     *
     * @return bool
     */
    public static function canManage()
    {
        return Yii::$app->has('user') && Yii::$app->user->can(UserRole::MANAGER);
    }

    /**
     * Checks if current user has access to backend zone
     *
     * @return bool
     */
    public static function canRoot()
    {
        return Yii::$app->has('user') && Yii::$app->user->can(UserRole::ADMIN);
    }

    /**
     * Checks if the current user is search robot
     *
     * @return bool
     */
    public static function isRobot()
    {
        $userAgent = Yii::$app->request->userAgent;

        if (empty($userAgent)) {
            return true;
        }

        $robotsUserAgents = [
            'bot',
            'spider',
            'crawler',
            'curl',
            'Wget',
            'XGET',
        ];

        $isRobot = preg_match('/' . implode('|', $robotsUserAgents) . '|^$/i', $userAgent);

        return (bool)$isRobot;
    }
}
