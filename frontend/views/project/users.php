<?php

use common\helpers\enum\ProjectUserRole;
use common\helpers\UserHelper;
use common\models\Project;
use common\models\ProjectInvite;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model Project */
/* @var $projectInvite \common\models\ProjectInvite */

$this->title = Yii::t('app', 'PROJECTS_USERS_TITLE_{name}', ['name' => $model->name]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'PROJECTS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'PROJECT_USERS');
$inviteSuccessMessage = Yii::$app->session->getFlash(ProjectInvite::FLASH_SUCCESS_KEY);
$inviteErrorMessage = Yii::$app->session->getFlash(ProjectInvite::FLASH_ERROR_KEY);
$currentUserId = UserHelper::getCurrentId();
$currentUserIsOwner = $currentUserId == $model->ownerId;
?>
<div class="project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th><?= Yii::t('app', 'NAME') ?></th>
            <th><?= Yii::t('app', 'EMAIL') ?></th>
            <th><?= Yii::t('app', 'PROJECT_USER_ROLE') ?></th>
            <th><?= Yii::t('app', 'CREATED_AT') ?></th>
            <th><?= Yii::t('app', 'UPDATED_AT') ?></th>
            <th><?= Yii::t('app', 'ACTIONS') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($model->getProjectUsers()->with('user')->all() as $projectUser): ?>
            <?php /* @var \common\models\ProjectUser $projectUser */ ?>
            <tr>
                <?= Html::hiddenInput('', $projectUser->userId, ['class' => 'projectUserId']) ?>
                <td><?= $projectUser->user->fName ?></td>
                <td><?= $projectUser->user->email ?></td>
                <td>
                    <?= ProjectUserRole::getLabel($projectUser->role) ?>
                </td>
                <td><?= Yii::$app->formatter->asDatetime($projectUser->createdAt) ?></td>
                <td><?= Yii::$app->formatter->asDatetime($projectUser->updatedAt) ?></td>
                <td>
                    <?php if ($projectUser->userId != $currentUserId && $projectUser->role == ProjectUserRole::VIEWER
                        && ($currentUserIsOwner || $model->isManageable())): ?>
                        <?= Html::a(Yii::t('app', 'PROJECT_USER_GRANT_ADMIN_ROLE_BTN'),
                            ['change-role', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-success',
                                'data' => [
                                    'confirm' => Yii::t('app', 'PROJECT_USER_GRANT_ADMIN_ROLE_CONFIRMATION'),
                                    'method' => 'post',
                                    'params' => [
                                        'userId' => $projectUser->userId,
                                        'newRole' => ProjectUserRole::ADMIN,
                                    ],
                                ],
                            ]) ?>
                    <?php endif ?>
                    <?php if ($projectUser->userId != $currentUserId && $currentUserIsOwner && $projectUser->role == ProjectUserRole::ADMIN): ?>
                        <?= Html::a(Yii::t('app', 'PROJECT_USER_REVOKE_ADMIN_ROLE_BTN'),
                            ['change-role', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', 'PROJECT_USER_REVOKE_ADMIN_ROLE_CONFIRMATION'),
                                    'method' => 'post',
                                    'params' => [
                                        'userId' => $projectUser->userId,
                                        'newRole' => ProjectUserRole::VIEWER,
                                    ],
                                ],
                            ]) ?>
                    <?php endif ?>
                    <?php if ($projectUser->userId != $currentUserId && $currentUserIsOwner): ?>
                        <?= Html::a(Yii::t('app', 'PROJECT_USER_SET_NEW_OWNER_BTN'),
                            ['change-owner', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-warning',
                                'data' => [
                                    'confirm' => Yii::t('app', 'PROJECT_CHANGE_OWNER_CONFIRMATION'),
                                    'method' => 'post',
                                    'params' => [
                                        'newOwnerId' => $projectUser->userId,
                                    ],
                                ],
                            ]) ?>
                    <?php endif ?>
                    <?php if ($projectUser->userId != $currentUserId && $model->ownerId != $projectUser->userId
                        && ($currentUserIsOwner || $projectUser->role == ProjectUserRole::VIEWER)): ?>
                        <?= Html::a(Yii::t('app', 'PROJECT_USER_REMOVE_BTN'),
                            ['remove-user', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', 'PROJECT_REMOVE_USER_CONFIRMATION'),
                                    'method' => 'post',
                                    'params' => [
                                        'userId' => $projectUser->userId,
                                    ],
                                ],
                            ]) ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <h2><?= Html::encode(Yii::t('app', 'PROJECT_USERS_SEND_INVITE')) ?></h2>
    <?php if ($inviteSuccessMessage || $inviteErrorMessage): ?>
        <div class="alert alert-<?= $inviteErrorMessage ? 'danger' : 'success' ?>" role="alert">
            <a href="<?= Url::current() ?>" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></a>
            <?= Html::encode($inviteErrorMessage ? $inviteErrorMessage : $inviteSuccessMessage) ?>
        </div>
    <?php else: ?>
        <?= $this->render('_inviteForm', ['model' => $projectInvite]) ?>
    <?php endif ?>

    <h2><?= Html::encode(Yii::t('app', 'PROJECT_USERS_INVITES_TITLE')) ?></h2>

    <table class="table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th><?= Yii::t('app', 'INVITE_EMAIL') ?></th>
            <th><?= Yii::t('app', 'INVITE_ROLE') ?></th>
            <th><?= Yii::t('app', 'INVITE_SENT_AT') ?></th>
            <th><?= Yii::t('app', 'INVITE_ACCEPTED_AT') ?></th>
            <th><?= Yii::t('app', 'ACTIONS') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$model->invites): ?>
            <tr>
                <td colspan="5"><?= Html::encode(Yii::t('app', 'PROJECT_INVITES_EMPTY_LIST')) ?></td>
            </tr>
        <?php endif ?>
        <?php foreach ($model->invites as $invite): ?>
            <tr>
                <td><?= $invite->email ?></td>
                <td><?= ProjectUserRole::getLabel($invite->role) ?></td>
                <td><?= Yii::$app->formatter->asDatetime($invite->sentAt) ?></td>
                <td><?= Yii::$app->formatter->asDatetime($invite->acceptedAt) ?></td>
                <td>
                    <?php
                    if (!$invite->acceptedAt) {
                        $canResendAfter = $invite->canResendAfter();
                        if ($canResendAfter === 0) {
                            echo Html::a(Yii::t('app', 'RESEND_INVITE'), ['resend-invite', 'id' => $invite->id],
                                ['class' => 'btn btn-xs btn-success resendInvite']);
                        } else {
                            echo Html::button(Yii::t('app', 'RESEND_INVITE_AFTER_{minutes}',
                                ['minutes' => Yii::$app->formatter->asDuration($canResendAfter)]),
                                ['class' => 'btn btn-xs btn-warning', 'disabled' => true]);
                        }
                    } else {
                        echo '<span class="text-success">' . Yii::t('app', 'PROJECT_INVITE_ACCEPTED') . '</span>';
                    } ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
