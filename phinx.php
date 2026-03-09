<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/tests/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'templates' => [
        'file' => '%%PHINX_CONFIG_DIR%%/stubs/migration_template.txt'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => $_ENV['ENVIRONEMENT'] == "production" ? "development" : "testing",
        'development' => [
            'adapter' => $_ENV['DB_CONNECTION'] ?? 'mysql',
            'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'name'    => $_ENV['DB_DATABASE'] ?? 'mitsuki_db',
            'user'    => $_ENV['DB_USERNAME'] ?? 'root',
            'pass'    => $_ENV['DB_PASSWORD'] ?? '',
            'port'    => $_ENV['DB_PORT'] ?? '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'sqlite',
            'name'    => './tests/database/mitsuki_test',
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];
