<?php

require_once __DIR__ . '/src/NewRouter.php';
require_once __DIR__ . '/src/CORS.php';
require_once __DIR__ . './middleware.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Nexus\CORS;
use Nexus\Request;
use Nexus\Router;

$cors = new CORS([
  'allowedOrigins' => ['*'],
]);

$router = new Router(['prefix' => '/api']);
// $router->group("/health", function () use ($router) {





//   // Get request
//   $router->get("/ping", function ($request) {
//     return ['status' => 'ok'];
//   });
// });

$functionality1 = function (Request $request) {
  return ['status' => 'ok'];
};

$functionality2 = function (Request $request) {
  // throw new Exception('Error');
  return ['status' => 'ok'];
};

$router->group(
  ['prefix' => '/health', 'middleware' => $auth],
  $router->get('/ping', function ($request) {
    return ['status' => 'ok'];
  }),
  $router->get('/test', function ($request) {
    return ['status' => 'ok'];
  })
);


$router->get("test/peepee/", function (Request $request, $next) {
  $next();
}, $functionality1);
$router->get("/test2", $functionality2);
