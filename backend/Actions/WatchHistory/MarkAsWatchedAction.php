<?php
namespace App\Actions\WatchHistory;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Services\Api\TmdbService;
use App\Services\Api\JikanService;

class MarkAsWatchedAction
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
    if (($source === 'tmdb' && $type === 'anime') || ($source === 'jikan' && $type !== 'anime')) {
      Response::badRequest('source and type mismatch');
    }

    $pdo = Database::getInstance()->getConnection();

    try {
      $pdo->beginTransaction();
      $contentId = $this->findOrCreateContent($pdo, $source, $externalId, $type);
      $this->markContentCompleted($pdo, $userId, $contentId);
      $pdo->commit();
      Response::json(['success' => true, 'contentId' => $contentId]);
    } catch (\Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      Response::json(['error' => 'Failed to mark as watched: ' . $e->getMessage()], 500);
    }
  }

  private function findOrCreateContent(\PDO $pdo, string $source, int $externalId, string $type): int
  {
    $col = $source === 'tmdb' ? 'tmdb_id' : 'jikan_id';
    $stmt = $pdo->prepare("SELECT id FROM content WHERE type = :type AND $col = :ext LIMIT 1");
    $stmt->execute([':type' => $type, ':ext' => $externalId]);
    $row = $stmt->fetch();
    if ($row) return (int) $row['id'];

    $details = $this->fetchDetails($source, $externalId, $type);

    $stmt = $pdo->prepare("
      INSERT INTO content (type, title, description, release_year, imdb_rating, thumbnail_url, duration_seconds, tmdb_id, jikan_id)
      VALUES (:type, :title, :description, :year, :rating, :thumb, :duration, :tmdb, :jikan)
    ");
    $stmt->execute([
      ':type' => $type,
      ':title' => $details['title'],
      ':description' => $details['overview'] ?? null,
      ':year' => !empty($details['year']) ? (int) $details['year'] : null,
      ':rating' => $details['rating'] ?? null,
      ':thumb' => $details['poster'] ?? null,
      ':duration' => ($type === 'movie' && !empty($details['runtime'])) ? (int) $details['runtime'] * 60 : null,
      ':tmdb' => $source === 'tmdb' ? $externalId : null,
      ':jikan' => $source === 'jikan' ? $externalId : null,
    ]);

    return (int) $pdo->lastInsertId();
  }

  private function fetchDetails(string $source, int $externalId, string $type): array
  {
    if ($source === 'tmdb' && $type === 'movie')  return (new TmdbService())->getMovieDetails($externalId);
    if ($source === 'tmdb' && $type === 'tv')     return (new TmdbService())->getTvShowDetails($externalId);
    if ($source === 'jikan' && $type === 'anime') return (new JikanService())->getAnimeDetails($externalId);
    throw new \RuntimeException('Unsupported source/type combination');
  }

  private function markContentCompleted(\PDO $pdo, int $userId, int $contentId): void
  {
    $check = $pdo->prepare("SELECT id FROM progress WHERE user_id = :user AND content_id = :content AND episode_id IS NULL LIMIT 1");
    $check->execute([':user' => $userId, ':content' => $contentId]);
    $existing = $check->fetch();

    if ($existing) {
      $upd = $pdo->prepare("UPDATE progress SET status = 'completed', updated_at = CURRENT_TIMESTAMP WHERE id = :id");
      $upd->execute([':id' => (int) $existing['id']]);
      return;
    }

    $ins = $pdo->prepare("INSERT INTO progress (user_id, content_id, episode_id, status) VALUES (:user, :content, NULL, 'completed')");
    $ins->execute([':user' => $userId, ':content' => $contentId]);
  }
}
