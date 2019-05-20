<?php

class OauthAutenticacao extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $identificador;

    /**
     *
     * @var string
     */
    public $autenticacao;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("oauth_autenticacao");
        $this->belongsTo('identificador', 'Oauth', 'identificador', ['alias' => 'Oauth']);
        $this->belongsTo('autenticacao', 'Autenticacao', 'autenticacao', ['alias' => 'Autenticacao']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_autenticacao';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthAutenticacao[]|OauthAutenticacao|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthAutenticacao|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
