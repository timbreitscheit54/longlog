<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \frontend\modules\dashboard\models\ResetPasswordForm */

$this->title = Yii::t('app', 'PASSWORD_CHANGING_TITLE');
?>
<div class="request-reset-password">
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'password_c')->passwordInput() ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'SAVE_BTN'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
