<?php

namespace frontend\controllers;

use common\helpers\enum\ProjectUserRole;
use common\helpers\enum\UserRole;
use common\helpers\UserHelper;
use common\models\ProjectInvite;
use common\models\ProjectUser;
use common\models\User;
use Yii;
use common\models\Project;
use frontend\models\ProjectSearch;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
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
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => [UserRole::VIEWER],
                    ],
                    [
                        'actions' => [
                            'create', 'update', 'delete', 'generate-new-token', 'users', 'resend-invite',
                            'change-role', 'change-owner', 'remove-user',
                        ],
                        'allow' => true,
                        'roles' => [UserRole::MANAGER],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'generate-new-token' => ['POST'],
                    'delete' => ['POST'],
                    'change-role' => ['POST'],
                    'change-owner' => ['POST'],
                    'remove-user' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Project model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (!$model->isViewable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_VIEW_ACCESS_RESTRICTED'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Project();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Project model.
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

        if (!$model->isManageable()) {
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
     * Deletes an existing Project model.
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

        if (!$model->isManageable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        }

        $model->safeDelete();

        return $this->redirect(['index']);
    }

    /**
     * Re-generate project token
     *
     * @param integer $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionGenerateNewToken($id)
    {
        $model = $this->findModel($id);

        if (!$model->isManageable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        }

        $model->generateToken();
        $model->save(false, ['token']);

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Displays a Project users listing.
     *
     * @param integer $id Project ID
     *
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionUsers($id)
    {
        $model = $this->findModel($id);
        if (!$model->isManageable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        }

        // New project invite model
        $projectInvite = new ProjectInvite();
        $projectInvite->projectId = $model->id;
        if ($projectInvite->load(Yii::$app->request->post()) && $projectInvite->processNewInvite()) {
            // Refresh current page and show session flash message
            return $this->redirect(['users', 'id' => $id]);
        }

        return $this->render('users', [
            'model' => $model,
            'projectInvite' => $projectInvite,
        ]);
    }

    /**
     * Change project user role.
     * Project owner can downgrade roles.
     * Project admin can only give admin role for viewer.
     *
     * @param integer $id Project ID
     *
     * @return \yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionChangeRole($id)
    {
        $model = $this->findModel($id);

        if (!$model->isManageable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        }

        $request = Yii::$app->request;
        $userId = $request->post('userId');
        $newRole = $request->post('newRole');
        $user = User::find()->where(['id' => $userId])->active()->one();
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app/error', 'USER_NOT_FOUND'));
        } elseif (!ProjectUser::hasAccess($model->id, $user->id, ProjectUserRole::VIEWER)) {
            throw new BadRequestHttpException(Yii::t('app/error', 'USER_NOT_A_PROJECT_MEMEBER'));
        } elseif ($user->id == UserHelper::getCurrentId()) {
            throw new BadRequestHttpException(Yii::t('app/error', 'USER_CANNOT_CHANGE_OWN_ROLE'));
        }

        // Only project owner can downgrade role
        if ($newRole == ProjectUserRole::VIEWER && $model->ownerId != UserHelper::getCurrentId()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'ONLY_PROJECT_OWNER_CAN_DOWNGRADE_ROLE'));
        }

        // Set new role
        ProjectUser::assign($model->id, $user->id, $newRole);

        return $this->redirect(['users', 'id' => $model->id]);
    }

    /**
     * Remove project user
     *
     * @param integer $id ProjectUser ID
     *
     * @return \yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRemoveUser($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $project = $this->findModel($id);
        $userId = Yii::$app->request->post('userId');

        $projectUser = ProjectUser::find()->where(['projectId' => $project->id, 'userId' => $userId])->one();
        if (!$projectUser) {
            throw new NotFoundHttpException(Yii::t('app/error', 'PROJECT_USER_NOT_FOUND'));
        }

        $project = $projectUser->project;
        // Has project admin access
        if (!$project->isManageable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        }
        // Project owner is unremovable
        if ($projectUser->userId == $project->ownerId) {
            throw new BadRequestHttpException(Yii::t('app/error', 'PROJECT_OWNER_UNREMOVABLE'));
        }

        // Removing admin-users allowed only for project owners
        if ($projectUser->role == ProjectUserRole::ADMIN && $project->ownerId != UserHelper::getCurrentId()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'ONLY_PROJECT_OWNER_CAN_REMOVE_ADMIN_USERS'));
        }

        // Remove user
        $projectUser->delete();

        return $this->redirect(['users', 'id' => $project->id]);
    }

    /**
     * Change project owner
     *
     * @param integer $id Project ID
     *
     * @return \yii\web\Response
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionChangeOwner($id)
    {
        $project = $this->findModel($id);
        $newOwnerId = Yii::$app->request->post('newOwnerId');

        if ($project->ownerId != UserHelper::getCurrentId()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'ONLY_PROJECT_OWNER_CAN_CHANGE_OWNER'));
        } elseif (!User::find()->where(['id' => $newOwnerId])->active()->exists()) {
            throw new NotFoundHttpException(Yii::t('app/error', 'NEW_PROJECT_OWNER_USER_NOT_FOUND'));
        }

        if (!$project->changeOwner($newOwnerId)) {
            throw new ServerErrorHttpException(Yii::t('app/error', 'SOMETHING_WENT_WRONG'));
        }

        return $this->redirect(['users', 'id' => $project->id]);
    }

    /**
     * Resend project invite
     *
     * @param integer $id Invite id
     *
     * @return \yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionResendInvite($id)
    {
        $invite = ProjectInvite::find()->where(['id' => $id])->one();
        if (!$invite) {
            throw new NotFoundHttpException(Yii::t('app/error', 'PROJECT_INVITE_NOT_FOUND'));
        } elseif (!$invite->project || !$invite->project->isManageable()) {
            throw new ForbiddenHttpException(Yii::t('app/error', 'PROJECT_MANAGE_ACCESS_RESTRICTED'));
        } elseif ($invite->canResendAfter() > 0) {
            throw new BadRequestHttpException(Yii::t('app/error', 'PROJECT_INVITE_RESEND_INTERVAL_ERROR_{after}',
                ['after' => Yii::$app->formatter->asDuration($invite->canResendAfter())]));
        }

        // Send email again
        $invite->sendEmail();

        return $this->redirect(['users', 'id' => $invite->projectId]);
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::find()->where(['id' => $id])->active()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app/error', 'PROJECT_NOT_FOUND'));
        }
    }
}
