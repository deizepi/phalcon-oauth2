<?php

class OauthDominio extends \Phalcon\Mvc\Model
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
    public $dominio;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("oauth_dominio");
        $this->belongsTo('identificador', 'Oauth', 'identificador', ['alias' => 'Oauth']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_dominio';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthDominio[]|OauthDominio|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return OauthDominio|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
