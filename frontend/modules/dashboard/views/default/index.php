<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */

$this->title = Yii::t('app', 'DASHBOARD_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashboard-index">
    <?= Html::a(Yii::t('app', 'DASHBOARD_UPDATE_PROFILE_BTN'), ['profile/update'], ['class' => 'btn btn-primary']); ?>
</div>
