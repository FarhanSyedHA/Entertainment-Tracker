<?php
namespace App\Actions\Auth;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class LoginAction
{
  public function handle(Request $request): void
  {
    $service = new AuthService();
    $response = new Response();

    $input = $request->getBody();
    $result = $service->login($input['email'], $input['password']);

    if (isset($result['error'])) {
      $response->json(['error' => $result['error']], 401);
      return;
    }

    $response->json($result);
  }
}
