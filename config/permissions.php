<?php

return [
    'cache_duration' => 60 * 60 * 24 * 30,
    'cache_driver' => 'array',

    'database_connection_name' => 'testbench',
    'database_mode' => 'host',

    'table_prefix' => 'permissions_',
    'tables' => [
        'users_abilities' => 'users_abilities',
        'users_roles' => 'users_roles',
    ],

    'role_abilities' => [
        'role1' => [
            'ability-1',
            'ability-2',
        ],
        'role2' => [
            'ability-2',
            'ability-3',
        ],
    ],
];