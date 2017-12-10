<?php

/**
 * Fake Yii-class for make IDE autocomplete
 *
 * Note for PhpStorm: mark the file "/vendor/yiisoft/yii2/Yii.php" as plain text (right-click "Mark as Plain Text")
 *
 * @inheritdoc
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var Application|\yii\console\Application|\yii\web\Application
     */
    public static $app;
}

/**
 * Fake-class for make IDE autocomplete
 *
 * @property \yii\rbac\PhpManager $authManager
 * @property UserIdentityAutocomplete $user
 */
class Application
{
}

/**
 * User identity autocomplete
 *
 * @property \common\models\User|null $identity The identity object associated with the currently logged-in
 */
class UserIdentityAutocomplete extends \yii\web\User
{

}
