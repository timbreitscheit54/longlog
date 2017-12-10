<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $project common\models\Project */
/* @var $invite common\models\ProjectInvite */

$link = $invite->url;
?>
<div>
    <p>Hello</p>
    <br/>
    <p>
        You have been invited to join the project
        <?= Html::a($project->fName, Url::to(['/project/view', 'id' => $project->id], true)) ?>
        <?php if ($invite->inviter): ?>
            from user <?= $invite->inviter->fName ?> (<?= $invite->inviter->email ?>)
        <?php endif ?>
        <br/>
        To accept invitation and create an account, follow this link:
    </p>
    <p>
        <?= Html::a(Html::encode($link), $link) ?>
    </p>
</div>
