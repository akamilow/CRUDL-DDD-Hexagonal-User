<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use InvalidArgumentException;


class UserService
{
    // Inyecta el repositorio de usuarios
    public function __construct(private UserRepository $repo) {}

    // Crea un nuevo usuario a partir de la solicitud
    public function create(CreateUserRequest $req): User
    {
        // Verifica si el correo ya está registrado
        if ($this->repo->findByEmail(strtolower($req->email))) {
            throw new InvalidArgumentException('El correo ya está registrado');
        }
        // Crea la entidad User y la guarda en el repositorio
        $user = User::create($req->name, $req->email, $req->phone, $req->password);
        $this->repo->save($user);
        return $user;
    }

    // Obtiene un usuario por su ID
    public function get(string $id): ?User
    {
        return $this->repo->findById($id);
    }

    /**
     * Lista todos los usuarios
     * @return User[]
     */
    public function list(): array
    {
        return $this->repo->findAll();
    }

    // Actualiza los datos de un usuario existente
    public function update(UpdateUserRequest $req): ?User
    {
        // Busca el usuario por ID
        $user = $this->repo->findById($req->id);
        if (!$user) return null; // Si no existe, retorna null

        // Actualiza los campos si se proporcionan
        if ($req->name !== null) $user->setName($req->name);
        if ($req->email !== null) $user->setEmail($req->email);
        if ($req->phone !== null) $user->setPhone($req->phone);
        if ($req->password !== null) $user->changePassword($req->password);

        // Guarda los cambios en el repositorio
        $this->repo->update($user);
        return $user;
    }

    // Elimina un usuario por su ID
    public function delete(string $id): void
    {
        $this->repo->delete($id);
    }
}
