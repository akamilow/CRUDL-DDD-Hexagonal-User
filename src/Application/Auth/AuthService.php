<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\User\UserRepository;


class AuthService
{
    // Constructor: recibe el repositorio de usuarios, el secreto para firmar tokens y el tiempo de vida del token
    public function __construct(
        private UserRepository $repo,
        private string $secret,
        private int $ttlSeconds
    ) {}

    // login: verifica credenciales y retorna un token si son válidas, null si no
    public function login(string $email, string $password): ?string
    {
        $user = $this->repo->findByEmail(strtolower($email));
        if (!$user) return null;
        if (!$user->verifyPassword($password)) return null;

        $payload = [
            'sub' => $user->getId(),
            'exp' => time() + $this->ttlSeconds,
        ];
        return $this->encode($payload);
        
    }

    // verify: valida el token y retorna el id de usuario si es válido, null si no
    public function verify(string $token): ?string
    {
        $data = $this->decode($token);
        if (!$data) return null;
        if (($data['exp'] ?? 0) < time()) return null;
        return $data['sub'] ?? null;
    }

    // encode: genera un token tipo JWT (HMAC) a partir de un payload
    private function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $segments = [
            $this->b64(json_encode($header)),
            $this->b64(json_encode($payload)),
        ];
        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $this->secret, true);
        $segments[] = $this->b64($signature);
        return implode('.', $segments);
    }

    // decode: valida y decodifica un token, retorna el payload si es válido, null si no
    private function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$h, $p, $s] = $parts;
        $signingInput = $h . '.' . $p;
        $expected = $this->b64(hash_hmac('sha256', $signingInput, $this->secret, true));
        if (!hash_equals($expected, $s)) return null;
        $payload = json_decode($this->ub64($p), true);
        if (!is_array($payload)) return null;
        return $payload;
    }

    // b64: codifica en base64 url-safe (sin relleno)
    private function b64(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // ub64: decodifica base64 url-safe
    private function ub64(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
