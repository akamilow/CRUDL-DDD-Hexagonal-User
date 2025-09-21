<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$container = require __DIR__ . '/../bootstrap.php';

use App\Application\User\UserService;
use App\Application\User\CreateUserRequest;
use App\Application\User\UpdateUserRequest;
use App\Application\Auth\AuthService;

/** @var UserService $userService */
$userService = $container['userService'];
/** @var AuthService $authService */
$authService = $container['authService'];


// Helpers

/**
 * Obtiene y decodifica el cuerpo JSON de la petición HTTP
 * @return array Datos recibidos en el body
 */
function body(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
}

/**
 * Envía una respuesta HTTP con el código y los datos en formato JSON
 * @param int $code Código de estado HTTP
 * @param mixed $data Datos a enviar en la respuesta
 */
function respond(int $code, $data): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

/**
 * Obtiene la ruta solicitada, eliminando la barra final
 * @return string Ruta de la petición
 */
function path(): string {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return rtrim($uri, '/') ?: '/';
}

/**
 * Obtiene el método HTTP de la petición (GET, POST, etc.)
 * @return string Método HTTP
 */
function method(): string { return $_SERVER['REQUEST_METHOD'] ?? 'GET'; }

/**
 * (Comentada) Obtiene el ID de usuario autenticado a partir del header Authorization
 * @param AuthService $auth Servicio de autenticación
 * @return string|null ID de usuario si el token es válido, null si no
 */
// function authUserId(AuthService $auth): ?string {
//     $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
//     if (preg_match('/Bearer\s+(.*)/i', $hdr, $m)) {
//         return $auth->verify(trim($m[1]));
//     }
//     return null;
// }

try {
    $p = path();
    $m = method();

    // Salud
    if ($p === '/' && $m === 'GET') {
        respond(200, ['ok' => true, 'service' => 'CRUDL-DDD-Hexagonal-Users']);
        return;
    }

    // Login
    if ($p === '/login' && $m === 'POST') {
        $b = body();
        $token = $authService->login($b['email'] ?? '', $b['password'] ?? '');
        if (!$token) {
            respond(401, ['error' => 'Credenciales inválidas']);
            return;
        }
        respond(200, ['token' => $token]);
        return;
    }

    // Usuarios: CRUDL
    if ($p === '/users' && $m === 'POST') {
        $b = body();
        $req = new CreateUserRequest(
            $b['name'] ?? '',
            $b['email'] ?? '',
            $b['phone'] ?? null,
            $b['password'] ?? ''
        );
        $user = $userService->create($req);
        respond(201, $user->toPrimitives());
        return;
    }

    if ($p === '/users' && $m === 'GET') {
        $list = array_map(fn($u) => $u->toPrimitives(), $userService->list());
        respond(200, $list);
        return;
    }

    if (preg_match('#^/users/([\w-]+)$#', $p, $mId)) {
        $id = $mId[1];
        if ($m === 'GET') {
            $u = $userService->get($id);
            if (!$u) { respond(404, ['error' => 'No encontrado']); return; }
            respond(200, $u->toPrimitives());
            return;
        } elseif ($m === 'PUT' || $m === 'PATCH') {
            $b = body();
            $req = new UpdateUserRequest(
                $id,
                $b['name'] ?? null,
                $b['email'] ?? null,
                $b['phone'] ?? null,
                $b['password'] ?? null
            );
            $u = $userService->update($req);
            if (!$u) { respond(404, ['error' => 'No encontrado']); return; }
            respond(200, $u->toPrimitives());
            return;
        } elseif ($m === 'DELETE') {
            $userService->delete($id);
            respond(200, ['deleted' => true, 'id' => $id]);
            return;
        }
    }

    respond(404, ['error' => 'Ruta no encontrada']);
} catch (Throwable $e) {
    respond(400, ['error' => $e->getMessage()]);
}
