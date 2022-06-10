<?php

namespace Nexus;

header('Content-Type: application/json');
include_once __DIR__ . '/Request.php';

class Router
{
  private $request;
  private $object;

  function __construct($object = null)
  {
    $this->request = new Request();
    $this->object = $object;
    $this->routerInit($object);
  }

  private function routerInit($object)
  {
    if ($object === null) return;
    foreach ($object as $key => $value) {
      $this->$key = $value;
    }
  }

  function __call($name, $parameters)
  {
    $middleWare = [];
    $route = '';
    $method = null;
    foreach ($parameters as $key => $param) {
      if ($key === 0) {
        $route = $param;
      } elseif ($key === 1) {
        $method = $param;
      } else {
        array_push($middleWare, $param);
      }
    }

    if ($this->object !== null && $this->prefix) {
      $route = "{$this->prefix}{$route}";
    }
    $this->{strtolower($name)}[$this->formatRoute($route)] = [$method, $middleWare];
  }

  private function formatRoute($route)
  {
    $result = rtrim($route, '/');
    if ($result === '') return '/';
    return $result;
  }

  /**
   * Resolves a route
   */
  function resolve()
  {
    $methodDictionary = $this->{strtolower($this->request->requestMethod)};
    $formattedRoute = $this->formatRoute($this->request->requestUri);
    $method = null;
    $params = [];

    foreach ($methodDictionary as $key => $value) {
      if ($formattedRoute === $key) {
        $method = $value[0];
        $this->request->setMiddleware($value[1]);
      } else {
        $path_array = explode('/', $key);
        $route_array = explode('/', $formattedRoute);

        if (sizeof($path_array) === sizeof($route_array)) {
          $path = [];
          for ($x = 0; $x < sizeof($path_array); $x++) {
            // Guard clause
            if (($path_array[$x] !== $route_array[$x]) && !str_starts_with($path_array[$x], ':')) {
              break;
            }
            // Sets the params in the route
            if (str_starts_with($path_array[$x], ':')) {
              if ($path_array[$x] !== null) {
                $params[substr($path_array[$x], 1)] = $route_array[$x];
              }
            }
            array_push($path, $path_array[$x]);
          }
          if ($path === $path_array) {
            $method = $value[0];
            $this->request->setMiddleware($value[1]);
          }
        }
      }
    }

    if (is_null($method)) {
      $this->failedRequest();
      return;
    }

    try {
      $this->request->executeMiddleware();
      $this->request->calcExecTime();
      echo json_encode(call_user_func_array($method, array($this->request)));
    } catch (\Exception $e) {
      $this->failedRequest();
    }
  }

  private function failedRequest()
  {
    header("{$this->request->serverProtocol} 404 Not Found");
    $message = [
      'code' => 'NOT_FOUND',
      'message' => "Unknown resource: {$this->formatRoute($this->request->requestUri)}"
    ];
    echo json_encode($message);
  }

  function __destruct()
  {
    $this->resolve();
  }
}
