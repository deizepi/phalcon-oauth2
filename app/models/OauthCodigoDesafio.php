<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\Callback;
use Phalcon\Validation\Validator\Uniqueness;

class OauthCodigoDesafio extends \Phalcon\Mvc\Model
{

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
    public $metodo_desafio;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("oauth_codigo_desafio");
        $this->belongsTo('identificador', 'Oauth', 'identificador', ['alias' => 'Oauth']);
        $this->hasMany('codigo_desafio', 'OauthCodigoAutorizacao', 'codigo_desafio', ['alias' => 'OauthCodigoAutorizacao']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_codigo_desafio';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthCodigoDesafio[]|OauthCodigoDesafio|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthCodigoDesafio|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function checkChallenge($codigo_verificador)
    {
        return hash_equals(
            OauthCodigoDesafio::S256($codigo_verificador),
            $this->codigo_desafio
        ) !== false;
    }

    public static function S256($codigo_verificador)
    {
        return strtr(rtrim(base64_encode(hash('sha256', $codigo_verificador, true)), '='), '+/', '-_');
    }
    
    public function beforeValidation()
    {
        $validator = new Validation();

        $validator->add(
            "codigo_desafio",
            new Uniqueness(
                [
                    "model"   => new OauthCodigoDesafio(),
                    "message" => ":field precisa ser único",
                ]
            )
        );

        $validator->add(
            'codigo_desafio',
            new Regex(
                [
                    'pattern' => "/^[A-Za-z0-9-._~]{43,128}$/",
                    'message' => "O código do desafio não é válido",
                ]
            )
        );

        $validator->add(
            'identificador',
            new Callback(
                [
                    "callback" => function($data) {
                        return is_object(Oauth::findFirstByIdentificador($data->identificador));
                    },
                    "message" => "O identificador não é válido"
                ]
            )
        );

        $validator->add(
            "metodo_desafio",
            new InclusionIn(
                [
                    "domain"  => ["S256"],
                    "message" => "O método do desafio não é válido",
                ]
            )
        );
        
        if(!$this->validate($validator))
            foreach($this->getMessages() as $e)
                throw new \Exception($e->getMessage());
    }

}
