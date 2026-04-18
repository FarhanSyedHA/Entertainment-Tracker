<?php
namespace App\Actions\Auth;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

/**
 * GET /api/me — returns the current user's profile + admin flag.
 * Runs after AuthMiddleware, so $request->getUserId() is guaranteed non-null.
 * Frontend uses this right after login to know whether to render admin UI.
 */
class MeAction
{
  public function handle(Request $request): void
  {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare('SELECT id, username, email, is_admin FROM users WHERE id = :id');
    $stmt->execute([':id' => $request->getUserId()]);
    $row = $stmt->fetch();

    if (!$row) {
      Response::notFound('User not found');
    }

    Response::json([
      'id' => (int) $row['id'],
      'username' => $row['username'],
      'email' => $row['email'],
      'is_admin' => (bool) $row['is_admin'],
    ]);
  }
}
