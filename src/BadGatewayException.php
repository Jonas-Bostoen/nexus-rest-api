<?php

namespace Nexus;

class BadGatewayException extends \Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
  }
}
