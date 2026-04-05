<?php
//a class response so we reuse this and give response to frontend.

namespace App\Core;

class Response 
{
    public function json(array $data,int $statusCode = 200): void
    {
      header('Content-Type: application/json');
      http_response_code($statusCode);
      echo json_encode($data);
    }
}
