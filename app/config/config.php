<?php

use Phalcon\Config as PhalconConfig;

require_once(CONFIG_PATH . "/env.php");

return new PhalconConfig(
    [
        'database' => [
            'adapter'     => 'Mysql',
            'host'        => ENV_SV,
            'username'    => ENV_UN,
            'password'    => ENV_PW,
            'dbname'      => ENV_DB,
            'charset'     => 'utf8',
        ],
        'application' => [
            'appDir'            => APP_PATH . '/',
            'controllersDir'    => APP_PATH . '/controllers/',
            'servicesDir'       => APP_PATH . '/services/',
            'modelsDir'         => APP_PATH . '/models/',
            'validatorsDir'     => APP_PATH . '/validators/',
            'librariesDir'      => APP_PATH . '/libraries/',
            'repositoriesDir'   => APP_PATH . '/repositories/',
            'interfacesDir'     => APP_PATH . '/interfaces/',
            'translationsDir'   => APP_PATH . '/translations/',
            'baseUri'           => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
        ],
        'oauth' => [
            'token_atualizacao_expiracao'   => date("Y-m-d H:i:s", strtotime("+1 MONTH")),
            'token_acesso_expiracao'        => date("Y-m-d H:i:s", strtotime("+1 HOUR")),
            'codigo_autorizacao_expiracao'  => date("Y-m-d H:i:s", strtotime("+10 MINUTE")),
            'private_key'                   => 'C:/xampp/htdocs/cert/private.key',
            'public_key'                    => 'C:/xampp/htdocs/cert/public.key',
            'crypto_key'                    => 'FPPPTIPl83CUWSVg'
        ],
    ]
);