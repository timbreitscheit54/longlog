<?php

namespace common\helpers\enum;

/**
 * User roles enumerable class
 */
class UserRole extends BasicEnum
{
    const __default = self::MANAGER;

    /**
     * Only view access
     */
    const VIEWER = 'viewer';
    /**
     * Can create and manage own projects
     */
    const MANAGER = 'manager';
    /**
     * Backend user, full access
     */
    const ADMIN = 'admin';

    /**
     * @inheritdoc
     */
    protected static function labels()
    {
        return [
            static::VIEWER => 'Viewer',
            static::MANAGER => 'Manager',
            static::ADMIN => 'Admin',
        ];
    }
}
