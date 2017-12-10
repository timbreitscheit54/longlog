<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $operation common\models\Operation */

$job = $operation->job;
$project = $job->project;

$this->title = Yii::t('app', 'OPERATION_VIEW_TITLE_{jobName}{operationId}',
    ['jobName' => $job->fTitle, 'operationId' => $operation->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'PROJECTS'), 'url' => ['project/index']];
$this->params['breadcrumbs'][] = ['label' => $project->name, 'url' => ['project/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => $job->fTitle, 'url' => ['job/view', 'id' => $job->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'OPERATION_{id}', ['id' => $operation->id]);
?>
<div class="job-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $operation,
        'attributes' => [
            'id',
            [
                'attribute' => 'duration',
                'value' => $operation->duration . ' (' . Yii::$app->formatter->asDuration($operation->duration) . ')'
            ],
            'payload',
            'createdAt:datetime',
        ],
    ]) ?>

</div>
