<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $email string */
?>
<h3 class="text-success">You have successfully submitted a request for password recovery</h3>

<p class="text-warning">
    Now check your mailbox <?php if (!empty($email)) {
        echo '<strong>' . Html::encode($email) . '</strong>';
    } ?>, You should receive an email, which will link to the form entered a new password.
</p>
