<?php

namespace App\Actions\Auth;

use App\Core\Request;
use App\Core\Response;
use App\DTOs\Auth\UserResponseDTO;

class MeAction
{
    public function __invoke(Request $request): Response
    {
        return Response::json([
            'user' => UserResponseDTO::fromEntity($request->user)->toArray(),
        ]);
    }
}
