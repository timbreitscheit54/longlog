<?php

namespace console\controllers;

use Yii;
use common\helpers\enum\UserRole;
use yii\console\Controller;

class RbacController extends Controller
{
    /**
     * Init RBAC rules
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // Guest
        $guest = $auth->createRole('guest');
        $guest->description = 'Guest';
        $auth->add($guest);

        // Viewer
        $viewer = $auth->createRole(UserRole::VIEWER);
        $viewer->description = 'Viewer';
        $auth->add($viewer);
        $auth->addChild($viewer, $guest);

        // Manager
        $manager = $auth->createRole(UserRole::MANAGER);
        $manager->description = 'Manager';
        $auth->add($manager);
        $auth->addChild($manager, $viewer);

        // Admin
        $admin = $auth->createRole(UserRole::ADMIN);
        $admin->description = 'Admin';
        $auth->add($admin);
        $auth->addChild($admin, $manager);
    }
}
