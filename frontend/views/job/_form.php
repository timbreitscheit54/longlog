<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Job */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs(<<<JS
    $("#job-critduration-helper").on("keyup", function(e) {
        var minutes = parseFloat(this.value);
        var targetInput = $("#job-critduration");
        
        if (minutes > 0) {
            targetInput.val((minutes * 60).toFixed(3));
        } else {
            targetInput.val("");
        }
    });

    $("#job-critduration").on("keyup", function(e) {
        var seconds = parseFloat(this.value);
        var targetInput = $("#job-critduration-helper");
        
        if (seconds > 0) {
            targetInput.val((seconds / 60).toFixed());
        } else {
            targetInput.val("");
        }
    });
JS
);
?>

<div class="job-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'projectId')->textInput(['disabled' => true, 'value' => $model->project->name]) ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <?= $form->field($model, 'critDuration', ['options' => ['class' => 'col-md-10']])->textInput() ?>

        <div class="col-md-2">
            <label class="control-label" for="job-critduration-helper">
                <?= Html::encode(Yii::t('app', 'JOB_CRIT_DURATION_MINUTES')) ?>
            </label>
            <input id="job-critduration-helper" class="form-control" value="<?= ceil($model->critDuration / 60) ?>" type="text">
            <div class="help-block"></div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ?
            Yii::t('app', 'BTN_CREATE') : Yii::t('app', 'BTN_SAVE'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
