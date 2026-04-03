<?php

namespace App\DTOs\Auth;

use App\Entities\User;

class UserResponseDTO
{
    public int $id;
    public string $email;
    public string $displayName;
    public string $role;

    public static function fromEntity(User $user): self
    {
        $dto = new self();
        $dto->id = $user->id;
        $dto->email = $user->email;
        $dto->displayName = $user->displayName;
        $dto->role = $user->role;
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'display_name' => $this->displayName,
            'role' => $this->role,
        ];
    }
}
