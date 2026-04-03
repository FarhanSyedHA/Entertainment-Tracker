<?php

namespace App\Actions\Auth;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class LogoutAction
{
    public function __invoke(Request $request): Response
    {
        $service = new AuthService();
        $service->logout($request->user->id, $request->getBearerToken());

        return Response::json(['message' => 'Logged out successfully']);
    }
}
