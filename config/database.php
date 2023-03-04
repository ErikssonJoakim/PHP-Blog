<?php declare(strict_types = 1);

return [
    'driver' => env(key: 'DB_DRIVER', default: 'mysql'),
    'host' => env(key: 'DB_HOST', default: 'db'),
    'name' => env(key: 'DB_DATABASE', default: 'blog'),
    'username' => env(key: 'DB_USERNAME', default: 'root'),
    'password' => env(key: 'DB_PASSWORD'),
];
