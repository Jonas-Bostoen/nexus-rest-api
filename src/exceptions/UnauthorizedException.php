<?php

namespace Nexus;

class UnauthorizedException extends \Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
  }
}
