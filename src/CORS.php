<?php

namespace Nexus;

class CORS
{
  private $allowedOrigins = [];
  private $allowedMethods = [];
  private $allowedHeaders = [];
  private $exposedHeaders = [];
  private $maxAge = 0;

  public function __construct($options)
  {
    $this->handle($options);
    $this->setCORSHeaders();
  }

  private function handle($options)
  {
    $possibleOptions = ['allowedOrigins', 'allowedHeaders', 'allowedMethods', 'exposedHeaders', 'maxAge'];
    foreach ($options as $key => $value) {
      if (in_array($key, $possibleOptions)) {
        $this->$key = $value;
      }
    }
  }

  private function setCORSHeaders()
  {
    header('Access-Control-Allow-Origin: ' . implode(', ', $this->allowedOrigins));
    header('Access-Control-Allow-Headers: ' . implode(', ', $this->allowedHeaders));
    header('Access-Control-Allow-Methods: ' . implode(', ', $this->allowedMethods));
    header('Access-Control-Allow-exposedHeaders: ' . implode(', ', $this->exposedHeaders));
    header('Access-Control-Max-Age: ' . $this->maxAge);
  }
}
