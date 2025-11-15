<?php

declare(strict_types=1);

namespace App\Domain\User;

use DateTimeImmutable;
use InvalidArgumentException;

class User
{
    private string $id;
    private string $name;
    private string $email;
    private ?string $phone;
    private string $passwordHash;
    private DateTimeImmutable $createdAt;

    private function __construct(
        string $id,
        string $name,
        string $email,
        ?string $phone,
        string $passwordHash,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->passwordHash = $passwordHash;
        $this->createdAt = $createdAt;
    }

    
    //Obtiene el identificador único del usuario.
    public function getId(): string { return $this->id; }

    
    // Obtiene el nombre del usuario.
    public function getName(): string { return $this->name; }

    // Obtiene el correo electrónico del usuario.
    public function getEmail(): string { return $this->email; }

    // Obtiene el número de teléfono del usuario (puede ser nulo).
    public function getPhone(): ?string { return $this->phone; }

    // Obtiene la fecha de creación del usuario.
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }

    /**
     * Crea una nueva instancia de usuario con los datos proporcionados y genera un id único.
     * Hashea la contraseña y asigna la fecha de creación actual.
     */
    public static function create(string $name, string $email, ?string $phone, string $plainPassword): self
    {
        $id = self::uuidV4();
        $createdAt = new DateTimeImmutable('now');
        $passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
        return new self($id, $name, strtolower($email), $phone, $passwordHash, $createdAt);
    }

    
    //Actualiza el nombre del usuario. Lanza excepción si el nombre está vacío.
     
    public function setName(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('El nombre no puede estar vacío');
        }
        $this->name = $name;
    }

    //Actualiza el correo electrónico del usuario. Valida el formato y lo convierte a minúsculas.
    public function setEmail(string $email): void
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Correo inválido');
        }
        $this->email = $email;
    }

    
    // Actualiza el número de teléfono del usuario. Permite valor nulo.
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone !== null ? trim($phone) : null;
    }

    // Cambia la contraseña del usuario, almacenando el hash de la nueva contraseña.
    public function changePassword(string $plainPassword): void
    {
        $this->passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    
    // Verifica si la contraseña proporcionada coincide con el hash almacenado.
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }

    // Genera un identificador único (UUID v4).
    private static function uuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Crea una instancia de usuario a partir de un array de datos primitivos.
    public static function fromPrimitives(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['password_hash'],
            new DateTimeImmutable($data['created_at'])
        );
    }

    // Devuelve los datos del usuario en formato array de valores primitivos.
    public function toPrimitives(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password_hash' => $this->passwordHash,
            'created_at' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}
