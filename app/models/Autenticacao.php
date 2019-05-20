<?php

class Autenticacao extends \Phalcon\Mvc\Model
{

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
        $this->setSource("autenticacao");
        $this->hasMany('autenticacao', 'OauthAutenticacao', 'autenticacao', ['alias' => 'OauthAutenticacao']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'autenticacao';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Autenticacao[]|Autenticacao|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Autenticacao|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
