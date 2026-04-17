<?php
namespace App\Actions\WatchHistory;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class GetInProgressAction
{
  public function handle(Request $request): void
  {
    $userId = $request->getUserId();
    if (!$userId) Response::unauthorized();

    $pdo = Database::getInstance()->getConnection();

    $stmt = $pdo->prepare("
      SELECT c.id, c.type, c.title, c.thumbnail_url, c.imdb_rating, c.release_year, c.tmdb_id, c.jikan_id
      FROM content c
      INNER JOIN progress p ON p.content_id = c.id
      WHERE p.user_id = :user AND p.status = 'in_progress'
      GROUP BY c.id
      ORDER BY MAX(p.updated_at) DESC
    ");
    $stmt->execute([':user' => $userId]);
    $rows = $stmt->fetchAll();

    $shows = array_map(function($r) {
      $type = $r['type'];
      $frontendType = $type === 'tv' ? 'tvshows' : $type;
      $externalId = $r['tmdb_id'] ?: $r['jikan_id'];
      return [
        'id' => (int) $externalId,
        'title' => $r['title'],
        'poster' => $r['thumbnail_url'],
        'rating' => (float) ($r['imdb_rating'] ?? 0),
        'year' => (string) ($r['release_year'] ?? ''),
        'type' => $frontendType,
      ];
    }, $rows);

    Response::json(['shows' => $shows]);
  }
}
