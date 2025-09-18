<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    public function save(User $user): void;
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
    /** @return User[] */
    public function findAll(): array;
    public function update(User $user): void;
    public function delete(string $id): void;
}