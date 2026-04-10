<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class AuthMiddleware implements MiddlewareInterface
{
  private \PDO $pdo;

  public function __construct()
  {
    $this->pdo = Database::getInstance()->getConnection();
  }

  public function handle(Request $request): void
  {
    $header = $request->getHeader('Authorization');
    if (!$header || !str_starts_with($header, 'Bearer ')) {
      Response::unauthorized();
    }

    $token = substr($header, 7);

    $stmt = $this->pdo->prepare('SELECT user_id FROM api_tokens WHERE token = :token');
    $stmt->execute([':token' => $token]);
    $result = $stmt->fetch();

    if (!$result) Response::unauthorized('Invalid token');

    $request->setUserId((int) $result['user_id']);
  }
}
