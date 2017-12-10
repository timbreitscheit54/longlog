<?php

namespace common\helpers\enum;

use Yii;

/**
 * User statuses enumerable class
 */
class UserStatus extends BasicEnum
{
    const __default = self::INACTIVE;

    const INACTIVE = 0;
    const ACTIVE = 1;

    /**
     * @inheritdoc
     */
    protected static function labels()
    {
        return [
            static::ACTIVE => Yii::t('app', 'USER_STATUS_ACTIVE'),
            static::INACTIVE => Yii::t('app', 'USER_STATUS_INACTIVE'),
        ];
    }
}