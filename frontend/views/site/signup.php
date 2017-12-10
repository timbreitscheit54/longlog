<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('app', 'SIGNUP_TITLE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app', 'SIGNUP_DESCRIPTION_{loginUrl}', ['loginUrl' => Url::toRoute(Yii::$app->user->loginUrl)]) ?></p>

    <div class="row">
        <div class="col-md-7">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <div class="row">
                <?= $form->field($model, 'name', ['options' => ['class' => 'col-md-6']])->textInput() ?>
            </div>

            <div class="row">
                <?php
                $emailInputOptions = [];
                $emailHint = '';
                if ($model->invite) {
                    $emailInputOptions['disabled'] = true;
                    $emailHint = Yii::t('app', 'SIGNUP_EMAIL_HINT_INVITATION');
                }
                ?>
                <?= $form->field($model, 'email', ['options' => ['class' => 'col-md-6']])
                    ->input('email', $emailInputOptions)
                    ->hint($emailHint) ?>
            </div>

            <div class="row">
                <?= $form->field($model, 'password', ['options' => ['class' => 'col-md-6']])->passwordInput() ?>
                <?= $form->field($model, 'password_confirm', ['options' => ['class' => 'col-md-6']])->passwordInput() ?>
            </div>

            <?= $form->field($model, 'captcha', ['enableAjaxValidation' => false])->label(false)
                ->widget('demi\recaptcha\ReCaptcha', ['siteKey' => Yii::$app->params['reCAPTCHA.siteKey']]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'SIGNUP_SUBMIT_BTN'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
