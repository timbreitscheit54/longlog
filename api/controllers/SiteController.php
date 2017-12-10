<?php

namespace api\controllers;

use common\components\rateLimiter\AuthRateLimiter;
use common\models\User;
use Yii;
use yii\base\UserException;
use yii\filters\RateLimiter;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionError()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        Yii::$app->getResponse()->setStatusCodeByException($exception);

        if ($exception instanceof \yii\base\Exception) {
            $name = $exception->getName();
        } else {
            $name = Yii::t('yii', 'Error');
        }

        $code = $exception->getCode();
        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        }
        if ($code) {
            $name .= " (#$code)";
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('yii', 'An internal server error occurred.');
        }

        return [
            'name' => $name,
            'message' => $message,
            'code' => $exception->getCode(),
            'status' => $code,
            'type' => get_class($exception),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
            'rateLimiter' => [
                'class' => RateLimiter::className(),
                'user' => new AuthRateLimiter(),
                'only' => ['auth'],
            ],
        ]);
    }

    /**
     * Get user access token
     * @return array
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAuth()
    {
        $login = Yii::$app->request->post('login');
        $password = Yii::$app->request->post('password');

        if (!$login) {
            throw new BadRequestHttpException('Login required');
        } elseif (!mb_strlen($password)) {
            throw new BadRequestHttpException('Password required');
        }

        $user = User::findByUsername($login);
        if (!$user || !$user->validatePassword($password)) {
            throw new BadRequestHttpException('User not found or password incorrect');
        }

        return [
            'accessToken' => $user->accessToken,
        ];
    }

    /**
     * Check client API version
     *
     * @param string $version Client API version
     *
     * @return array
     */
    public function actionCheckVersion($version)
    {
        $supportedVersions = Yii::$app->params['supportedApiVersions'];
        $latestApiVersion = Yii::$app->params['latestApiVersion'];

        // Check that client API version is supported
        $isCompatible = in_array($version, $supportedVersions);
        // Check that client using latest API version
        $isLatest = $version === $latestApiVersion;

        return [
            'isCompatible' => $isCompatible,
            'isLatest' => $isLatest,
            'latestVersion' => $latestApiVersion,
            'supportedVersions' => $supportedVersions,
        ];
    }
}
