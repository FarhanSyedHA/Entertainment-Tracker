<?php

namespace App\Actions\Token;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class ListAction
{
    public function __invoke(Request $request): Response
    {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            SELECT id, name, last_used_at, created_at,
                   CONCAT(LEFT(token, 8), "...") as token_preview
            FROM api_tokens
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ');
        $stmt->execute(['user_id' => $request->user->id]);

        return Response::json(['tokens' => $stmt->fetchAll()]);
    }
}
