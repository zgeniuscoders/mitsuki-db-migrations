<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$adapter = $_ENV['DB_CONNECTION'] == "sqlite" ? [
    'adapter' => 'sqlite',
    'name'    => '%%PHINX_CONFIG_DIR%%/database/db',
    'charset' => 'utf8',
] : [
    'adapter' => $_ENV['DB_CONNECTION'] ?? 'mysql',
    'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'name'    => $_ENV['DB_DATABASE'] ?? 'mitsuki_db',
    'user'    => $_ENV['DB_USERNAME'] ?? 'root',
    'pass'    => $_ENV['DB_PASSWORD'] ?? '',
    'port'    => $_ENV['DB_PORT'] ?? '3306',
    'charset' => 'utf8',
];

$migrationsPath = $_ENV['ENVIRONEMENT'] == "production" ?
    '%%PHINX_CONFIG_DIR%%/database/migrations'
    : '%%PHINX_CONFIG_DIR%%/tests/database/migrations';

$seedsPath = $_ENV['ENVIRONEMENT'] == "production" ?
    '%%PHINX_CONFIG_DIR%%/database/seeds'
    : '%%PHINX_CONFIG_DIR%%/tests/database/seeds';

return [
    'paths' => [
        'migrations' => $migrationsPath,
        'seeds' => $seedsPath
    ],
    'templates' => [
        'file' => '%%PHINX_CONFIG_DIR%%/stubs/migration_template.txt'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => $_ENV['ENVIRONEMENT'] == "production" ? "development" : "testing",
        'development' => $adapter,
        'testing' => [
            'adapter' => 'sqlite',
            'name'    => './tests/database/mitsuki_test',
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];
