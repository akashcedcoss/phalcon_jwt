<?php

namespace app\listeners;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;


// use Phalcon\Logger;

class getdata extends Injectable
{
    public function getController()
    {
        $controllers = [];

        foreach (glob(APP_PATH . '/controllers/*Controller.php') as $controller) {
            $className = basename($controller, '.php');
            $controllers[$className] = [];
        }
        return $controllers;
    }

    public function getMethod($controller)
    {
        $ActionMethod = [];
        $controller = APP_PATH . '/controllers/' .$controller.'.php';

        $className = basename($controller, '.php');
        $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                $ActionMethod[] = $method->name;
            }
        }
        return $ActionMethod;
    }
}