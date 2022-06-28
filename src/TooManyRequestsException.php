<?php

namespace Nexus;

class TooManyRequestsException extends \Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
  }
}
