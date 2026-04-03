<?php

namespace App\Core\Middleware;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Exceptions\UnauthorizedException;
use App\Entities\User;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $token = $request->getBearerToken();

        if (!$token) {
            throw new UnauthorizedException('Missing authorization token');
        }

        $db = Database::getInstance();

        $stmt = $db->prepare('
            SELECT u.* FROM users u
            INNER JOIN api_tokens t ON t.user_id = u.id
            WHERE t.token = :token
            AND (t.expires_at IS NULL OR t.expires_at > NOW())
        ');
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new UnauthorizedException('Invalid or expired token');
        }

        $db->prepare('UPDATE api_tokens SET last_used_at = NOW() WHERE token = :token')
            ->execute(['token' => $token]);

        $request->user = User::fromRow($row);

        return $next($request);
    }
}
