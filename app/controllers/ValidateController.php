<?php

use \Firebase\JWT\JWT;

class ValidateController extends Phalcon\Mvc\Controller
{
    protected $token;
    protected $oauth;
    protected $params;
    protected $empresa;
    protected $cliente;
    protected $estabelecimento;
    protected $profissional;

    public function initialize()
    {
        try{
            $jwt = str_replace("Bearer ", "", $this->request->getHeader('AUTHORIZATION'));
            $decode = JWT::decode($jwt, file_get_contents($this->config->oauth->public_key), array('RS256'));
        } catch(\Firebase\JWT\BeforeValidException $e){
            return $this->response->sendError("nbf_invalido", "O token de acesso não é válido");
        } catch(\Firebase\JWT\ExpiredException $e){
            return $this->response->sendError("exp_invalido", "O token de acesso não é válido");
        } catch(\Firebase\JWT\SignatureInvalidException $e){
            return $this->response->sendError("token_invalido", "O formato do token de acesso não é válido");
        } catch(Exception $e){
            return $this->response->sendError("parametro_invalido", $e->getMessage());
        }

        $this->token = $decode->jti;
        $token_acesso = OauthTokenAcesso::findFirst([
            "token_acesso = {token:str} AND ativo = 1 AND expiracao > NOW()",
            "bind" => [
                "token" => $this->token
            ]
        ]);
        if($token_acesso)
        {
            $this->oauth    = $token_acesso->getOauth();
            $escopo = $this->oauth->getOauthEscopo([
                "escopo IN({escopo:array})",
                "bind" => [
                    "escopo" => $this->session->get("scopes")
                ]
            ]);
            if($escopo->count() === 0 && !$this->escopoPadrao())
            {
                return $this->response->sendError("escopo_invalido", "O escopo de acesso não é válido");
            }

            $oauth = $token_acesso->getOauth();
            if($oauth)
            {
                $this->empresa = $oauth->getEmpresa();
            }

            $oauth_profissional = $token_acesso->getOauthProfissional();
            if($oauth_profissional->count() !== 0)
            {
                $this->profissional = $oauth_profissional->getFirst()->getProfissional();
                $this->empresa      = $this->profissional->getEstabelecimento()->getEmpresa();
            }
            
            $oauth_cliente = $token_acesso->getOauthCliente();
            if($oauth_cliente->count() !== 0)
            {
                $this->cliente = $oauth_cliente->getFirst()->getCliente();
                $this->empresa = $this->cliente->getEmpresa();
                $this->estabelecimento = $oauth_cliente->getFirst()->getEstabelecimento();
            }

            if(isset($this->empresa))
            {
                $this->session->set("id_empresa", $this->empresa->id_empresa);
            }

            if(!isset($this->empresa) && !$this->escopoPadrao())
            {
                return $this->response->sendError("acesso_invalido", "Você precisa estar logado para acessar esse recurso");
            }

            $this->params = $this->request->getJsonRawBody();
        }
        else 
        {
            return $this->response->sendError("token_invalido", "O token de acesso não é válido");
        }
    }

    private function escopoPadrao()
    {
        return Escopo::find([
            "escopo IN({escopo:array}) AND padrao = 1",
            "bind" => [
                "escopo" => $this->session->get("scopes")
            ]
        ])->count() !== 0;
    }
}
