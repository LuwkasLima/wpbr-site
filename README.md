# Comunidade WordPress Website #

Repositório com dependências do projeto e scripts de build e deploy.

## Requerimentos ##

* [Composer](http://getcomposer.org/doc/00-intro.md)
* [NodeJS](http://nodejs.org/download/)
* [GruntJS](http://gruntjs.com/getting-started)

## Ambientes ##

Esta pré-configurado um arquivo `dev-config.php` para as configurações do ambiente de desenvolvimento.  
É possível criar um arquivo `prod-config.php` para o ambiente de produção que irá sobrescrever sozinho as configurações do arquivo de desenvolvinento.

Exemplo de um arquivo `prod-config.php`:

```php
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

Configurar vagrant para usar o endereço `192.168.56.101` e criar um alias no `/etc/hosts` como:

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

#### Fazer build ####

Faz build de todas as dependências (projeto, temas, plugins e etc).

```bash
grunt build
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
