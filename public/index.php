<?php
// From udemy course
// $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// $segments = explode('/', trim($path, '/'));
// $controller = $segments[0] ?? 'products';
// $action = $segments[1] ?? 'index';

// // $controller = $_GET['controller'] ?? 'products';
// // $action = $_GET['action'] ?? 'index';

// require __DIR__ . "/../app/controllers/$controller.php";

// $controllerObject = new $controller;
// $controllerObject->$action();


// from google
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\Products;
use App\Controllers\AuthController;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

$request = Request::createFromGlobals();

// Define Routes
$routes = new RouteCollection();
$routes->add('register', new Route('/register', ['_controller' => [new AuthController(), 'register']]));
$routes->add('login', new Route('/login', ['_controller' => [new AuthController(), 'login']]));

$routes->add('/', new Route('/', ['_controller' => [new Products(), 'index']]));

$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
    $attributes = $matcher->match($request->getPathInfo());
    $controller = $attributes['_controller'];
    $response = call_user_func($controller, $request);
} catch (Exception $e) {
    $response = new Response('Not Found', 404);
}

$response->send();