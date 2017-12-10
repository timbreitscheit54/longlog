<?php

namespace common\helpers\enum;

/**
 * Language enumerable class
 */
class Language extends BasicEnum
{
    const __default = self::EN;

    const EN = 'en-US';
    const RU = 'ru';

    /**
     * @inheritdoc
     */
    protected static function labels()
    {
        return [
            static::EN => 'English',
            static::RU => 'Russian',
        ];
    }
}
