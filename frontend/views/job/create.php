<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Job */

$this->title = Yii::t('app', 'CREATE_NEW_JOB');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'PROJECTS'), 'url' => ['project/index']];
$this->params['breadcrumbs'][] = ['label' => $model->project->name, 'url' => ['project/view', 'id' => $model->projectId]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="job-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-warning" role="alert">
        <?= Yii::t('app', 'NEW_JOB_NOT_NECESSARY_REMINDER') ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
