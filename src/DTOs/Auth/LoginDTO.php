<?php

namespace App\DTOs\Auth;

use App\Core\Request;

class LoginDTO
{
    public string $email;
    public string $password;

    public static function fromRequest(Request $request): self
    {
        $body = $request->getBody();
        $dto = new self();
        $dto->email = trim($body['email'] ?? '');
        $dto->password = $body['password'] ?? '';
        return $dto;
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->email)) {
            $errors[] = 'Email is required';
        }

        if (empty($this->password)) {
            $errors[] = 'Password is required';
        }

        return $errors;
    }
}
