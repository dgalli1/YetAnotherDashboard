<?php
namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoutingManager {

    private static $routes;

    public static function handleRoute() {
        foreach (self::$routes as $key => $route) {
            /** @var \Closure  */
            $closure = $route['closure'];
            /** @var Response */
            $response = $closure->call($route['class'],Request::createFromGlobals());
            $response->send();
            die();
        }
    }

    public static function registerRoute($function,$instance) {
        self::$routes[] = [
            'closure' => $function,
            'class' => $instance
        ];
    }
}