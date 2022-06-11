<?php

namespace Nexus;

use Closure;

// Json header
header('Content-Type: application/json');
// Include request class
include_once __DIR__ . '/Request.php';

class Router
{
  // attributes
  private $request;
  private $options;

  // Constructor
  function __construct($options = null)
  {
    $this->request = new Request();
    $this->routerInit($options);
  }

  // Initializes the router
  private function routerInit($options)
  {
    if ($options === null) return;
    $this->options = $options;
    foreach ($options as $key => $value) {
      $this->$key = $value;
    }
  }

  // Formats a route
  private function formatRoute($route)
  {
    $result = rtrim($route, '/');
    if ($result === '') return '/';
    return $result;
  }

  public function group()
  {
    // echo "test";
  }

  public function __call($request_method, $arguments)
  {
    var_dump($arguments);
    $request_name = '';
    $request_function = null;
    $request_middleware = [];

    foreach ($arguments as $key => $value) {
      if (gettype($value) === 'string') {
        $request_name = $value;
        // removes fist slash
        if ($request_name[0] === '/') {
          $request_name = substr($request_name, 1);
        }
        // removes last slash
        if ($request_name[strlen($request_name) - 1] === '/') {
          $request_name = substr($request_name, 0, strlen($request_name) - 1);
        }
      } else {
        $reflection = new \ReflectionFunction($value);
        if (sizeof($reflection->getParameters()) === 1) {
          $request_function = $value;
        } else {
          array_push($request_middleware, $value);
        }
      }
    }

    if ($this->options !== null && $this->prefix) {
      $request_name = "{$this->prefix}/{$request_name}";
    }
    $this->{strtolower($request_method)}[$this->formatRoute($request_name)] = [$request_function, $request_middleware];

    // print_r($this);
  }
}
