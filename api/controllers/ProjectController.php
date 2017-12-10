<?php

namespace api\controllers;

use api\components\ApiController;
use api\models\NewLogForm;
use api\responses\ProjectResponse;
use common\helpers\enum\ProjectUserRole;
use common\helpers\enum\UserRole;
use common\models\Project;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Project controller
 */
class ProjectController extends ApiController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $nonAuthActions = ['add-log'];

        return ArrayHelper::merge(
            parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'except' => $nonAuthActions,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'jobs'],
                        'allow' => true,
                        'roles' => [UserRole::VIEWER],
                    ],
                ],
            ],
            'bearerAuth' => [
                'except' => $nonAuthActions,
            ],
        ]);
    }

    /**
     * Projects list
     *
     * @return array|\common\models\Project[]
     */
    public function actionIndex()
    {
        $roles = [ProjectUserRole::VIEWER, ProjectUserRole::ADMIN];

        $query = Project::find()
            ->joinWith(['currentProjectUser'])
            ->andWhere(['currentProjectUser.role' => $roles])
            ->active();

        return ProjectResponse::toArrayModels($query->all());
    }

    /**
     * Project item view
     *
     * @param integer $id
     *
     * @return array|\common\models\Project[]
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = Project::find()->with(['jobs'])->where(['id' => $id])->active()->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app/error', 'PROJECT_NOT_FOUND'));
        } elseif (!$model->isViewable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_VIEW_ACCESS_RESTRICTED'));
        }

        return ProjectResponse::toArray($model, true);
    }

    /**
     * Save new log record
     *
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionAddLog()
    {
        $response = Yii::$app->response;
        $request = Yii::$app->request;
        $body = $request->getBodyParams();

        // Project token isset
        $projectToken = !empty($body['projectToken']) ? $body['projectToken'] : null;
        if (!$projectToken) {
            throw new BadRequestHttpException('projectToken required');
        }

        // Project exists
        $projectId = Project::getIdByToken($projectToken);
        if (!$projectId) {
            throw new NotFoundHttpException('Project not found');
        }

        $model = new NewLogForm();
        $model->projectId = $projectId;
        $model->setAttributes($body);

        if (!$model->validate()) {
            $response->setStatusCode(400);

            return $model->errors;
        }

        // Save new log record
        if (!$model->save()) {
            throw new ServerErrorHttpException('Something went wrong...');
        }

        // New log successfully saved
        $response->setStatusCode(201);

        return $response;
    }
}
