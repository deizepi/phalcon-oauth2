<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Callback;

class OauthTokenAcesso extends \Phalcon\Mvc\Model
{

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
        $this->setSource("oauth_token_acesso");
        $this->hasMany('token_acesso', 'OauthTokenAtualizacao', 'token_acesso', ['alias' => 'OauthTokenAtualizacao']);
        $this->belongsTo('identificador', 'Oauth', 'identificador', ['alias' => 'Oauth']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_token_acesso';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthTokenAcesso[]|OauthTokenAcesso|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthTokenAcesso|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    
    public function beforeValidation()
    {
        $validator = new Validation();

        $this->token_acesso = Oauth::generateUniqueIdentifier();

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
