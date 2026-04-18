<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

/**
 * Rejects any request whose authenticated user is not flagged is_admin=1.
 * Must run AFTER AuthMiddleware (which sets userId on the request). Route
 * order in public/index.php: [CorsMiddleware, AuthMiddleware, AdminMiddleware].
 */
class AdminMiddleware implements MiddlewareInterface
{
  public function handle(Request $request): void
  {
    $userId = $request->getUserId();
    if ($userId === null) {
      Response::unauthorized();
    }

    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare('SELECT is_admin FROM users WHERE id = :id');
    $stmt->execute([':id' => $userId]);
    $row = $stmt->fetch();

    if (!$row || !(int) $row['is_admin']) {
      Response::json(['error' => 'Forbidden'], 403);
    }
  }
}
