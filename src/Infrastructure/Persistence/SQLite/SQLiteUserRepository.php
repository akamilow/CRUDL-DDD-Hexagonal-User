<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SQLite;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use PDO;


// Repositorio de usuarios usando SQLite como almacenamiento
class SQLiteUserRepository implements UserRepository
{
    // Recibe la conexión PDO por inyección de dependencias
    public function __construct(private PDO $pdo) {}

    /**
     * Guarda un nuevo usuario en la base de datos
     * Convierte el usuario a un array de datos primitivos y ejecuta el INSERT
     */
    public function save(User $user): void
    {
        $p = $user->toPrimitives(); // Convierte la entidad a array
        $stmt = $this->pdo->prepare('INSERT INTO users (id, name, email, phone, password_hash, created_at) VALUES (:id, :name, :email, :phone, :password_hash, :created_at)');
        $stmt->execute([
            ':id' => $p['id'],
            ':name' => $p['name'],
            ':email' => $p['email'],
            ':phone' => $p['phone'],
            ':password_hash' => $p['password_hash'],
            ':created_at' => $p['created_at'],
        ]);
    }

    /**
     * Busca un usuario por su ID
     * Devuelve la entidad User o null si no existe
     */
    public function findById(string $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? User::fromPrimitives($row) : null;
    }

    /**
     * Busca un usuario por su email (case-insensitive)
     */
    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => strtolower($email)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? User::fromPrimitives($row) : null;
    }

    /**
     * Devuelve todos los usuarios ordenados por fecha de creación descendente
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM users ORDER BY created_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Convierte cada fila en una entidad User
        return array_map(fn($r) => User::fromPrimitives($r), $rows);
    }

    /**
     * Actualiza los datos de un usuario existente
     */
    public function update(User $user): void
    {
        $p = $user->toPrimitives();
        $stmt = $this->pdo->prepare('UPDATE users SET name = :name, email = :email, phone = :phone, password_hash = :password_hash WHERE id = :id');
        $stmt->execute([
            ':id' => $p['id'],
            ':name' => $p['name'],
            ':email' => $p['email'],
            ':phone' => $p['phone'],
            ':password_hash' => $p['password_hash'],
        ]);
    }

    /**
     * Elimina un usuario por su ID
     */
    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}



