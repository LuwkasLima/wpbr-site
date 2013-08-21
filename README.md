Esta é uma cópia da produção do [site da Comunidade
WP-Brasil](http://wp-brasil.org), que é uma instalação WordPress -- obviamente.

### Ambiente de desenvolvimento

Como o WP-Brasil é um multisite, você deve configurar uma localidade raiz
`http://wpbrasildev` no seu servidor de desenvolvimento para reproduzí-lo
adequadamente. Se você usar subdiretórios do tipo `http://localhost/wp-brasil`
o banco de dados aqui fornecido não irá funcionar.

Estes são os passos para reproduzir o ambiente:

1. Copie o repositório para uma localidade raiz do seu servidor web.

    git clone --recursive git@github.com/WP-Brasil/WP-Brasil.git .

2. Faça download do [banco de dados de
   desenvolvimento](http://wp-brasil.org/dev/devdump.tar.bz2), descompacte e
   importe no seu servidor MySQL.

3. Copie o arquivo `wp-config-dev.php` para `wp-config.php` e insira as
   credenciais de conexão neste banco de dados.

4. Abra o seu arquivo de hosts no Linux (`/etc/hosts`) ou no Windows
   (`C:\Windows\system32\etc\hosts`) e adicione a seguinte linha:

    127.0.0.1 wpbrasildev participe.wpbrasildev oportunidades.wpbrasildev

5. Acesse o projeto em `http://wpbrasildev`.

### Submódulos

Cada subprojeto que é utilizado neste site está presente como um submódulo
também presente na conta do Github da comunidade. Você pode contribuir
diretamente com estes projetos através de seus repositórios, mas é altamente
recomendável que você os teste aqui com este ambiente antes de enviar os seus
_pull requests_, já que esta é a forma como validaremos as proposições.

### Ideias e propostas

As discussões sobre implementações e ideias para este site são feitas no
[http://participe.wp-brasil.org](http://participe.wp-brasil.org). Todas as
contribuições são bem-vindas. Dê um alô por lá!

Bugs e tarefas específicas são reportadas e monitoradas pelos
[tickets](/tickets) do Github.
