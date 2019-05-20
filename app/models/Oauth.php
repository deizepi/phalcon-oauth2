<?php

class Oauth extends \Phalcon\Mvc\Model
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
    public $chave_secreta;

    /**
     *
     * @var string
     */
    public $expiracao;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("oauth");
        $this->hasMany('identificador', 'OauthAutenticacao', 'identificador', ['alias' => 'OauthAutenticacao']);
        $this->hasMany('identificador', 'OauthCodigoAutorizacao', 'identificador', ['alias' => 'OauthCodigoAutorizacao']);
        $this->hasMany('identificador', 'OauthTokenAcesso', 'identificador', ['alias' => 'OauthTokenAcesso']);
        $this->hasMany('identificador', 'OauthTokenAtualizacao', 'identificador', ['alias' => 'OauthTokenAtualizacao']);
        $this->hasMany('identificador', 'OauthEscopo', 'identificador', ['alias' => 'OauthEscopo']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Oauth[]|Oauth|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Oauth|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    
    /**
     * Generate a new unique identifier.
     *
     * @param int $length
     *
     * @throws OAuthServerException
     *
     * @return string
     */
    public static function generateUniqueIdentifier($length = 40)
    {
        try {
            return bin2hex(random_bytes($length));
            // @codeCoverageIgnoreStart
        } catch (TypeError $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred', $e);
        } catch (Error $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred', $e);
        } catch (Exception $e) {
            // If you get this message, the CSPRNG failed hard.
            throw OAuthServerException::serverError('Could not generate a random string', $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Generates an unique auth code.
     *
     * Implementing classes may want to override this function to implement
     * other auth code generation schemes.
     *
     * @return
     * An unique auth code.
     *
     * @ingroup oauth2_section_4
     */
    public static function generateAuthorizationCode()
    {
        $tokenLen = 40;
        if (function_exists('random_bytes')) {
            $randomData = random_bytes(100);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $randomData = openssl_random_pseudo_bytes(100);
        } elseif (function_exists('mcrypt_create_iv')) {
            $randomData = mcrypt_create_iv(100, MCRYPT_DEV_URANDOM);
        } elseif (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 100) . uniqid(mt_rand(), true);
        } else {
            $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
        }

        return substr(hash('sha512', $randomData), 0, $tokenLen);
    }

}
