<?php

namespace App\Entities;

class User
{
    public int $id;
    public string $email;
    public string $passwordHash;
    public string $displayName;
    public string $role;
    public string $createdAt;
    public string $updatedAt;

    public static function fromRow(array $row): self
    {
        $user = new self();
        $user->id = (int) $row['id'];
        $user->email = $row['email'];
        $user->passwordHash = $row['password_hash'];
        $user->displayName = $row['display_name'];
        $user->role = $row['role'];
        $user->createdAt = $row['created_at'];
        $user->updatedAt = $row['updated_at'];
        return $user;
    }
}
