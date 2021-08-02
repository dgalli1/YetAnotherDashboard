<?php
namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoutingManager {

    private static $routes;

    public static function handleRoute() {
        foreach (self::$routes as $key => $route) {
            if($route['url'] !== $_GET['path'])
            /** @var \Closure  */
            $closure = $route['closure'];
            /** @var Response */
            $response = $closure->call($route['class'],Request::createFromGlobals());
            $response->send();
            die();
        }
    }

    private static function getUrl($plugin,$name) {
        return "/plugin/".strtolower($plugin['name'])."/".$name;
    }

    public static function registerRoute($plugin,$name, $function,$instance) {
        self::$routes[] = [
            'url' => self::getUrl($plugin,$name),
            'closure' => $function,
            'class' => $instance
        ];
    }
}