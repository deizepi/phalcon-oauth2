<?php

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

class ScopesPlugin extends Plugin
{
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $annotations = $this->annotations->getMethod(
            $dispatcher->getControllerClass(),
            $dispatcher->getActiveMethod()
        );
        
        if ($annotations->has('Scopes'))
        {
            $annotation = $annotations->get('Scopes');
            $this->session->set("scopes", $annotation->getArgument(0));
        }
        else 
        {
            $defaults = array_column(Escopo::find("padrao = 1")->toArray(), "escopo");
            $this->session->set("scopes", $defaults);
        }
    }
}