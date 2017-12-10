<?php

use common\helpers\UserHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Project */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'PROJECTS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$canManage = $model->isManageable();
?>
<div class="project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($canManage): ?>
        <p>
            <?= Html::a(Yii::t('app', 'BTN_UPDATE'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'BTN_PROJECT_USERS'), ['users', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
            <?= Html::a(Yii::t('app', 'PROJECT_ADD_JOB'), ['job/create', 'projectId' => $model->id],
                ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('app', 'BTN_PROJECT_NEW_TOKEN'), ['generate-new-token', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => Yii::t('app', 'PROJECT_NEW_TOKEN_CONFIRMATION'),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(Yii::t('app', 'BTN_DELETE'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'DELETE_CONFIRMATION'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'token' => [
                'attribute' => 'token',
                'visible' => $canManage,
            ],
            'createdAt:datetime',
            'updatedAt:datetime',
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'PROJECT_JOBS') ?>:</h3>
    <div id="project-jobs">
        <?php
        if (!$model->jobs) {
            echo '<span class="project_no_jobs">' . Yii::t('app', 'PROJECT_HAS_NO_JOBS') . '</span>';
        } else {
            foreach ($model->jobs as $job) {
                echo $this->render('_job', ['model' => $job]);
            }
        }
        ?>
    </div>
</div>
