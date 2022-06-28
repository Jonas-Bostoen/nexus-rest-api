<?php

require_once __DIR__ . '/src/Router.php';
require_once __DIR__ . '/src/CORS.php';
require_once __DIR__ . '/src/App.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Nexus\Request;
use Nexus\App;
use Nexus\BadGatewayException;
use Nexus\BadRequestException;
use Nexus\exceptions\UnauthorizedException;
use Nexus\ForbiddenException;
use Nexus\NotImplementedException;
use Nexus\PayloadTooLargeException;
use Nexus\TooManyRequestsException;

$app = new App([
  'prefix' => '/api',
  'ENV' => 'development',
  'db_connection' => [
    'host' => 'localhost',
    'user' => 'root',
    'port' => 3306,
    'password' => 'root',
    'database' => 'wpcn',
  ],
  'CORS' => [
    'allowedOrigins' => ['*'],
  ]
]);


$app->get('/ping', function (Request $request) {
  throw new BadGatewayException("test");
  return 'pong';
});
