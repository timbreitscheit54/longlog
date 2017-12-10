<?php

use \frontend\modules\dashboard\models\ProfileForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model ProfileForm */

$this->title = Yii::t('app', 'DASHBOARD_PROFILE_UPDATE_TITLE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'DASHBOARD_TITLE'), 'url' => ['/dashboard/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="update profile">
    <div class="row">
        <div class="col-md-7">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <p><?= Yii::t('app', 'DASHBOARD_PROFILE_UPDATE_DESCRIPTION') ?></p>

            <?=
            \common\widgets\Alert::widget([
                'alertTypes' => [
                    'passwordChanged' => 'alert-warning',
                    'emailChangeSend' => 'alert-warning',
                ],
            ]); ?>

            <div class="row">
                <?= $form->field($model, 'name', ['options' => ['class' => 'col-md-6']])->textInput(['maxlength' => true]); ?>
            </div>

            <div class="row">
                <?= $form->field($model, 'email', ['options' => ['class' => 'col-md-6']])->input('email', ['maxlength' => true]) ?>
            </div>

            <div class="row">
                <?= $form->field($model, 'password', ['options' => ['class' => 'col-md-6']])->passwordInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'password_c', ['options' => ['class' => 'col-md-6']])->passwordInput(['maxlength' => true]) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'SAVE_BTN'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                <?= Html::a(Yii::t('app', 'BACK_BTN'), ['default/index'], ['class' => 'btn btn-default']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
