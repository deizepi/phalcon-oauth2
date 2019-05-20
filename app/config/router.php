<?php

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

$router = $di->getRouter();

// Não encontrado
$router->notFound([
    "controller" => "nao_encontrado",
    "action"     => "index"
]);

$router->handle();

$router->getMatchedRoute()->beforeMatch(
    function($uri, $route){
        if((new Request())->isOptions()) // preflight send to not found
        {
            return false;
        }
        return true;
    }
);