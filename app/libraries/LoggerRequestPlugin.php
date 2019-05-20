<?php

use Phalcon\Events\Event;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Logger\Adapter\File;

class LoggerRequestPlugin extends Plugin
{
    public function afterExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $request = $this->getDi()->get("request");

        if($request->getMethod() == "OPTIONS")
            exit;

        $data = date("Ymd");
        $path = LOG_PATH . "/" . $data;
        if(!is_dir($path))
            mkdir($path);

        $log = "\r\n\tAcessada a URL: " . $request->getMethod() . " " . $request->getURI() . "\r\n";
        $log .= "\tIP: " . $request->getClientAddress();
        if($request->getHeader('AUTHORIZATION'))
            $log .= "\r\n\tAutorização: " . $request->getHeader('AUTHORIZATION');
        else 
            $log .= "\r\n\tNão foi enviada autorização";
        if(count($dispatcher->getParams()))
            $log .= "\r\n\tParams: " . json_encode($dispatcher->getParams());
        if(count($_POST))
            $log .= "\r\n\tParâmetros recebidos como POST: " . json_encode($_POST);
        if(!is_null($request->getJsonRawBody(true)))
            $log .= "\r\n\tParâmetros recebidos via JSON: " . json_encode($request->getJsonRawBody(true));
        
        $logger = new File($path . "/request.log");
        $logger->log($log);
        $logger->close();
    }
}