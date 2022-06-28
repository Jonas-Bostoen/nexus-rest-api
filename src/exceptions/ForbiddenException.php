<?php

namespace Nexus;

class ForbiddenException extends \Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
  }
}
