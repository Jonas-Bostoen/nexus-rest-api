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
  // private function formatRoute($route)
  // {
  //   $result = rtrim($route, '/');
  //   if ($result === '') return '/';
  //   return $result;
  // }

  function __call($name, $arguments)
  {
    $middleWare = [];
    $route = '';
    $method = null;

    print_r($arguments);

    foreach ($arguments as $key => $param) {
      // print_r($param);

      // if ($key === 0) {
      //   $route = $param;
      // } elseif ($key === 1) {
      //   $method = $param;
      // } else {
      //   array_push($middleWare, $param);
      // }


    }

    // if ($this->options !== null && $this->prefix) {
    // $route = "{$this->prefix}{$route}";
    // }
    // $this->{strtolower($name)}[$this->formatRoute($route)] = [$method, $middleWare];
    $this->{strtolower($name)}[$route] = "test";

    return [$name, $arguments];
  }
}
