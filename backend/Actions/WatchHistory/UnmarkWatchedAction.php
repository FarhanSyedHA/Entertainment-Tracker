<?php
namespace App\Actions\WatchHistory;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class UnmarkWatchedAction
{
  public function handle(Request $request): void
  {
    $userId = $request->getUserId();
    if (!$userId) Response::unauthorized();

    $body = $request->getBody();
    $source = $body['source'] ?? null;
    $externalId = isset($body['externalId']) ? (int) $body['externalId'] : 0;
    $type = $body['type'] ?? null;

    if (!$source || !$externalId || !in_array($type, ['movie', 'tv', 'anime'], true)) {
      Response::badRequest('source, externalId, and valid type are required');
    }

    $pdo = Database::getInstance()->getConnection();
    $col = $source === 'tmdb' ? 'tmdb_id' : 'jikan_id';

    $stmt = $pdo->prepare("SELECT id FROM content WHERE type = :type AND $col = :ext LIMIT 1");
    $stmt->execute([':type' => $type, ':ext' => $externalId]);
    $row = $stmt->fetch();
    if (!$row) { Response::json(['success' => true]); return; }

    $stmt = $pdo->prepare("DELETE FROM progress WHERE user_id = :user AND content_id = :content");
    $stmt->execute([':user' => $userId, ':content' => (int) $row['id']]);

    Response::json(['success' => true]);
  }
}
