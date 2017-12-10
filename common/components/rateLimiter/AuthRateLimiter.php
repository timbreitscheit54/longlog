<?php

namespace common\components\rateLimiter;

use common\models\RateLimiter;
use Yii;
use yii\base\BaseObject;
use yii\filters\RateLimitInterface;

/**
 * Auth rate limiter
 *
 * Max 60 login requests allowed per minute
 */
class AuthRateLimiter extends BaseObject implements RateLimitInterface
{
    protected $maxRequestsPerPeriod = 30;
    protected $period = 60;

    public function __construct($maxRequests = 30, $period = 60, array $config = [])
    {
        $this->maxRequestsPerPeriod = $maxRequests;
        $this->period = $period;

        parent::__construct($config);
    }

    /**
     * Returns the maximum number of allowed requests and the window size.
     *
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action  the action to be executed
     *
     * @return array an array of two elements. The first element is the maximum number of allowed requests,
     * and the second element is the size of the window in seconds.
     */
    public function getRateLimit($request, $action)
    {
        // 30 times per minute
        return [$this->maxRequestsPerPeriod, $this->period];
    }

    /**
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     *
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action  the action to be executed
     *
     * @return array an array of two elements. The first element is the number of allowed requests,
     * and the second element is the corresponding UNIX timestamp.
     */
    public function loadAllowance($request, $action)
    {
        $since = time() - $this->period;
        $count = RateLimiter::getRequestsCount($action->controller->route, $since, $request->userIP);

        return [$this->maxRequestsPerPeriod - $count, time()];
    }

    /**
     * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
     *
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action  the action to be executed
     * @param int $allowance            the number of allowed requests remaining.
     * @param int $timestamp            the current timestamp.
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        RateLimiter::addRequest($action->controller->route, $request->userIP, $timestamp);
    }
}
