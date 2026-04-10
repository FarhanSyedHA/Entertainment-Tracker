<?php
namespace App\Services;

use App\Core\Database;

class AuthService
{
  private \PDO $pdo;

  public function __construct()
  {
    $this->pdo = Database::getInstance()->getConnection();
  }

  public function register(string $email, string $username, string $password): array
  {
    $user = $this->findUserByEmail($email);
    if($user) {
      return ['error' => 'Email already exists'];
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $this->pdo->prepare('INSERT INTO users (email, username, password) VALUES (:email, :username, :password)');
    $stmt->execute([':email' => $email, ':username' => $username, ':password' => $hashedPassword]);
    
    $userId = (int) $this->pdo->lastInsertId();

    $token = $this->generateToken($userId);

    return ['token' => $token, 'userId' => $userId];
  }

  public function login(string $email, string $password): array
  {
    $user = $this->findUserByEmail($email);
    if(!$user || !password_verify($password, $user['password'])) {
      return ['error' => 'Invalid email or password'];
    }
    $token = $this->generateToken((int)$user['id']);
    return ['token' => $token, 'userId' => (int)$user['id']];
  }

  private function findUserByEmail(string $email): array|false
  {
    $stmt = $this->pdo->prepare('SELECT id, password FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
  }

  private function generateToken(int $userId): string
  {
    $token = bin2hex(random_bytes(32));
    $stmt = $this->pdo->prepare('INSERT INTO api_tokens (user_id, token) VALUES (:user_id, :token)');
    $stmt->execute([':user_id' => $userId, ':token' => $token]);
    return $token;
  }
}
