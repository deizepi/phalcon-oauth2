<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use Phalcon\Events\Manager as EventsManager;

defined('BASE_PATH')   || define('BASE_PATH',   getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/..'));
defined('APP_PATH')    || define('APP_PATH',    BASE_PATH . '/app');
defined('LOG_PATH')    || define('LOG_PATH',    BASE_PATH . '/public/log');
defined('CONFIG_PATH') || define('CONFIG_PATH', APP_PATH . '/config');

require_once BASE_PATH . '/vendor/autoload.php';

try {
    
    $di = new FactoryDefault();
    $di->setShared('config', function () {
        return include CONFIG_PATH . "/config.php";
    });
    
    require_once(CONFIG_PATH . "/loader.php");
    
    require_once(CONFIG_PATH . "/services.php");
    
    require_once(CONFIG_PATH . "/router.php");

    $application = new Application($di);

    $eventsManager = new EventsManager();
    $eventsManager->attach(
        'application:beforeSendResponse',
        new LoggerResponsePlugin()
    );
    $application->setEventsManager($eventsManager);

    echo $application->handle()->getContent();

} catch (\Exception $e) {

    if($di->getShared('db')->isUnderTransaction())
        $di->getShared('db')->rollback();

    $code = in_array($e->getCode(), HttpStatusCodes::HTTP_STATUS) ? $e->getCode() : 400;

   $response = $di->getShared('response');
   return $response->sendError($e->getMessage(), "An error occurred", $code);

}
