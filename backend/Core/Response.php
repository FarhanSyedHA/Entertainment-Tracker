<?php
// A class response so we reuse this and give response to frontend.

namespace App\Core;

class Response
{
    public function json(array $data, int $statusCode = 200): void
    {
      header('Content-Type: application/json');
      http_response_code($statusCode);
      echo json_encode($data);
    }

    public static function unauthorized(string $message = 'Unauthorized'): void
    {
      (new self())->json(['error' => $message], 401);
      exit;
    }

    public static function notFound(string $message = 'Not found'): void
    {
      (new self())->json(['error' => $message], 404);
      exit;
    }

    public static function badRequest(string $message = 'Bad request'): void
    {
      (new self())->json(['error' => $message], 400);
      exit;
    }

    public static function success(string $message = 'success'): void
    {
      (new self())->json(['error' => $message], 200);
      exit
    }
}
