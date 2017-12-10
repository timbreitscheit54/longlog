<?php

namespace common\helpers\enum;

/**
 * API versions enumerable class
 */
class ApiVersion extends BasicEnum
{
    const RED_SNAKE = '1';
    const LATEST = self::RED_SNAKE;

    /**
     * @inheritdoc
     */
    protected static function labels()
    {
        return [
            static::RED_SNAKE => 'Red Snake',
        ];
    }
}
