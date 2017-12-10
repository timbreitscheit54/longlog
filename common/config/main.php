<?php

return [
    'language' => 'en-US', // en-US | ru-RU
    'sourceLanguage' => 'en',
    'name' => 'LongLog',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'authManager' => [
            'class' => '\common\components\rbac\PhpManager',
            'itemFile' => '@common/components/rbac/items.php',
            'assignmentFile' => '@common/components/rbac/assignments.php',
            'ruleFile' => '@common/components/rbac/rules.php',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'directoryLevel' => 6,
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'app/error.php',
                    ],
                ],
            ],
        ],
    ],
];
