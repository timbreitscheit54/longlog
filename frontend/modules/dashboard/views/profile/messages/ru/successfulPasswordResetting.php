<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
?>
<h3 class="text-success">Вы успешно изменили пароль от Вашего аккаунта!</h3>

<p>Теперь вы можете <?= Html::a('авторизоваться', Yii::$app->user->loginUrl); ?> используя новый пароль</p>
