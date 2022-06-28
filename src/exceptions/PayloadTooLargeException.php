<?php

namespace Nexus;

class PayloadTooLargeException extends \Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
  }
}
