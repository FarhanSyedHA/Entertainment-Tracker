<?php

namespace App\Actions\Auth;

use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\UserResponseDTO;
use App\Services\AuthService;

class LoginAction
{
    public function __invoke(Request $request): Response
    {
        $dto = LoginDTO::fromRequest($request);

        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $service = new AuthService();
        $result = $service->login($dto);

        return Response::json([
            'token' => $result['token'],
            'user' => UserResponseDTO::fromEntity($result['user'])->toArray(),
        ]);
    }
}
