<?php

namespace common\helpers;

use Yii;

class UrlHelper
{
    /**
     * Generate absolute link to frondend
     *
     * @param string|array $params use a string to represent a route (e.g. `site/index`),
     *                             or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
     * @param string $scheme       the scheme to use for the url (either `http` or `https`). If not specified
     *                             the scheme of the current request will be used.
     *
     * @return string the created URL
     */
    public static function getFrontendUrl($params, $scheme = null)
    {
        // Change current host to frontend
        $hostInfo = Yii::$app->urlManager->hostInfo;
        $frontendSchema = YII_DEBUG ? 'http://' : 'http://';
        Yii::$app->urlManager->hostInfo = str_replace('http://admin.', $frontendSchema, $hostInfo);

        // Generate url
        $url = Yii::$app->urlManager->createAbsoluteUrl($params, $scheme);

        // Rollback host info
        Yii::$app->urlManager->hostInfo = $hostInfo;

        return $url;
    }

    /**
     * Generate absolute link to backend
     *
     * @param string|array $params use a string to represent a route (e.g. `site/index`),
     *                             or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
     * @param string $scheme       the scheme to use for the url (either `http` or `https`). If not specified
     *                             the scheme of the current request will be used.
     *
     * @return string the created URL
     */
    public static function getBackendUrl($params, $scheme = null)
    {
        // Change current host to backend
        $hostInfo = Yii::$app->urlManager->hostInfo;

        if (strpos($hostInfo, 'http://admin.', 0) === false) {
            $frontendSchema = YII_DEBUG ? 'http://' : 'http://';
            Yii::$app->urlManager->hostInfo = str_replace($frontendSchema, 'http://admin.', $hostInfo);
        }

        // Generate url
        $url = Yii::$app->urlManager->createAbsoluteUrl($params, $scheme);

        // Rollback host info
        Yii::$app->urlManager->hostInfo = $hostInfo;

        return $url;
    }
}
