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
    ) {
        //VALIDACIONES AQUI
    }
}

class UpdateUserRequest
{
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $password = null
    ) {
        //VALIDACIONES AQUI
    }
}
