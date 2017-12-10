<?php

namespace common\helpers\enum;

use Yii;

/**
 * Project user roles enumerable class
 */
class ProjectUserRole extends BasicEnum
{
    const __default = self::VIEWER;

    const VIEWER = 'viewer';
    const ADMIN = 'admin';

    /**
     * @inheritdoc
     */
    protected static function labels()
    {
        return [
            static::VIEWER => Yii::t('app', 'PROJECT_USER_ROLE_VIEWER'),
            static::ADMIN => Yii::t('app', 'PROJECT_USER_ROLE_ADMIN'),
        ];
    }
}
