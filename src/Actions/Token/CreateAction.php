<?php

namespace App\Actions\Token;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class CreateAction
{
    public function __invoke(Request $request): Response
    {
        $body = $request->getBody();
        $name = trim($body['name'] ?? 'Stremio');

        $db = Database::getInstance();
        $token = bin2hex(random_bytes(32));

        $stmt = $db->prepare('
            INSERT INTO api_tokens (user_id, token, name)
            VALUES (:user_id, :token, :name)
        ');
        $stmt->execute([
            'user_id' => $request->user->id,
            'token' => $token,
            'name' => $name,
        ]);

        return Response::json([
            'token' => $token,
            'name' => $name,
            'message' => 'Token created. Save it — it won\'t be shown again.',
        ], 201);
    }
}
