<?php

use Defuse\Crypto\Crypto;

class AuthorizeController extends Phalcon\Mvc\Controller
{
    public function codeAction()
    {
        try
        {
            if($this->request->isGet())
            {
                $oauth_desafio = new OauthCodigoDesafio();
                if($oauth_desafio->save($_GET))
                {
                    $oauth_codigo = new OauthCodigoAutorizacao();
                    if(
                        $oauth_codigo->save([ 
                            "identificador"     => $oauth_desafio->identificador,
                            "codigo_desafio"    => $oauth_desafio->codigo_desafio,
                            "expiracao"         => $this->config->oauth->codigo_autorizacao_expiracao
                        ])
                    )
                    {
                        $payload = json_encode([
                            'identificador'         => $oauth_codigo->identificador,
                            'codigo_autorizacao'    => $oauth_codigo->codigo_autorizacao,
                            'expiracao'             => $oauth_codigo->expiracao,
                            'codigo_desafio'        => $oauth_desafio->codigo_desafio,
                            'metodo_desafio'        => $oauth_desafio->metodo_desafio,
                        ]);

                        try {
                            $codigo = Crypto::encryptWithPassword($payload, $this->config->oauth->crypto_key);
                        } catch(Defuse\Crypto\Exception\EnvironmentIsBrokenException $e){
                            return $this->response->sendError("servidor_invalido", "O servidor não consegue validar o código");
                        } catch(\TypeError $e){
                            return $this->response->sendError("tipo_invalido", "A requisição não possui o formato esperado");
                        }

                        return $this->response->sendSuccess([
                            "codigo" => $codigo
                        ]);
                    }
                    else 
                    {
                        return $this->response->sendError("codigo_autorizacao", "Não foi possível registrar o código de autorização");
                    }
                }
                else 
                {
                    return $this->response->sendError("codigo_desafio", "Não foi possível registrar o código do desafio");
                }
            }
            else 
            {
                return $this->response->sendError("requisicao_invalida", "Método de requisição inválido");
            }
        }
        catch(Exception $e)
        {
            return $this->response->sendError("oauth_exception", $e->getMessage());
        }
    }
}
