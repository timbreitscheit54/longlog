<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
?>
<h3 class="text-success">You have successfully changed the password of your account!</h3>

<p>Now you can <?= Html::a('log in', Yii::$app->user->loginUrl); ?>, using the new password</p>
