<?php
// A class response so we reuse this and give response to frontend.

namespace App\Core;

class Response
{
    public static function json(array $data, int $statusCode = 200): void
    {
      header('Content-Type: application/json');
      http_response_code($statusCode);
      echo json_encode($data);
      exit;
    }

    public static function unauthorized(string $message = 'Unauthorized'): void
    {
      self::json(['error' => $message], 401);
    }

    public static function notFound(string $message = 'Not found'): void
    {
      self::json(['error' => $message], 404);
    }

    public static function badRequest(string $message = 'Bad request'): void
    {
      self::json(['error' => $message], 400);
    }

    public static function success(string $message = 'success'): void
    {
      self::json(['success' => $message], 200);
    }
}
