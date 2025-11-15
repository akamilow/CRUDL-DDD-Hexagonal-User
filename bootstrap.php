<?php

declare(strict_types=1);

$config = require __DIR__ . '/config/config.php';

require_once __DIR__ . '/autoload.php';

use App\Infrastructure\Persistence\SQLite\Connection;
use App\Infrastructure\Persistence\SQLite\SQLiteUserRepository;
use App\Application\User\UserService;
use App\Application\Auth\AuthService;

// Crear conexión SQLite
$pdo = Connection::fromPath($config['db_path']);

// Repositorios
$userRepository = new SQLiteUserRepository($pdo);

// Servicios de aplicación
$userService = new UserService($userRepository);
$authService = new AuthService($userRepository, $config['auth_secret'], (int)$config['token_ttl_seconds']);

return [
    'config' => $config,
    'pdo' => $pdo,
    'userRepository' => $userRepository,
    'userService' => $userService,
    'authService' => $authService,
];
