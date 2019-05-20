<?php

class NaoEncontradoController extends Phalcon\Mvc\Controller
{
    public function indexAction()
    {
        if($this->request->isOptions())
        {
            return $this->response->sendSuccess([]);
        }
        else 
        {
            return $this->response->sendError("nao_encontrado", "Recurso não encontrado");
        }
    }
}
