<?php

namespace common\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "rateLimiter".
 *
 * @property string $route
 * @property integer $userIp
 * @property integer $time
 */
class RateLimiter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rateLimiter';
    }

    /**
     * @inheritdoc
     * @return \common\components\query\RateLimiterQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\components\query\RateLimiterQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DEFAULT] = [];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [['route', 'userIp', 'time'], 'required'],
            // integer
            [['userIp', 'time'], 'integer'],
            // string
            [['route'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'route' => 'Route',
            'userIp' => 'User Ip',
            'time' => 'Time',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // something...

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // something...

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        // Do not forget remove related data!

        parent::afterDelete();
    }

    /**
     * Return requests count for $route by $userIp since $since
     *
     * @param string $route  Controller + Action: "site/login"
     * @param integer $since Since datetime: unix timestamp
     * @param string $userIp User ip: "123.123.123.123"
     *
     * @return int Requests count
     */
    public static function getRequestsCount($route, $since, $userIp)
    {
        return (int)static::find()->where([
            'and',
            [
                'route' => $route,
                'userIp' => new Expression('INET_ATON(:userIp)', [':userIp' => $userIp]),
            ],
            ['>=', 'time', $since],
        ])->count();
    }

    /**
     * Insert new request log
     *
     * @param string $route       Controller + Action: "site/login"
     * @param string $userIp      User ip: "123.123.123.123"
     * @param int|null $timestamp Request timestamp
     *
     * @return bool
     */
    public static function addRequest($route, $userIp, $timestamp = null)
    {
        if (!$timestamp) {
            $timestamp = time();
        }

        return (bool)static::getDb()->createCommand()->insert(static::tableName(), [
            'route' => $route,
            'userIp' => new Expression('INET_ATON(:userIp)', [':userIp' => $userIp]),
            'time' => $timestamp,
        ])->execute();
    }
}
