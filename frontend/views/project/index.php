<?php

use common\helpers\enum\ProjectUserRole;
use common\helpers\UserHelper;
use common\models\Project;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'PROJECTS_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (UserHelper::canManage()): ?>
        <p><?= Html::a(Yii::t('app', 'CREATE_PROJECT_BTN'), ['create'], ['class' => 'btn btn-success']) ?></p>
    <?php endif ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name' => [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (Project $model) {
                    return Html::a($model->fName, ['view', 'id' => $model->id]);
                },
            ],
            'token' => [
                'attribute' => 'token',
                'enableSorting' => false,
                'format' => 'raw',
                'value' => function (Project $model) {
                    if (!$model->isManageable()) {
                        return '<i class="hint">hidden</i>';
                    }

                    return $model->token;
                },
            ],
            'myRole' => [
                'attribute' => 'myRole',
                'filter' => ProjectUserRole::getList(),
                'value' => function (Project $model) {
                    return $model->currentProjectUser ? ProjectUserRole::getLabel($model->currentProjectUser->role) : null;
                },
            ],
            [
                'class' => '\common\components\grid\BigActionColumn',
                'visibleButtons' => [
                    'update' => function (Project $model, $key, $index) {
                        return $model->isManageable();
                    },
                    'delete' => function (Project $model, $key, $index) {
                        return $model->isManageable();
                    },
                ],
            ],
        ],
    ]); ?>
</div>
