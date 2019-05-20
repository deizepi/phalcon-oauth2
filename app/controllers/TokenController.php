<?php

use \Firebase\JWT\JWT;
use Defuse\Crypto\Crypto;

class TokenController extends Phalcon\Mvc\Controller
{
    private $oauth;
    private $token_acesso;
    private $token_atualizacao;

    public function indexAction()
    {
        try 
        {
            if($this->request->isPost())
            {
                $tipo_autenticacao  = $this->request->getPost("tipo_autenticacao");
                $identificador      = $this->request->getPost("identificador");
                $this->oauth = Oauth::findFirst([
                    "identificador = {identificador:str} AND expiracao > NOW()",
                    "bind" => [
                        "identificador" => $identificador
                    ]
                ]);
                if(!$this->oauth)
                {
                    return $this->response->sendError("identificador_invalido", "O identificador não é válido");
                }

                if(
                    $this->oauth->getOauthAutenticacao([
                        "autenticacao = {tipo_autenticacao:str}",
                        "bind" => [
                            "tipo_autenticacao" => $tipo_autenticacao
                        ]
                    ])->count() === 0
                ){
                    return $this->response->sendError("identificador_invalido", "O identificador não é válido");
                }

                if($this->oauth->chave_secreta != $this->request->getPost("chave_secreta"))
                {
                    return $this->response->sendError("identificador_invalido", "O identificador não é válido");
                }
                
                //***** validate domains *****
                // $dominios = explode(",", $this->oauth->dominios);
                // if(!in_array($this->request->getHeader('HOST'), $dominios))
                // {
                //     return $this->response->sendError("requisicao_invalida", "A origem da requisição não é válida");
                // }

                switch($tipo_autenticacao)
                {
                    case 'senha':
                        return $this->senha();
                        break;
                    
                    case 'cliente_credenciado':
                        return $this->clienteCredenciado();
                        break;
                    
                    case 'codigo_autorizacao':
                        return $this->codigoAutorizacao();
                        break;
                    
                    case 'token_atualizacao':
                        return $this->tokenAtualizacao();
                        break;

                    default:
                        return $this->response->sendError("parametros_invalidos", "Um parâmetro obrigatório não foi fornecido");
                        break;
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

    private function senha()
    {
        // validate password
        if($this->request->getPost("email") == "email@example.com" && $this->request->getPost("senha") == "123456")
        {
            $data = $this->generateTokens();

            return $this->response->sendSuccess($data);
        }
        else 
        {
            return $this->response->sendError("senha_invalida", "A senha não é válida");
        }
    }

    private function clienteCredenciado()
    {
        return $this->response->sendSuccess($this->generateTokens());
    }

    private function codigoAutorizacao()
    {
        try {
            $payload = json_decode(
                Crypto::decryptWithPassword($this->request->getPost("codigo"), $this->config->oauth->crypto_key)
            );
        } catch(Defuse\Crypto\Exception\EnvironmentIsBrokenException $e){
            return $this->response->sendError("servidor_invalido", "O servidor não consegue validar o código");
        } catch(Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $e){
            return $this->response->sendError("chave_invalida", "A chave para descriptografar o código não é válida");
        } catch(\TypeError $e){
            return $this->response->sendError("tipo_invalido", "O código não possui o formato esperado");
        }

        $codigo_autorizacao = OauthCodigoAutorizacao::findFirst([
            "codigo_autorizacao = {codigo:str} AND ativo = 1 AND expiracao > NOW()",
            "bind" => [
                "codigo" => $payload->codigo_autorizacao
            ]
        ]);
        if($codigo_autorizacao)
        {
            $codigo_verificador = $this->request->getPost("codigo_verificador");
            if($codigo_autorizacao->getOauthCodigoDesafio()->checkChallenge($codigo_verificador))
            {
                $codigo_autorizacao->ativo = 0;
                if(!$codigo_autorizacao->update())
                {
                    return $this->response->sendError("codigo_autorizacao", "Não foi possível atualizar o código de autorização");
                }

                return $this->response->sendSuccess($this->generateTokens());
            }
            else 
            {
                return $this->response->sendError("verificador_invalido", "O código de verificação não é válido");
            }
        } 
        else 
        {
            return $this->response->sendError("codigo_invalido", "O código de autorização não é válido");
        }
    }

    private function tokenAtualizacao()
    {
        try{
            $decode = JWT::decode($this->request->getPost("token_atualizacao"), file_get_contents($this->config->oauth->public_key), array('RS256'));
        } catch(\Firebase\JWT\BeforeValidException $e){
            return $this->response->sendError("nbf_invalido", "O token de acesso não é válido");
        } catch(\Firebase\JWT\ExpiredException $e){
            return $this->response->sendError("exp_invalido", "O token de acesso não é válido");
        } catch(\Firebase\JWT\SignatureInvalidException $e){
            return $this->response->sendError("token_invalido", "O formato do token de atualização não é válido");
        }

        $token_atualizacao = OauthTokenAtualizacao::findFirst([
            "token_atualizacao = {token:str} AND ativo = 1 AND expiracao > NOW()",
            "bind" => [
                "token" => $decode->jti
            ]
        ]);
        if($token_atualizacao)
        {
            $token_atualizacao->ativo = 0;
            if(!$token_atualizacao->update())
            {
                return $this->response->sendError("token_atualizacao", "Não foi possível atualizar o token de atualização");
            }

            $data = $this->generateTokens();

            $token_acesso = $token_atualizacao->getOauthTokenAcesso();

            // save status of token

            return $this->response->sendSuccess($data);
        }
        else 
        {
            return $this->response->sendError("token_invalido", "O token de atualização não é válido");
        }
    }

    private function generateTokens()
    {
        $token_acesso = new OauthTokenAcesso();
        $token_acesso->save([ 
            "identificador" => $this->oauth->identificador,
            "expiracao"     => $this->config->oauth->token_acesso_expiracao
        ]);

        $this->token_acesso = $token_acesso->token_acesso;
        
        $payload = [
            "aud" => $this->oauth->identificador,
            "exp" => strtotime($token_acesso->expiracao),
            "iat" => time(),
            "nbf" => time(),
            "jti" => $this->token_acesso
        ];

        $data = [
            "token_acesso"   => JWT::encode($payload, file_get_contents($this->config->oauth->private_key), 'RS256'),
            "expiracao"      => strtotime($token_acesso->expiracao)
        ];

        if($this->oauth->getOauthAutenticacao("autenticacao = 'token_atualizacao'")->count() !== 0)
        {
            $token_atualizacao = new OauthTokenAtualizacao();
            $token_atualizacao->save([ 
                "identificador" => $this->oauth->identificador,
                "token_acesso"  => $this->token_acesso,
                "expiracao"     => $this->config->oauth->token_atualizacao_expiracao
            ]);

            $this->token_atualizacao = $token_atualizacao->token_atualizacao;
            
            $payload2 = [
                "aud" => $this->oauth->identificador,
                "exp" => strtotime($token_atualizacao->expiracao),
                "iat" => time(),
                "nbf" => time(),
                "jti" => $this->token_atualizacao
            ];

            $data["token_atualizacao"] = JWT::encode($payload2, file_get_contents($this->config->oauth->private_key), 'RS256');
        }

        return $data;
    }
}
