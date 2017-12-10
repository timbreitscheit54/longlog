<?php

namespace frontend\controllers;

use common\helpers\enum\UserRole;
use common\helpers\ProjectHelper;
use common\models\Operation;
use Yii;
use common\models\Job;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JobController implements the CRUD actions for Job model.
 */
class JobController extends Controller
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
                        'actions' => ['view', 'operation', 'stat'],
                        'allow' => true,
                        'roles' => [UserRole::VIEWER],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => [UserRole::MANAGER],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Displays a single Job model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (!$model->project || !$model->project->isViewable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_VIEW_ACCESS_RESTRICTED'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a Job statistics by date
     *
     * @param integer $id
     * @param string $date "Y-m-d"
     *
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionStat($id, $date)
    {
        $model = $this->findModel($id);
        if (!$model->project || !$model->project->isViewable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_VIEW_ACCESS_RESTRICTED'));
        }

        $stat = $model->getStats()->andWhere(['date' => $date])->one();
        if (!$stat) {
            throw new NotFoundHttpException(Yii::t('app/error', 'JOB_STAT_NOT_EXIST'));
        }

        return $this->render('stat', [
            'job' => $model,
            'stat' => $stat,
        ]);
    }

    /**
     * Creates a new Job model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $projectId
     *
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionCreate($projectId)
    {
        if (!ProjectHelper::checkManageAccess($projectId)) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        }

        $model = new Job();
        $model->projectId = $projectId;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Job model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!ProjectHelper::checkManageAccess($model->projectId)) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Job model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!ProjectHelper::checkManageAccess($model->projectId)) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * View operation details
     *
     * @param integer $id operation ID
     *
     * @return string
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionOperation($id)
    {
        $operation = Operation::find()->where(['id' => $id])->one();

        if (!$operation) {
            // Not found
            throw new NotFoundHttpException(Yii::t('app/error', 'OPERATION_NOT_FOUND'));
        } elseif (!$operation->job || !$operation->job->project || !$operation->job->project->isViewable()) {
            // Check restricted
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_VIEW_ACCESS_RESTRICTED'));
        }

        return $this->render('operation', ['operation' => $operation]);
    }

    /**
     * Finds the Job model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Job the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Job::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
