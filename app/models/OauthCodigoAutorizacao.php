<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Callback;

class OauthCodigoAutorizacao extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $codigo_autorizacao;

    /**
     *
     * @var string
     */
    public $codigo_desafio;

    /**
     *
     * @var string
     */
    public $identificador;

    /**
     *
     * @var string
     */
    public $expiracao;

    /**
     *
     * @var integer
     */
    public $ativo;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("oauth_codigo_autorizacao");
        $this->belongsTo('identificador', 'Oauth', 'identificador', ['alias' => 'Oauth']);
        $this->belongsTo('codigo_desafio', 'OauthCodigoDesafio', 'codigo_desafio', ['alias' => 'OauthCodigoDesafio']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_codigo_autorizacao';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthCodigoAutorizacao[]|OauthCodigoAutorizacao|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthCodigoAutorizacao|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    
    public function beforeValidation()
    {
        $validator = new Validation();

        $this->codigo_autorizacao = Oauth::generateAuthorizationCode();

        $validator->add(
            'identificador',
            new Callback(
                [
                    "callback" => function($data) {
                        $oauth = Oauth::findFirstByIdentificador($data->identificador);
                        if($oauth)
                        {
                            $tipo = $oauth->getOauthAutenticacao("autenticacao = 'codigo_autorizacao'");
                            return $tipo->count() !== 0;
                        }
                        return false;
                    },
                    "message" => "O identificador não é válido"
                ]
            )
        );

        $validator->add(
            'codigo_desafio',
            new Callback(
                [
                    "callback" => function($data) {
                        return is_object(OauthCodigoDesafio::findFirstByCodigo_desafio($data->codigo_desafio));
                    },
                    "message" => "O código de desafio não é válido"
                ]
            )
        );
        
        if(!$this->validate($validator))
            foreach($this->getMessages() as $e)
                throw new \Exception($e->getMessage());
    }

}
