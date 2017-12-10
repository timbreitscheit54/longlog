<?php

namespace common\helpers\enum;

use Yii;

/**
 * Project invite statuses enumerable class
 */
class ProjectInviteStatus extends BasicEnum
{
    const __default = self::SENT;

    const SENT = 'sent';
    const ACCEPTED = 'accepted';

    /**
     * @inheritdoc
     */
    protected static function labels()
    {
        return [
            static::SENT => Yii::t('app', 'PROJECT_INVITE_STATUS_SENT'),
            static::ACCEPTED => Yii::t('app', 'PROJECT_INVITE_STATUS_ACCEPTED'),
        ];
    }
}
