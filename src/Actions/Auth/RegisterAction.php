<?php

namespace App\Actions\Auth;

use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\UserResponseDTO;
use App\Services\AuthService;

class RegisterAction
{
    public function __invoke(Request $request): Response
    {
        $dto = RegisterDTO::fromRequest($request);

        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $service = new AuthService();
        $user = $service->register($dto);

        return Response::json([
            'user' => UserResponseDTO::fromEntity($user)->toArray(),
        ], 201);
    }
}
