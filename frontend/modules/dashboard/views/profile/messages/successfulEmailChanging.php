<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $newEmail string */
?>
<h3 class="text-success">You have successfully verified your new email-address!</h3>

<p>Now all the messages will be sent to the
    <?php if (!empty($newEmail)): ?>
        this email: <strong><?= Html::encode($newEmail); ?></strong>
    <?php else: ?>
        your new e-mail address
    <?php endif; ?>
</p>

<p>As well as the authorization will be made <strong>only</strong> by new e-mail address</p>
