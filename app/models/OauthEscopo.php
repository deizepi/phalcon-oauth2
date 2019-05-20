<?php

class OauthEscopo extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $identificador;

    /**
     *
     * @var integer
     */
    public $id_escopo;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("oauth_escopo");
        $this->belongsTo('identificador', 'Oauth', 'identificador', ['alias' => 'Oauth']);
        $this->belongsTo('escopo', 'Escopo', 'escopo', ['alias' => 'Escopo']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_escopo';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Oauthescopo[]|Oauthescopo|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Oauthescopo|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
