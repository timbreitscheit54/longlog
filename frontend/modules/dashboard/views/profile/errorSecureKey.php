<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $key \common\models\SecureKey */
?>
<h3 class="text-error"><?= Yii::t('app/error', 'ERROR') ?> ;-(</h3>

<?= Html::errorSummary($key); ?>
