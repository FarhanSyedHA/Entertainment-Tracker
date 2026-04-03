<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Exceptions\HttpException;
use App\Core\Exceptions\UnauthorizedException;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Entities\User;

class AuthService
{
    public function register(RegisterDTO $dto): User
    {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $dto->email]);

        if ($stmt->fetch()) {
            throw new HttpException('Email already registered', 409);
        }

        $passwordHash = password_hash($dto->password, PASSWORD_BCRYPT);

        $stmt = $db->prepare('
            INSERT INTO users (email, password_hash, display_name, role)
            VALUES (:email, :password_hash, :display_name, :role)
        ');
        $stmt->execute([
            'email' => $dto->email,
            'password_hash' => $passwordHash,
            'display_name' => $dto->displayName,
            'role' => 'admin',
        ]);

        $userId = (int) $db->lastInsertId();

        return $this->getUserById($userId);
    }

    public function login(LoginDTO $dto): array
    {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $dto->email]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($dto->password, $row['password_hash'])) {
            throw new UnauthorizedException('Invalid email or password');
        }

        $user = User::fromRow($row);
        $token = bin2hex(random_bytes(32));

        $stmt = $db->prepare('
            INSERT INTO api_tokens (user_id, token, name, expires_at)
            VALUES (:user_id, :token, :name, DATE_ADD(NOW(), INTERVAL 30 DAY))
        ');
        $stmt->execute([
            'user_id' => $user->id,
            'token' => $token,
            'name' => 'web-login',
        ]);

        return ['token' => $token, 'user' => $user];
    }

    public function logout(int $userId, string $token): void
    {
        $db = Database::getInstance();

        $stmt = $db->prepare('DELETE FROM api_tokens WHERE user_id = :user_id AND token = :token');
        $stmt->execute(['user_id' => $userId, 'token' => $token]);
    }

    public function getUserById(int $id): ?User
    {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }
}
