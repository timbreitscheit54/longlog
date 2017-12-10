<?php

use common\helpers\UserHelper;
use frontend\widgets\JobOperationsChart;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Job */

$this->title = $model->title ? $model->title : $model->key;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'PROJECTS'), 'url' => ['project/index']];
$this->params['breadcrumbs'][] = ['label' => $model->project->name, 'url' => ['project/view', 'id' => $model->projectId]];
$this->params['breadcrumbs'][] = $this->title;
$canManage = $model->project->isManageable();
?>
<div class="job-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($canManage): ?>
        <p>
            <?= Html::a(Yii::t('app', 'BTN_UPDATE'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            [
                'attribute' => 'projectId',
                'format' => 'raw',
                'value' => Html::a($model->project->fName, ['project/view', 'id' => $model->projectId]),
            ],
            'key',
            'title',
            [
                'attribute' => 'critDuration',
                'visible' => $model->critDuration > 0,
                'value' => $model->critDuration . ' (' . Yii::$app->formatter->asDuration($model->critDuration) . ')'
            ],
            'createdAt:datetime',
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'JOB_OPERATIONS') ?></h3>

    <?= JobOperationsChart::widget(['jobModel' => $model]) ?>
</div>
