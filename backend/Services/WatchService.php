<?php
namespace App\Services;

use App\Core\Database;

class WatchService
{
  public static function getWatchedMap(int $userId, array $items): array
  {
    if (empty($items)) return [];

    $pdo = Database::getInstance()->getConnection();

    $tmdbIds = [];
    $jikanIds = [];
    foreach ($items as $it) {
      if (($it['source'] ?? null) === 'tmdb') $tmdbIds[] = (int) $it['externalId'];
      if (($it['source'] ?? null) === 'jikan') $jikanIds[] = (int) $it['externalId'];
    }

    $watched = [];

    if (!empty($tmdbIds)) {
      $placeholders = implode(',', array_fill(0, count($tmdbIds), '?'));
      $sql = "SELECT c.tmdb_id, c.type FROM content c
              INNER JOIN progress p ON p.content_id = c.id
              WHERE c.tmdb_id IN ($placeholders) AND p.user_id = ? AND p.status = 'completed'
              GROUP BY c.tmdb_id, c.type";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([...$tmdbIds, $userId]);
      foreach ($stmt->fetchAll() as $row) {
        $watched["tmdb:{$row['tmdb_id']}:{$row['type']}"] = true;
      }
    }

    if (!empty($jikanIds)) {
      $placeholders = implode(',', array_fill(0, count($jikanIds), '?'));
      $sql = "SELECT c.jikan_id FROM content c
              INNER JOIN progress p ON p.content_id = c.id
              WHERE c.jikan_id IN ($placeholders) AND p.user_id = ? AND p.status = 'completed'
              GROUP BY c.jikan_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([...$jikanIds, $userId]);
      foreach ($stmt->fetchAll() as $row) {
        $watched["jikan:{$row['jikan_id']}:anime"] = true;
      }
    }

    return $watched;
  }
}
