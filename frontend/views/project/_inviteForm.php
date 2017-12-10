<?php

use common\helpers\enum\ProjectUserRole;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectInvite */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-invite-form">
    <div class="row">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'email', ['options' => ['class' => 'col-md-6']])->label(false)
            ->textInput([
                'maxlength' => true,
                'type' => 'email',
                'placeholder' => Yii::t('app', 'PROJECT_INVITE_ENTER_EMAIL'),
            ]) ?>

        <?= $form->field($model, 'role', ['options' => ['class' => 'col-md-3']])->label(false)
            ->dropDownList(ProjectUserRole::getList(), ['prompt' => Yii::t('app', 'PROJECT_INVITE_SELECT_ROLE')]) ?>

        <div class="col-md-3">
            <?= Html::submitButton(Yii::t('app', 'SUBMIT_INVITE_BTN'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
