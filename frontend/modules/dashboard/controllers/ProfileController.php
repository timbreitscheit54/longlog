<?php

namespace frontend\modules\dashboard\controllers;

use common\models\SecureKey;
use common\models\User;
use frontend\modules\dashboard\models\ProfileForm;
use frontend\modules\dashboard\models\ResetPasswordForm;
use frontend\modules\dashboard\models\ResetPasswordRequestForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ProfileController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [
                            'activate', 'change-email', 'reset-password', 'request-reset-password',
                            'successful-activation', 'successful-email-changing', 'successful-password-resetting',
                            'successful-request-password-resetting',
                        ],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Update user profile
     */
    public function actionUpdate()
    {
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            throw new ForbiddenHttpException('This user does not exist or has been removed');
        }

        $model = new ProfileForm();

        // Передаём в форму модельку текущего пользователя, тут же происходит присвоение аттрибутов пользователя полям формы
        $model->user = $user;

        if ($model->load(Yii::$app->request->post()) && $model->updateProfile()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'DASHBOARD_PROFILE_SAVED'));

            return $this->redirect(['update']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Check secure key on errors
     *
     * @param null|SecureKey $key
     *
     * @throws \yii\web\NotFoundHttpException
     */
    private function checkSecureKey($key)
    {
        // Ключ не найден
        if (!$key instanceof SecureKey) {
            throw new NotFoundHttpException(Yii::t('app/error', 'SECURE_KEY_NOT_FOUND'));
        }

        // Ключ был использован
        if ($key->status == SecureKey::STATUS_USED) {
            throw new NotFoundHttpException(Yii::t('app/error', 'SECURE_KEY_ALREADY_USED'));
        }

        // Был выдан более новый ключ
        if ($key->status == SecureKey::STATUS_FORGOTTEN) {
            throw new NotFoundHttpException(Yii::t('app/error', 'SECURE_KEY_NEW_KEY_ALREADY_ISSUED'));
        }

        // Срок годности ключа истёк
        if (!$key->isValidKey()) {
            throw new NotFoundHttpException(Yii::t('app/error', 'SECURE_KEY_NOT_VALID'));
        }
    }

    /**
     * Email confirmation
     *
     * @param integer $id
     * @param string $code
     *
     * @return string|\yii\web\Response
     */
    public function actionActivate($id, $code)
    {
        $key = SecureKey::getKey($id, $code);
        $this->checkSecureKey($key);

        if ($key->activate()) {
            // Show successfull message
            return $this->redirect(['successful-activation']);
        }

        // Show error message
        return $this->render('errorSecureKey', ['key' => $key]);
    }

    /**
     * Show successfull activated message
     */
    public function actionSuccessfulActivation()
    {
        return $this->render('messages/successfulActivation');
    }

    /**
     * Email changing confirmation
     *
     * @param integer $id
     * @param string $code
     *
     * @return string|\yii\web\Response
     */
    public function actionChangeEmail($id, $code)
    {
        $key = SecureKey::getKey($id, $code);
        $this->checkSecureKey($key);

        if ($key->activate() && $key->user instanceof User) {
            // Show successfull message
            Yii::$app->session->setFlash('newEmail', $key->user->email);

            return $this->redirect(['successful-email-changing']);
        }

        // Show error message
        return $this->render('errorSecureKey', ['key' => $key]);
    }

    /**
     * Show successfull email changing message
     */
    public function actionSuccessfulEmailChanging()
    {
        $newEmail = Yii::$app->session->getFlash('newEmail');

        return $this->render('messages/successfulEmailChanging', ['newEmail' => $newEmail]);
    }

    /**
     * Reset password request form
     *
     * @return string|\yii\web\Response
     */
    public function actionRequestResetPassword()
    {
        $model = new ResetPasswordRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->sendRequest()) {
            return $this->redirect(['successful-request-password-resetting']);
        }

        return $this->render('requestResetPasswordForm', ['model' => $model]);
    }

    /**
     * Reset password request successfull message
     */
    public function actionSuccessfulRequestPasswordResetting()
    {
        return $this->render('messages/successfulRequestPasswordResetting');
    }

    /**
     * Reset password form
     *
     * @param integer $id
     * @param string $code
     *
     * @throws \yii\web\ForbiddenHttpException
     * @return string
     */
    public function actionResetPassword($id, $code)
    {
        $key = SecureKey::getKey($id, $code);
        $this->checkSecureKey($key);

        $user = $key->user;

        if (!$user) {
            throw new ForbiddenHttpException('This user does not exist or has been removed');
        }

        $model = new ResetPasswordForm();
        $model->user = $user;

        if ($model->load(Yii::$app->request->post()) && $model->updatePassword()) {
            $key->activate();

            return $this->redirect(['successful-password-resetting']);
        }

        return $this->render('resetPasswordForm', ['model' => $model]);
    }

    /**
     * Reset password successfull message
     */
    public function actionSuccessfulPasswordResetting()
    {
        return $this->render('messages/successfulPasswordResetting');
    }
}
