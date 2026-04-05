<?php
//The key insight: constructor creates the PDO connection. getInstance creates the Database object (which triggers the constructor). getConnection just returns the existing PDO
//this is singleton behaving class, can only be called once and not by any one else cause of the private construct. 
namespace App\Core;

class Database {
  private static ?Database $instance = null;
  private \PDO $pdo;

  private function __construct() {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_NAME');
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    $this->pdo = new \PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
  }

  public static function getInstance(): self
  {
    if(!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getConnection(): \PDO
  {
    return $this->pdo;
  }

}