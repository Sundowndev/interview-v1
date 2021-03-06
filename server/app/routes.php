<?php

use App\Service\DotEnvParser;
use App\Service\JsonResponse;

$router->setNamespace('\App\Controller');

$router->before('GET|POST|PUT|DELETE', '/.*', function () use ($router) {
    date_default_timezone_set('Europe/Paris');

    # This will be always executed
    $dotEnvParser = new DotEnvParser();
    $dotEnvParser->run();

    $jsonResponse = new JsonResponse();

    if ($_SERVER['HTTP_ACCEPT'] !== 'application/json') {
        $code = 400;
        $message = 'Accept header is not set to "application/json".';
        return $jsonResponse->create($code, $message, []);
    } elseif ($_SERVER['REQUEST_METHOD'] != 'GET' && $_SERVER['CONTENT_TYPE'] !== 'application/json') {
        $code = 400;
        $message = 'Content-type header is not set to "application/json".';
        return $jsonResponse->create($code, $message, []);
    }
});

/**
 * API index
 */
$router->get('/', 'DefaultController@index');

/**
 * 404 error response
 */
$router->set404('DefaultController@error');

/**
 * Session handling routes
 */
$router->post('/auth', 'SessionController@auth');
$router->post('/signup', 'SessionController@signup');
$router->post('/logout', 'SessionController@signout');
$router->get('/me', 'SessionController@me');

/**
 * Task resource
 */
$router->mount('/tasks', function () use ($router) {
    // Get all tasks
    $router->get('/', 'TaskController@getAll');

    // Get one task
    $router->get('/(\d+)', 'TaskController@get');

    // Create a task
    $router->post('/', 'TaskController@post');

    // Update a task
    $router->put('/(\d+)', 'TaskController@put');

    // Delete a task
    $router->delete('/(\d+)', 'TaskController@delete');
});

/**
 * User resource
 */
$router->mount('/users', function () use ($router) {
    // Get one user
    $router->get('/(\d+)', 'UserController@get');

    // Get one task's tasks
    $router->get('/(\d+)/tasks', 'UserController@getTasks');
});

// Quickfix for Chrome prelight request on OPTIONS method
$router->options('(.*)', 'DefaultController@index');