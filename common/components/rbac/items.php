<?php
return [
    'guest' => [
        'type' => 1,
        'description' => 'Guest',
    ],
    'viewer' => [
        'type' => 1,
        'description' => 'Viewer',
        'children' => [
            'guest',
        ],
    ],
    'manager' => [
        'type' => 1,
        'description' => 'Manager',
        'children' => [
            'viewer',
        ],
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Admin',
        'children' => [
            'manager',
        ],
    ],
];
