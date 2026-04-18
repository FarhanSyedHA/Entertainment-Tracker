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
    $options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];

    // TiDB Cloud requires TLS. Setting MYSQL_ATTR_SSL_CA to '' used to silently
    // disable TLS on older mysqlnd builds; on PHP 8.2 with modern drivers it
    // fails the handshake and the server reports 'Access denied' — not 'SSL
    // required'. Fix: use the Debian-packaged CA bundle (present in php:8.2-cli)
    // and disable strict hostname verification (TiDB's cert CN doesn't match
    // the gateway host exactly).
    if (getenv('APP_ENV') === 'production' || str_contains($host, 'tidbcloud.com')) {
      $options[\PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
      $options[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }
    $this->pdo = new \PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), $options);
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