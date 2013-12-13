# Website da Comunidade WordPress Brasil #

Este repositório contém as dependências e scripts para build e deploy do site
da Comunidade WordPress Brasil. O seguinte roteiro tem como objetivo mostrar
como instalar as ferramentas necessários e montar o seu próprio ambiente de
desenvolvimento.

## Requerimentos ##

* [Composer](http://getcomposer.org/doc/00-intro.md)
* [NodeJS](http://nodejs.org/download/)
* [GruntJS](http://gruntjs.com/getting-started)

## Montando o seu ambiente ##

O seguinte roteiro é para ambientes Linux. Se você desenvolve em Windows pode
nos ajudar a gerar uma outra parte desta documentação com os mesmos
procedimentos.

**1-** Vá para a pasta do seu servidor Web local e clone este repositório do Github.

```bash
git clone git@github.com:wpbrasil/wpbr-site.git
cd wpbr-site
```

**2-** Instale as ferramentas necessárias (caso você já possua elas instaladas
pode pular para o passo 3).

```bash
sudo apt-get install nodejs        # Ubuntu
sudo apt-get install nodejs-legacy # Debian
npm install -g grunt-cli
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

**3-** Agora você já pode baixar as dependências do Grunt no projeto:

```bash
npm install
```

Esse comando vai ler o arquivo `package.json` e instalar 

**4-** Agora podemos baixar os temas e plugins que compõem o site.

```bash
grunt source
```

Isto criará uma pasta `wp` para o WordPress e outra `wp-content` para os
plugins e temas utilizados. Veja que a pasta `wp/wp-content` não é utilizada
neste projeto, e só está lá porque é distribuída juntamente com o WordPress.

Agora você já deve conseguir acessar o `http://localhost` no seu navegador e
fazer a instalação do WordPress ajustando as informaçẽs de banco de dados e
tudo mais. É recomendado que você siga as credenciais em `dev-config.php` para
que não seja necessário modificar este arquivo, caso contrário você pode acabar
fazendo alguma modificação nele por engano.

## Configurando para a produção ##

O arquivo `dev-config.php` contém as pré-configurações do ambiente de
desenvolvimento. É possível criar um arquivo `prod-config.php` para o ambiente
de produção que irá sobrescrever sozinho as configurações do arquivo de
desenvolvinento.

Exemplo de um arquivo `prod-config.php`:

```php
<?php
/**
 * Development environment configuration.
 */

// Database.
define( 'DB_NAME', 'database' );
define( 'DB_USER', 'user' );
define( 'DB_PASSWORD', 'pass' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
$table_prefix = 'prefix_';

// Salts (https://api.wordpress.org/secret-key/1.1/salt).
define( 'AUTH_KEY', 'auth_key' );
define( 'SECURE_AUTH_KEY', 'secure_uth_key' );
define( 'LOGGED_IN_KEY', 'logged_in_key' );
define( 'NONCE_KEY', 'nonce_key' );
define( 'AUTH_SALT', 'auth_salt' );
define( 'SECURE_AUTH_SALT', 'secure_auth_salt' );
define( 'LOGGED_IN_SALT', 'logged_in_salt' );
define( 'NONCE_SALT', 'nonce_salt' );

// Disallow file edit.
define( 'DISALLOW_FILE_MODS', true );
define( 'DISALLOW_FILE_EDIT', true );

// Debug.
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );
```

#### Ambiente de Desenvolvimento ####

TODO: depois passo o vagrant que usei.

Configurar vagrant para usar o endereço `192.168.56.101` e criar um alias no
`/etc/hosts` como:

```
192.168.56.101 wp-brasil.org
```

Configurar usuário de SSH em `~/.ssh/config`:

```
Host 192.168.56.101
	User vagrant
```

#### Ambiente de Produção ####

Configurar usuário e chave de SSH em `~/.ssh/config`:

```
Host wp-brasil.org
	IdentityFile ~/.ssh/SUA_CHAVE_rsa
	User USUÁRIO_DO_SERVIDOR
```

## Comandos ##

Comandos do Grunt para build e deploy.

#### Fazer build para produção ####

Faz build de todas as dependências (projeto, temas, plugins e etc).

```bash
grunt build
```

#### Fazer build para desenvolvimento ####

Faz build de todas as dependências (projeto, temas, plugins e etc)
incluíndo dependências de desenvolvimento e clonando os subprojetos
para que seja possível fazer alterações e commits.

```bash
grunt source
```

#### Atualizar as dependências ####

Faz update das dependências do build.

```bash
grunt update
```

#### Fazer deploy ####

Faz update e deploy rsync no ambiente de produção.

```bash
grunt deploy
```

#### RSYNC no ambiente de produção ####

```bash
grunt rsync:prod
```

#### RSYNC no ambiente de desenvolvimento ####

```bash
grunt rsync:dev
```

#### Limpar tudo ####

```bash
grunt clean
```

## Referências ##

* [Simplify Your Life With an SSH Config File](http://nerderati.com/2011/03/simplify-your-life-with-an-ssh-config-file/)
* [WordPress local dev tips: DB & plugins](http://markjaquith.wordpress.com/2011/06/24/wordpress-local-dev-tips/)
* [Best practice for versioning wp-config.php?](http://wordpress.stackexchange.com/questions/52682/best-practice-for-versioning-wp-config-php#answer-53014)
* [Configure the WordPress Git Repository]()
* [Your Guide to Composer in WordPress](http://composer.rarst.net/)
* [WordPress Packagist](http://wpackagist.org/)
* [Composer-WordPress-Skeleton](https://github.com/ADARTA/Composer-Wordpress-Skeleton)
* [wp-composer](https://github.com/bbrothers/wp-composer)
* [Setting Up Grunt For WordPress](http://tommcfarlin.com/setting-up-grunt-for-wordpress/)
