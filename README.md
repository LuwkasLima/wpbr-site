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
define( 'DB_NAME', 'wpbrasil' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
$table_prefix = 'wpbr_';

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

## Comandos ##

Comandos do Grunt para build e deploy.

#### Fazer build de todas as dependências (projeto, temas, plugins e etc) ####

```bash
grunt build
```

#### Atualizar as dependências ####

```bash
grunt update
```

#### Fazer deploy ####

```bash
grunt deploy
```

#### Limpar tudo ####

```bash
grunt clean
```
