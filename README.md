# Nexus

Nexus php is a lightweight and fast PHP framework for building RESTful APIs.

## Installation

Easy installation via Composer:

```bash
composer require nexus-rest-api/nexus
```

## Getting started 

## APP

Nexus rest api uses an app decorator to handle all the functionality.
There is also a possibility to pass config to the app while creating it. The order of values in config does not matter but the key names do.
```php
<?php
  use Nexus\App;

  $config = [
    'prefix' => 'api',
    'db_connection' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => '',
      'database' => 'nexus'
      'port' => 3306 // optional (default 3306)
    ],
    'CORS' =>[
      'allowedOrigins' => ['http://localhost', 'https://*.example.com'],
      'allowedHeaders' => ['x-allowed-header', 'x-other-allowed-header'],
      'allowedMethods' => ['DELETE', 'GET', 'POST', 'PUT'],
      'exposedHeaders' => ['Content-Encoding'],
      'maxAge' => 0
    ] 
  ]

  $app = new App($config);
```

## HTACCESS
A .htaccess file is required for the router to work.
```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [L]
```

## SERVING THE APP
If your using composer the following script in the `composer.json` file will launch the app on port 9000 when typing the command `composer start` in the terminal.
```json
"scripts": {
  "start": [
    "Composer\\Config::disableProcessTimeout",
    "php -S localhost:9000 -t ./"
  ]
}
```

## ROUTER AND REQUESTS
All request are handled by the router, the requests use a string as name and a callback for the logic of the request.
The router returns the data in json format. The order and format of the parameters in the request is not important.
The following requests are supported in this framework:
- GET
- POST 
- PUT 
- DELETE

### examples:
```php
<?php

// Get request
$app->get('/ping', function(Request $request) {
  return [
    'code' => 'OK',
    'message' => 'pong'
  ];
});

// Post request
$app->post('/blog', function(Request $request) {
  return $request->body();
});

// Put request
$app->put('/blog/:id', function(Request $request) {
  $param = $request->params()['id'];
  // do something with $param
});

// Delete request
$app->delete('/blog/:id', function(Request $request) {
  $param = $request->params()['id'];
  // do something with $param
});
```

### Important request object parameters
| Parameter         | code example                  | info                                                          |
| ----------------- | ----------------------------- | ------------------------------------------------------------- |
| totalRequestTime  | `$request->totalRequestTime`  | returns in milliseconds how long the request took to complete |
| requestUri        | `$request->requestUri`        | returns the uri of the request                                |
| requestMethod     | `$request->requestMethod`     | returns the http method used in the request                   |
| httpAuthorization | `$request->httpAuthorization` | returns the bearer token given with the request               |
| params            | `$request->params()`          | returns the params of the request                             |
| body              | `$request->body()`            | returns the body of the request                               |

## ROUTER GROUPS 
The router can group routes together and set a prefix for all routes in that group. Note nesting groups is not supported yet.

### example:
- request /api/health/ping
- request /api/health/status

```php
<?php
$app->group(['prefix' => '/health'], 

  $app->get('/ping', function(Request $request) {
    return [
      'code' => 'OK',
      'message' => 'pong'
    ];
  })

  ,$app->get('/status', function(Request $request) {
    return [
      'code' => 'OK',
      'message' => 'pong'
    ];
  })
);
```

## MIDDLEWARE
Middleware is an extra function passed on to a request that is executed before the request is handled. The middleware can also stop a route from being executed, this by not calling the `$next()` function.

### example:
```php
<?php
$middleware = function(Request $request, Closure $next) {
  echo "this is a middleware function";
  $next();
};

// The order of the parameters is not important
$app->get('/ping', $middleware, function(Request $request) {
  return [
    'code' => 'OK',
    'message' => 'pong'
  ];
});
```