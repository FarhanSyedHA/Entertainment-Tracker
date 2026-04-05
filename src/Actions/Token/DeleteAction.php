<?php

namespace App\Actions\Token;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Exceptions\NotFoundException;

class DeleteAction
{
    public function __invoke(Request $request): Response
    {
        $id = (int) $request->getParam('id');
        $db = Database::getInstance();

        // Don't let user delete their current session token
        $currentToken = $request->getBearerToken();
        $stmt = $db->prepare('SELECT token FROM api_tokens WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $request->user->id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new NotFoundException('Token not found');
        }

        if ($row['token'] === $currentToken) {
            return Response::error('Cannot delete your current session token', 400);
        }

        $stmt = $db->prepare('DELETE FROM api_tokens WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $request->user->id]);

        return Response::json(['message' => 'Token deleted']);
    }
}
