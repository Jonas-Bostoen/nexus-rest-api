<?php

require_once __DIR__ . '/src/NewRouter.php';
require_once __DIR__ . '/src/CORS.php';
require_once __DIR__ . './middleware.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Nexus\CORS;
use Nexus\Router;

$cors = new CORS([
  'allowedOrigins' => ['*'],
]);

$router = new Router(['prefix' => '/api']);
// $router->group("/health", function () use ($router) {





//   // Get request
//   $router->get("/ping", function ($request) {
//     return json_encode(['status' => 'ok']);
//   });
// });

$functionality = function ($request) {
  return json_encode(['status' => 'ok']);
};

$router->group(
  ['prefix' => '/health'],
  $router->get("/test", $functionality),
  $router->get("/test2", $functionality)
);
