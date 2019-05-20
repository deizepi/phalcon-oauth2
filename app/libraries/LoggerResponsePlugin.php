<?php

use Phalcon\Events\Event;
use Phalcon\Http\Response;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Logger\Adapter\File;

class LoggerResponsePlugin extends Plugin
{
    public function beforeSendResponse(Event $event, Application $app, Response $response)
    {
        $request = $this->getDi()->get("request");

        if($request->getMethod() == "OPTIONS")
            exit;

        $data = date("Ymd");
        $path = LOG_PATH . "/" . $data;
        if(!is_dir($path))
            mkdir($path);

        $log = "\r\n\tAcessada a URL: " . $request->getMethod() . " " . $request->getURI() . "\r\n";
        $log .= "\tIP: " . $request->getClientAddress() . "\r\n";
        if($request->getHeader('AUTHORIZATION'))
            $log .= "\tAutorização: " . $request->getHeader('AUTHORIZATION') . "\r\n";
        else 
            $log .= "\tNão foi enviada autorização" . "\r\n";
            
        $log .= "\tResposta: " . $response->getHeaders()->get("Status") . " " . $response->getContent();
        
        $logger = new File($path . "/response.log");
        $logger->log($log);
        $logger->close();
    }
}