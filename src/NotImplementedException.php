<?php

namespace Nexus;

class NotImplementedException extends \Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
  }
}
