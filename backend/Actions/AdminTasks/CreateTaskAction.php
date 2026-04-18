<?php
namespace App\Actions\AdminTasks;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

/**
 * POST /api/admin/tasks — create a new task.
 * Body: { title, description?, type: 'feature'|'bug'|'maintenance' }
 */
class CreateTaskAction
{
  private const ALLOWED_TYPES = ['feature', 'bug', 'maintenance'];

  public function handle(Request $request): void
  {
    $body = $request->getBody() ?? [];
    $title = trim((string) ($body['title'] ?? ''));
    $description = isset($body['description']) ? trim((string) $body['description']) : null;
    $type = $body['type'] ?? 'feature';

    if ($title === '') {
      Response::badRequest('Title is required');
    }
    if (!in_array($type, self::ALLOWED_TYPES, true)) {
      Response::badRequest('Invalid type');
    }
    if ($description === '') {
      $description = null;
    }

    $createdBy = $request->getUserId();

    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare(
      'INSERT INTO admin_tasks (title, description, type, created_by)
       VALUES (:title, :description, :type, :created_by)'
    );
    $stmt->execute([
      ':title' => $title,
      ':description' => $description,
      ':type' => $type,
      ':created_by' => $createdBy,
    ]);

    $id = (int) $pdo->lastInsertId();
    Response::json([
      'id' => $id,
      'title' => $title,
      'description' => $description,
      'type' => $type,
      'status' => 'open',
      'created_by' => $createdBy,
    ], 201);
  }
}
