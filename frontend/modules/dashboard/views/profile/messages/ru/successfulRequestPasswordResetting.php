<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $email string */
?>
<h3 class="text-success">Вы успешно отправили запрос на восстановление пароля</h3>

<p class="text-warning">
    Теперь проверьте Вашу почту<?php if (!empty($email)) {
        echo ' <strong>' . Html::encode($email) . '</strong>';
    } ?>, Вы должны получить E-mail с инструкциями по восстановлению.
</p>
