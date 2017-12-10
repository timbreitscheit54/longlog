<?php

use frontend\widgets\JobOperationsChart;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Job */
?>

<hr />
<div class="project-job">
    <strong><?= Html::a($model->fTitle, ['/job/view', 'id' => $model->id]) ?></strong>

    <?= JobOperationsChart::widget(['jobModel' => $model]) ?>
</div>
