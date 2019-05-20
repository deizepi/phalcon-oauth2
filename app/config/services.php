<?php

use Phalcon\Filter;
use Phalcon\Security;
use Phalcon\Translate\Factory as I18n;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Model\Metadata\Memory as MetaData;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Events\Manager as EventsManager;

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});

$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

// Set a models manager
$di->set(
    'modelsManager',
    function(){
        $manager = new \Phalcon\Mvc\Model\Manager();
        return $manager;
    }
);

// Use the memory meta-data adapter or other
$di->set(
    'modelsMetadata',
    new MetaData()
);

/**
 * Add security
 */
$di->setShared('security', function () {
    $security = new \Phalcon\Security();
    $security->setWorkFactor(12);
    return $security;
});

/**
 * Add request
 */
$di->setShared('request', function () {
    return new \Phalcon\Http\Request();
});

/**
 * Add response
 */
$di->setShared('response', function () {
    return new JsonResponse();
});

/**
 * Add config
 */
$di->setShared('config', $config);

$di->setShared('i18n', function () use ($config) {

    $options = [
        'locale'        => 'pt_BR.UTF-8',
        'defaultDomain' => 'translations',
        'directory'     => $config->application->translationsDir,
        'adapter'       => 'gettext',
    ];

    $translate = I18n::load($options);
    $language = 'pt_BR';

    $translationFile = APP_PATH . '/translations/' . $language . '.php';
    if (file_exists($translationFile)) {
        require $translationFile;
    } else {
        require APP_PATH . '/translations/en_AU.php';
    }

    return new TranslateAdapter(
        [
            'content' => $lang,
        ]
    );
});

$di->setShared('filter', function() {
    $filter = new Filter();

    $filter->add('html', function ($value) {
        $tags = "<p><b><i><strike><ul><ol><li><div><br><img><a><font><span><blockquote>";
        return trim(addslashes(strip_tags($value, $tags)));;
    });

    $filter->add('date', function($value){
        return preg_replace("/[^0-9\-]/", "", $value);
    });

    $filter->add('datetime', function($value){
        return preg_replace("/[^0-9\-: ]/", "", $value);
    });

    return $filter;
});

$di->set('view', function() {
    $view = new \Phalcon\Mvc\View();
    return $view;
});

// Registering a dispatcher
$di->set(
    'dispatcher',
    function () use ($di) {
        $eventsManager = new EventsManager();
        $eventsManager->attach(
            'dispatch:beforeExecuteRoute',
            new ScopesPlugin()
        );
        
        $eventsManager->attach(
            'dispatch:afterExecuteRoute',
            new LoggerRequestPlugin()
        );

        $dispatcher = new MvcDispatcher();
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    }
);