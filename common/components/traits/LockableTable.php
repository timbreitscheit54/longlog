<?php

namespace common\components\traits;

use yii\db\Connection;

/**
 * Trait LockableTable
 * Lock/Unlock table(s) methods
 *
 * @see https://dev.mysql.com/doc/refman/5.7/en/lock-tables.html
 */
trait LockableTable
{
    /**
     * Lock table(s)
     *
     * @param string $level                   "READ" or "WRITE", by default "WRITE"
     * @param string|array|null $customTables Table name(s) or tables list, by default NULL - current AR table
     * @param Connection|null $db             Custom database connection
     */
    public static function lockTable($level = 'WRITE', $customTables = null, Connection $db = null)
    {
        if (!$db) {
            $db = static::getDb();
        }

        $tables = [];
        if ($customTables === null) {
            $tables[] = $db->quoteTableName(static::tableName()) . ' ' . $level;
        } elseif (is_array($customTables)) {
            foreach ($customTables as $table) {
                $tables[] = $db->quoteTableName($table) . ' ' . $level;
            }
        } elseif (is_string($customTables)) {
            foreach (explode(',', $customTables) as $table) {
                $tables[] = $db->quoteTableName(trim($table)) . ' ' . $level;
            }
        }

        $db->createCommand('LOCK TABLES ' . implode(', ', $tables))->execute();
    }

    /**
     * Unlock all locked tables
     */
    public static function unlockTable()
    {
        /** @var \yii\db\Connection $db */
        $db = static::getDb();
        $db->createCommand('UNLOCK TABLES')->execute();
    }
}
