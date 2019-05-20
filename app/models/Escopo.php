<?php

class Escopo extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $escopo;

    /**
     *
     * @var integer
     */
    public $padrao;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("escopo");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'escopo';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Escopo[]|Escopo|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Escopo|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
