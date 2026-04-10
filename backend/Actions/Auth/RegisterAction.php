<?php
namespace App\Actions\Auth;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class RegisterAction
{
  public function handle(Request $request): void
  {
    $service = new AuthService();
    $response = new Response();

    $input = $request->getBody();
    $result = $service->register($input['email'], $input['username'], $input['password']);

    if (isset($result['error'])) {
      $response->json(['error' => $result['error']], 400);
      return;
    }

    $response->json($result, 201);
  }
}
