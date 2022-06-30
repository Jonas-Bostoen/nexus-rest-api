<?php

namespace Nexus;

class Request
{
  private $params;
  private $middleware;
  private $requestFailed = false;
  public $totalRequestTime;
  private $timeStart;

  // Constructor
  function __construct()
  {
    $this->init();
    $this->timeStart = microtime(true);
  }

  public function setParams($params)
  {
    $this->params = $params;
  }

  public function setMiddleware($middleware)
  {
    $this->middleware = $middleware;
  }

  public function calcExecTime()
  {
    $time = round((microtime(true) - $this->timeStart) * 10000);
    $this->totalRequestTime = "{$time} ms";
  }

  // Sets all $_SERVER variables as attributes (request data)
  private function init()
  {
    foreach ($_SERVER as $key => $value) {
      $this->{$this->toCamel($key)} = $value;
    }
  }

  // Changes the format of the string to make it easier readable
  private function toCamel($string)
  {
    $result = strtolower($string);
    preg_match_all('/_[a-z]/', $result, $matches);
    foreach ($matches[0] as $match) {
      $capital = str_replace('_', '', strtoupper($match));
      $result = str_replace($match, $capital, $result);
    }
    return $result;
  }

  public function params()
  {
    return $this->params;
  }

  // Checks the requests and calls the right function to return the data
  public function body()
  {
    if ($this->requestMethod === 'GET') return;
    return $this->bodyFormat();
  }

  public function executeMiddleware()
  {
    foreach ($this->middleware as $mw_function) {
      $this->requestFailed = true;

      $mw_function($this, function () {
        $this->requestFailed = false;
      });

      if ($this->requestFailed) {
        throw new \Exception('test');
      }
    }
  }

  private function bodyFormat()
  {
    return json_decode(file_get_contents('php://input'));
  }
}
