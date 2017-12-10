<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $project common\models\Project */
/* @var $invite common\models\ProjectInvite */

$link = $invite->url;
?>
<div>
    <p>Здравствуйте</p>
    <br/>
    <p>
        Вам было отправлено приглашение присоединиться к проекту
        <?= Html::a($project->fName, Url::to(['/project/view', 'id' => $project->id], true)) ?>
        <?php if ($invite->inviter): ?>
            от пользователя <?= $invite->inviter->fName ?> (<?= $invite->inviter->email ?>)
        <?php endif ?>
        <br/>
        Для принятия приглашения и создания аккаунта, пройдите по этой ссылке:
    </p>
    <p>
        <?= Html::a(Html::encode($link), $link) ?>
    </p>
</div>
