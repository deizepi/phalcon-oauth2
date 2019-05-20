<?php

$config = $di->getConfig();

$loader = new \Phalcon\Loader();
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->servicesDir,
        $config->application->modelsDir,
        $config->application->validatorsDir,
        $config->application->librariesDir,
    ]
);
$loader->register();