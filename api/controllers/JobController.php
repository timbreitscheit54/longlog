<?php

namespace api\controllers;

use api\components\ApiController;
use api\responses\JobResponse;
use common\helpers\enum\UserRole;
use common\models\Job;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Job controller
 */
class JobController extends ApiController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['stats'],
                        'allow' => true,
                        'roles' => [UserRole::VIEWER],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Job operations
     *
     * @param integer $id Job ID
     *
     * @return array|\common\models\Project[]
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionStats($id)
    {
        $job = Job::find()->where(['id' => $id])->one();

        if (!$job) {
            throw new NotFoundHttpException(Yii::t('app/error', 'JOB_NOT_FOUND'));
        } elseif (!$job->project) {
            throw new NotFoundHttpException(Yii::t('app/error', 'PROJECT_NOT_FOUND'));
        } elseif (!$job->project->isViewable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_VIEW_ACCESS_RESTRICTED'));
        }

        return JobResponse::stats($job);
    }
}
