<?php
namespace App\Services;

use App\Core\Database;
use App\Adapters\TmdbAdapter;
use App\Adapters\JikanAdapter;
use App\Services\Api\TmdbService;
use App\Services\Api\JikanService;

class ContentService
{
  public function getDetailsWithSeasons(int $externalId, string $frontendType): array
  {
    $details = $this->fetchDetails($externalId, $frontendType);
    if (!$details || empty($details['id'])) return $details ?: [];

    if ($frontendType === 'movie') {
      $details['seasons'] = [];
      return $details;
    }

    $source = $frontendType === 'anime' ? 'jikan' : 'tmdb';
    $dbType = $frontendType === 'tvshows' ? 'tv' : 'anime';

    $pdo = Database::getInstance()->getConnection();
    $contentId = $this->findOrCreateContent($pdo, $source, $externalId, $dbType, $details);

    $seasons = $this->loadCachedSeasons($pdo, $contentId);
    if (empty($seasons)) {
      $seasons = $this->fetchAndCacheSeasons($pdo, $contentId, $externalId, $frontendType);
    }

    $details['seasons'] = $seasons;
    return $details;
  }

  private function fetchDetails(int $externalId, string $frontendType): array
  {
    return match ($frontendType) {
      'movie'   => (new TmdbService())->getMovieDetails($externalId),
      'tvshows' => (new TmdbService())->getTvShowDetails($externalId),
      'anime'   => (new JikanService())->getAnimeDetails($externalId),
      default   => [],
    };
  }

  private function findOrCreateContent(\PDO $pdo, string $source, int $externalId, string $dbType, array $details): int
  {
    $col = $source === 'tmdb' ? 'tmdb_id' : 'jikan_id';
    $stmt = $pdo->prepare("SELECT id FROM content WHERE type = :type AND $col = :ext LIMIT 1");
    $stmt->execute([':type' => $dbType, ':ext' => $externalId]);
    $row = $stmt->fetch();
    if ($row) return (int) $row['id'];

    $ins = $pdo->prepare("
      INSERT INTO content (type, title, description, release_year, imdb_rating, thumbnail_url, duration_seconds, tmdb_id, jikan_id)
      VALUES (:type, :title, :description, :year, :rating, :thumb, :duration, :tmdb, :jikan)
    ");
    $ins->execute([
      ':type' => $dbType,
      ':title' => $details['title'] ?? '',
      ':description' => $details['overview'] ?? null,
      ':year' => !empty($details['year']) ? (int) $details['year'] : null,
      ':rating' => $details['rating'] ?? null,
      ':thumb' => $details['poster'] ?? null,
      ':duration' => null,
      ':tmdb' => $source === 'tmdb' ? $externalId : null,
      ':jikan' => $source === 'jikan' ? $externalId : null,
    ]);
    return (int) $pdo->lastInsertId();
  }

  private function loadCachedSeasons(\PDO $pdo, int $contentId): array
  {
    $stmt = $pdo->prepare("SELECT id, season_number FROM season WHERE content_id = :c ORDER BY season_number ASC");
    $stmt->execute([':c' => $contentId]);
    $seasons = $stmt->fetchAll();
    if (empty($seasons)) return [];

    $epStmt = $pdo->prepare("SELECT id, season_id, episode_number, title, duration_seconds FROM episode WHERE season_id = :s ORDER BY episode_number ASC");

    $result = [];
    foreach ($seasons as $s) {
      $epStmt->execute([':s' => (int) $s['id']]);
      $eps = $epStmt->fetchAll();
      $result[] = [
        'season_number' => (int) $s['season_number'],
        'name' => 'Season ' . $s['season_number'],
        'episode_count' => count($eps),
        'episodes' => array_map(fn($e) => [
          'episode_number' => (int) $e['episode_number'],
          'title' => $e['title'],
          'runtime_seconds' => $e['duration_seconds'] !== null ? (int) $e['duration_seconds'] : null,
        ], $eps),
      ];
    }
    return $result;
  }

  private function fetchAndCacheSeasons(\PDO $pdo, int $contentId, int $externalId, string $frontendType): array
  {
    try {
      $pdo->beginTransaction();
      $seasons = $frontendType === 'tvshows'
        ? $this->buildTvSeasons($externalId)
        : $this->buildAnimeSeasons($externalId);
      $this->persistSeasons($pdo, $contentId, $seasons);
      $pdo->commit();
      return $seasons;
    } catch (\Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      return [];
    }
  }

  private function buildTvSeasons(int $tmdbId): array
  {
    $tmdb = new TmdbService();
    $summaries = TmdbAdapter::toSeasonSummaries($tmdb->getTvShowSeasons($tmdbId));
    $out = [];
    foreach ($summaries as $s) {
      $rawEps = $tmdb->getSeasonEpisodes($tmdbId, $s['season_number']);
      $episodes = TmdbAdapter::toEpisodes($rawEps);
      $out[] = [
        'season_number' => $s['season_number'],
        'name' => $s['name'],
        'episode_count' => count($episodes),
        'episodes' => $episodes,
      ];
    }
    return $out;
  }

  private function buildAnimeSeasons(int $malId): array
  {
    $jikan = new JikanService();
    $raw = $jikan->getAnimeEpisodes($malId);
    $episodes = JikanAdapter::toEpisodes($raw);
    if (empty($episodes)) return [];
    return [[
      'season_number' => 1,
      'name' => 'Episodes',
      'episode_count' => count($episodes),
      'episodes' => $episodes,
    ]];
  }

  private function persistSeasons(\PDO $pdo, int $contentId, array $seasons): void
  {
    $seasonIns = $pdo->prepare("INSERT IGNORE INTO season (content_id, season_number) VALUES (:c, :n)");
    $seasonSel = $pdo->prepare("SELECT id FROM season WHERE content_id = :c AND season_number = :n LIMIT 1");
    $epIns = $pdo->prepare("INSERT IGNORE INTO episode (season_id, episode_number, title, duration_seconds) VALUES (:s, :n, :t, :d)");

    foreach ($seasons as $s) {
      $seasonIns->execute([':c' => $contentId, ':n' => $s['season_number']]);
      $seasonSel->execute([':c' => $contentId, ':n' => $s['season_number']]);
      $row = $seasonSel->fetch();
      if (!$row) continue;
      $seasonId = (int) $row['id'];
      foreach ($s['episodes'] as $e) {
        if (empty($e['episode_number'])) continue;
        $epIns->execute([
          ':s' => $seasonId,
          ':n' => $e['episode_number'],
          ':t' => $e['title'],
          ':d' => $e['runtime_seconds'],
        ]);
      }
    }
  }
}
