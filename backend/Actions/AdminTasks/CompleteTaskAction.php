<?php
namespace App\Actions\AdminTasks;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

/**
 * POST /api/admin/tasks/complete — mark a task done.
 * Body: { id: number }
 * Idempotent: completing an already-done task is a no-op success.
 */
class CompleteTaskAction
{
  public function handle(Request $request): void
  {
    $body = $request->getBody() ?? [];
    $id = (int) ($body['id'] ?? 0);
    if ($id <= 0) {
      Response::badRequest('Missing task id');
    }

    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare(
      "UPDATE admin_tasks
       SET status = 'done', completed_at = CURRENT_TIMESTAMP
       WHERE id = :id AND status = 'open'"
    );
    $stmt->execute([':id' => $id]);

    Response::success();
  }
}
