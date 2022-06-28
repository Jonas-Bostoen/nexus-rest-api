<?php

namespace Nexus;

class BadRequestException extends \Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
  }
}
