<?php

$root = realpath(__DIR__ . '/../../');

return [
    'sourcePath' => $root,
    'languages' => ['en', 'ru'],
    'translator' => 'Yii::t',
    'sort' => true,
    'removeUnused' => false,
    'only' => ['*.php'],
    'except' => [
        // files
        '.git',
        '.gitignore',
        '.gitkeep',
        // system directories
        '/.sass-cache',
        '/.vagrant',
        '/node_modules',
        '/vagrant',
        // site directories
        '/vendor',
        '/common/messages',
        '/api/runtime',
        '/api/web',
        '/frontend/runtime',
        '/frontend/resourses',
        '/frontend/web',
        '/backend/web',
        '/backend/runtime',
        '/console/runtime',
    ],
    'ignoreCategories' => [
        'yii',
        'custom',
        'app/custom',
    ],
    'format' => 'php',
    'messagePath' => $root . '/common/messages',
    'overwrite' => true,
];
