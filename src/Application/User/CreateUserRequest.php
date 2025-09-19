<?php

declare(strict_types=1);

namespace App\Application\User;

class CreateUserRequest
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $password
    ) {}
}