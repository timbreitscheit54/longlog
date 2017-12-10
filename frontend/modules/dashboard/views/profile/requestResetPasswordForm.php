<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \frontend\modules\dashboard\models\ResetPasswordRequestForm */

$this->title = Yii::t('app', 'RESET_PASSWORD_TITLE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'LOGIN'), 'url' => Yii::$app->user->loginUrl];
?>
<div class="request-reset-password">
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-reset-password-form']); ?>

            <?= $form->field($model, 'email')->input('email'); ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'CONTINUE_BTN'),
                    ['class' => 'btn btn-primary', 'name' => 'request-reset-password-button']) ?>
                <?= Html::a(Yii::t('app', 'BACK_BTN'), 'javascript:history.back()', ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
