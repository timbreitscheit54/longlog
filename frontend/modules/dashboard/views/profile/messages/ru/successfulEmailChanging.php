<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $newEmail string */
?>
<h3 class="text-success">Вы успешно подтвердили ваш Новый E-mail адрес!</h3>

<p>Теперь все сообщения Вы будете получать на
    <?php if (!empty($newEmail)): ?>
        новый E-mail: <strong><?= Html::encode($newEmail); ?></strong>
    <?php else: ?>
        ваш новый E-mail
    <?php endif; ?>
</p>

<p>Не забудьте, что теперь для авторизации Вы должны использовать <strong>только</strong> новый E-mail
</p>
