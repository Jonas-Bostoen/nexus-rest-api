<?php

namespace Nexus;

require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/CORS.php';

class App
{
  private $router;
  private static $database = null;
  private static $modules = [];
  private static $dbOptions;

  public function __construct($options = null)
  {
    $this->handleOptions($options);
  }

  private function handleOptions($options = null)
  {
    // Isolating database config
    if (key_exists('db_connection', $options)) {
      try {
        $database_connection =  $options['db_connection'];
        self::$dbOptions = $database_connection;
        unset($options['db_connection']);
      } catch (\Exception $exception) {
        echo json_encode([
          'code' => 'MYSQL_CONNECTION_ERROR',
          'message' => $exception->getMessage(),
          'stack' => $exception->getTraceAsString()
        ]);
        return;
      }
      if (key_exists('CORS', $options)) {
        new CORS($options['CORS']);
        unset($options['CORS']);
      }
    }
    // Creating router with config
    $this->router = new Router($options);
  }

  private static function setDatabaseConnection($host, $user, $password, $database, $port = 3306)
  {
    self::$database = new \mysqli($host, $user, $password, $database, $port);
    if (self::$database->connect_errno) {
      throw new \Exception('Failed to connect to MySQL: ' . self::$database->connect_error);
    }
  }

  /**
   * Database
   */

  public static function dbConnect()
  {
    self::setDatabaseConnection(...self::$dbOptions);
  }

  public static function dbExit()
  {
    self::$database->close();
  }

  public static function db()
  {
    return self::$database;
  }

  /**
   * Modules
   */
  public static function appendModule($key, $value)
  {
    self::$modules[$key] = $value;
  }

  public static function requireModule($key)
  {
    return self::$modules[$key];
  }

  /**
   * Facade methods for Router
   */
  public function get($route, ...$callback)
  {
    if (!isset($this->router)) return;
    return $this->router->get($route, ...$callback);
  }

  public function post($route, ...$callback)
  {
    if (!isset($this->router)) return;
    return $this->router->post($route, ...$callback);
  }

  public function put($route, ...$callback)
  {
    if (!isset($this->router)) return;
    return $this->router->put($route, ...$callback);
  }

  public function delete($route, ...$callback)
  {
    if (!isset($this->router)) return;
    return $this->router->delete($route, ...$callback);
  }

  public function group($options, ...$requests)
  {
    if (!isset($this->router)) return;
    return $this->router->group($options, ...$requests);
  }
}
