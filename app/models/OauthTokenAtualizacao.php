<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Callback;

class OauthTokenAtualizacao extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $token_atualizacao;

    /**
     *
     * @var string
     */
    public $token_acesso;

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
        $this->setSource("oauth_token_atualizacao");
        $this->belongsTo('identificador', 'Oauth', 'identificador', ['alias' => 'Oauth']);
        $this->belongsTo('token_acesso', 'OauthTokenAcesso', 'token_acesso', ['alias' => 'OauthTokenAcesso']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_token_atualizacao';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthTokenAtualizacao[]|OauthTokenAtualizacao|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthTokenAtualizacao|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    
    public function beforeValidation()
    {
        $validator = new Validation();

        $this->token_atualizacao    = Oauth::generateUniqueIdentifier();

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
        
        if(!$this->validate($validator))
            foreach($this->getMessages() as $e)
                throw new \Exception($e->getMessage());
    }

}
