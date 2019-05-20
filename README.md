# phalcon-oauth2

Base de OAuth2 para Phalcon, baseado no [thephpleague/oauth2-server](https://github.com/thephpleague/oauth2-server "thephpleague/oauth2-server").

### Funcionalidades

- Código de autorização (PKCE);
- Cliente credenciado;
- Atualização de token;
- Login com senha;

###Demonstração de uso

####Código de autorização

Envie um `GET` para `/authorize/code` com as seguintes informações:

| Chave      | Valor | Descrição |
| --------- | ----- |-------|
| identificador  | oauth.identificador | Identificador da chave do cliente |
| metodo_desafio     | S256 | Método de desafio utilizado para a verificação do cliente |
| codigo_desafio      | string{43,128} | Código gerado a partir do código de verificação e o método de desafio |
    
A API irá retornar um código, que será utilizado no passo seguinte, enviando um `POST` para `/token`:

| Chave      | Valor | Descrição |
| --------- | ----- |-------|
| tipo_autenticacao  | `codigo_autorizacao` | Tipo de autenticação a ser utilizado |
| identificador     | oauth.identificador | Identificador da chave do cliente |
| chave_secreta      | oauth.chave_secreta | Chave secreta do cliente |
| codigo      | data.codigo | Código obtido em /authorize/code |
| codigo_verificador      | string{43,128} | Código randômico e único a partir do qual foi gerado o codigo_desafio |

####Cliente credenciado

Envie um `POST` para `/token` com as seguintes informações:

| Chave      | Valor | Descrição |
| --------- | ----- |-------|
| identificador  | oauth.identificador | Identificador da chave do cliente |
| chave_secreta | oauth.chave_secreta | Chave secreta do cliente |
| tipo_autenticacao | `cliente_credenciado` | Deve ser utilizado apenas a partir de servidores confiáveis |

####Senha

Envie um `POST` para `/token` com as seguintes informações:

| Chave      | Valor | Descrição |
| --------- | ----- |-------|
| identificador  | oauth.identificador | Identificador da chave do cliente |
| chave_secreta | oauth.chave_secreta | Chave secreta do cliente |
| tipo_autenticacao | `senha` | O acesso é concedido a partir de um login |
| email | usuario.email | E-mail ou informação de acesso |
| senha | email.senha | Senha correspondente ao email/login |

####Token de atualização

Envie um `POST` para `/token` com as seguintes informações:

| Chave      | Valor | Descrição |
| --------- | ----- |-------|
| identificador  | oauth.identificador | Identificador da chave do cliente |
| chave_secreta | oauth.chave_secreta | Chave secreta do cliente |
| tipo_autenticacao | `token_atualizacao` | Após a expiração do token obtido, é possível gerar um novo token |
| token_atualizacao | JWT | Token de atualização obtido na geração do token de acesso |

### Informações importantes

- As permissões de acesso da chave devem ser muito bem distribuídas (de modo que não seja possível obter um "cliente_credenciado" e um "codigo_autorizacao" com a mesma chave, pois isso tiraria a segurança do código de autorização);
- As chave em app/config/config.php devem ser modificadas para chaves geradas;
- Em alguns casos (como em "cliente_credenciado") deve-se validar o servidor de origem;
- Você deve adicionar suas próprias regras ao token de acesso (por exemplo, se o seu sistema permite dois acessos distintos, você pode criar duas tabelas para cada um desses relacionamentos e então popular essas tabelas de acordo com o token obtido; se há apenas um tipo de usuário para acessar o sistema, você pode incluir o id do usuário na tabela oauth_token_acesso e informar o id quando esse estiver disponível);

###End