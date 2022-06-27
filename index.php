<?php

require_once __DIR__ . './middleware.php';
require_once __DIR__ . '/src/App.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Nexus\Request;
use Nexus\App;

$app = new App([
  'prefix' => '/api',
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



// $app->get('/ping', function (Request $request) {
//   return [
//     'code' => 'OK',
//     'message' => 'pong'
//   ];
// });

// $app->post('/blog', function (Request $request) {
//   return $request->body();
// });

// $app->delete('/blog/:id', function (Request $request) {
//   $param = $request->params()['id'];
//   return $param;
// });


$app->group(
  ['prefix' => '/health'],

  $app->get('/ping', $auth, function (Request $request) {
    return [
      'message' => 'pong'
    ];
  }),
  $app->get('/status', function (Request $request) {
    return [
      'code' => 'OK'
    ];
  })
);

// $app->get('/ping', function (Request $request) {
//   return $request;
// });


// $functionality1 = function (Request $request) {
//   return ['status' => 'ok'];
// };

// $functionality2 = function (Request $request) {
//   return ['status' => 'ok'];
// };


// $app->group(
//   ['prefix' => '/health'],
//   $app->get('/ping', function (Request $request) {
//     return ['status' => 'ok'];
//   }),
//   $app->get('/test', function (Request $request) {
//     return ['status' => 'ok'];
//   })
// );

// $router->get("test/peepee/", function (Request $request, Closure $next) {
//   $next();
// }, $functionality1);

// $router->get("/test2", $functionality2);
