<?php

namespace Nexus;

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

  /**
   * Initializes the router and sets the options
   * @param $options
   */
  private function routerInit($options)
  {
    if ($options === null) return;
    $this->options = $options;
    foreach ($options as $key => $value) {
      $this->$key = $value;
    }
  }

  /**
   * Resolves a route
   * @param $route
   */
  private function formatRoute($route)
  {
    $result = rtrim($route, '/');
    if ($result === '') return '/';
    return $result;
  }

  /**
   * Groups routes with a specified prefix
   * @param $options
   * @param $requests
   */
  public function group($options, ...$requests)
  {
    $prefix = $options['prefix'];
    // removes fist slash
    if ($prefix[0] === '/') {
      $request_name = substr($prefix, 1);
    }
    // removes last slash2
    if ($prefix[strlen($prefix) - 1] === '/') {
      $prefix = substr($prefix, 0, strlen($prefix) - 1);
    }

    foreach ($requests as $request) {
      // New request name
      if ($this->options !== null && key_exists('prefix', $this->options)) {
        $request_name = explode($this->options['prefix'], $request[1]);
        $request_name = "{$this->options['prefix']}{$prefix}{$request_name[1]}";
      } else {
        $request_name = "{$prefix}/{$request[1]}";
      }

      $this->{"$request[0]"}[$request_name] = $this->{"$request[0]"}[$request[1]];
      unset($this->{"$request[0]"}[$request[1]]);
    }
  }

  /**
   * Saves the routes in the router
   * @param $request_method
   * @param $arguments
   */
  public function __call($request_method, $arguments)
  {
    $request_name = '';
    $request_function = null;
    $request_middleware = [];

    foreach ($arguments as $value) {
      if (gettype($value) === 'string') {
        $request_name = $value;
        // removes fist slash
        if ($request_name !== '/') {
          if ($request_name[0] === '/') {
            $request_name = substr($request_name, 1);
          }
          // removes last slash
          if ($request_name[strlen($request_name) - 1] === '/') {
            $request_name = substr($request_name, 0, strlen($request_name) - 1);
          }
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

    if ($this->options !== null && key_exists('prefix', $this->options)) {
      $request_name = "{$this->prefix}/{$request_name}";
    }
    $this->{strtolower($request_method)}[$this->formatRoute($request_name)] = [$request_function, $request_middleware];
    return [strtolower($request_method), $this->formatRoute($request_name)];
  }

  /**
   * Executes the function and middleware for the current url 
   */
  public function resolve()
  {
    $methodDictionary = $this->{strtolower($this->request->requestMethod)};
    $formattedRoute = $this->formatRoute($this->request->requestUri);
    $method = null;
    $params = [];

    if ($this->request->requestMethod === "OPTIONS") {
      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("HTTP/1.1 200 OK");
      }
      return;
    }

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
      $this->requestNotFound();
      return;
    }

    try {
      $this->request->setParams($params);
      $this->request->executeMiddleware();
      $this->request->calcExecTime();
      echo json_encode(call_user_func_array($method, array($this->request)));
    } catch (\Exception $e) {
      $this->failedRequest($e);
    }
  }

  /**
   * Handles a thrown exception and returns a failed request
   * @param $e
   */
  private function failedRequest($exception)
  {
    header("{$this->request->serverProtocol} 500 Internal Server Error");
    $message = [
      'code' => 'INTERNAL_SERVER_ERROR',
      'message' => $exception->getMessage(),
      'stack' => $exception->getTraceAsString()
    ];
    echo json_encode($message);
  }

  /**
   * Handles a 404 request
   */
  private function requestNotFound()
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
