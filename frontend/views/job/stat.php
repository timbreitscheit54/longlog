<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $job common\models\Job */
/* @var $stat common\models\Stat */

$project = $job->project;
$statFormattedDate = Yii::$app->formatter->asDate($stat->date);

$this->title = Yii::t('app', 'JOB_VIEW_STAT_TITLE_{jobName}{date}',
    ['jobName' => $job->fTitle, 'date' => $statFormattedDate]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'PROJECTS'), 'url' => ['project/index']];
$this->params['breadcrumbs'][] = ['label' => $project->name, 'url' => ['project/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => $job->fTitle, 'url' => ['job/view', 'id' => $job->id]];
$this->params['breadcrumbs'][] = $statFormattedDate;
?>
<div class="job-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $stat,
        'attributes' => [
            'jobId',
            'date:date',
            [
                'attribute' => 'avgDuration',
                'value' => $stat->avgDuration . ' (' . Yii::$app->formatter->asDuration($stat->avgDuration) . ')'
            ],
            [
                'attribute' => 'minOperationId',
                'format' => 'raw',
                'value' => $stat->minOperation
                    ? Html::a($stat->minOperation->duration . ' (' . Yii::$app->formatter->asDuration(
                            $stat->minOperation->duration) . ')', ['operation', 'id' => $stat->minOperationId])
                    : null,
            ],
            [
                'attribute' => 'maxOperationId',
                'format' => 'raw',
                'value' => $stat->maxOperation
                    ? Html::a($stat->maxOperation->duration . ' (' . Yii::$app->formatter->asDuration(
                            $stat->maxOperation->duration) . ')', ['operation', 'id' => $stat->maxOperationId])
                    : null,
            ],
            'operationsCount:integer',
        ],
    ]) ?>

</div>
