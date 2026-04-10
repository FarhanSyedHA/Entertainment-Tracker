<?php
namespace App\Core;

class Request 
{
  private string $method;
  private string $url;
  private ?array $body;
  private ?int $userId = null;

  public function __construct() {
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->url = $_SERVER['REQUEST_URI'];
    $this->body = json_decode(file_get_contents('php://input'), true);
  }

  public function getMethod() {
    return $this->method;
  }

  public function getPath() {
    return strtok($this->url, '?');
  }

  public function getBody() {
    return $this->body;
  }

  public function getHeader(string $name): ?string {
    $key = 'HTTP_' . strtoupper(str_replace('-','_',$name));
    return $_SERVER[$key] ?? null;
  }

  public function getUserId() {
    return $this->userId;
  }

  public function setUserId(int $userId) {
    $this->userId = $userId;
  }
}