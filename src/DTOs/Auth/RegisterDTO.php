<?php

namespace App\DTOs\Auth;

use App\Core\Request;

class RegisterDTO
{
    public string $email;
    public string $password;
    public string $displayName;

    public static function fromRequest(Request $request): self
    {
        $body = $request->getBody();
        $dto = new self();
        $dto->email = trim($body['email'] ?? '');
        $dto->password = $body['password'] ?? '';
        $dto->displayName = trim($body['display_name'] ?? '');
        return $dto;
    }

    public function validate(): array
    {
        $errors = [];

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        if (strlen($this->password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if (empty($this->displayName)) {
            $errors[] = 'Display name is required';
        }

        return $errors;
    }
}
