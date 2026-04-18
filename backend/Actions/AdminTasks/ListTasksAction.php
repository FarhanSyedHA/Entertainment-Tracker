<?php
namespace App\Actions\AdminTasks;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

/**
 * GET /api/admin/tasks — return every task, split into open + done arrays.
 * The frontend renders the two lists separately so we save it a filter pass.
 */
class ListTasksAction
{
  public function handle(Request $request): void
  {
    $pdo = Database::getInstance()->getConnection();
    $rows = $pdo->query(
      'SELECT t.id, t.title, t.description, t.type, t.status,
              t.created_at, t.completed_at, t.created_by,
              u.username AS created_by_username
       FROM admin_tasks t
       LEFT JOIN users u ON u.id = t.created_by
       ORDER BY t.status ASC, t.created_at DESC'
    )->fetchAll();

    $open = [];
    $done = [];
    foreach ($rows as $r) {
      $task = [
        'id' => (int) $r['id'],
        'title' => $r['title'],
        'description' => $r['description'],
        'type' => $r['type'],
        'status' => $r['status'],
        'created_at' => $r['created_at'],
        'completed_at' => $r['completed_at'],
        'created_by' => $r['created_by'] !== null ? (int) $r['created_by'] : null,
        'created_by_username' => $r['created_by_username'],
      ];
      if ($r['status'] === 'done') {
        $done[] = $task;
      } else {
        $open[] = $task;
      }
    }

    Response::json(['open' => $open, 'done' => $done]);
  }
}
