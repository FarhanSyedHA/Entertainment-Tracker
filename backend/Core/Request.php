<?php
namespace App\Core;

class Request 
{
  private string $method;
  private string $url;
  private ?array $body;

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
}