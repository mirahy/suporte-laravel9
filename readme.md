# Sistema de Suporte da EaD - UFGD
-------
## Instalação - Ambiente de Desenvolvimento 
### Instalação - Dependências 

Para a instalação, é necessário: 

1. [Docker Desktop](https://www.docker.com/products/docker-desktop/).

2. [Visual Studio Code](https://code.visualstudio.com/download).

3. Configuração de [WSL](https://docs.docker.com/desktop/windows/wsl/) (para SO Windows, uma arquitetura que melhora o desempenho do docker)

### Instalação - Código 

1. Clonar repositório 

	`git clone https://github.com/mirahy/suporte-up-version-laravel.git`

2. Entrar no diretório via terminal para executar comandos e abrir também no Visual Studio Code

### Instalação - Código - Para Windows, com WSL

1. Acessar o terminal linux instalado na configuração do seu WSL;

2. Entrar em algum diretório de preferência (`cd /home/user/projetos`, por exemplo) que deseja baixar os códicos-fonte;
    2.1. pode utilizar o comando `explorer.exe .` para abrir uma janela do explorer do windows na pasta em que está no terminal;

3. Clonar repositório:

	`git clone https://github.com/mirahy/suporte-up-version-laravel.git`

4. Entrar no diretório via terminal (`cd suporte-up-version-laravel`).
    4.1. pode utilizar o comando `code .` para abrir o Visual Studio Code no diretório em que está.

### Instalação - Infraestrutura

1. Copie o arquivo `docker-compose.yml.example` com o nome `docker-compose.yml`.

2. Edite o arquivo docker-compose.yml com as variáveis de ambiente que não estiverem pre-preenchidas ou edite alguma, se for necessário (as variáveis estão localizadas na sessão: `services` >> `host-laravel` >> `environment`)

3. Crie uma network e os volumes que os containers utilizarão:

    `docker network create minha-rede --subnet 172.18.0.0/16 --gateway=172.18.0.1`

    `docker volume create volume-mysql volume-host-laravel`

4. Executar o comando para subir a infraestrutura:

    `docker-compose up` 

5. No terminal do container "host-laravel", executar os seguintes comandos:

    `composer update`
    
    `chown www-data:www-data storage -R`

    `chmod g+w storage -R`

### Instalação - Banco de Dados

O banco de dados pode ser instalado com o script base localizado em `database/script.sql` ou então ser extraído de algum outro backup do banco do sistema.

Acesse algum cliente MySql para então realizar a instalação do banco de dados (configuração padrão: user: `root`, password: `secret`, database: `suporte`)

1. pode ser acessado pelo diretamente terminal do container MySql:
        
        mysql -p -D suporte
        use suporte
        <colar o conteúdo do script do banco de dados>

2. Ou pode ser acessado por algum outro cliente Mysql de sua preferência, como PhpMyAdmin, MySql Workbench, etc, por `localhost:3306`.

### Instalação - Acesso

O acesso pode ser realizado através do endereço: `http://localhost:8080/`, através de um Web browser.	

### Problemas comuns

#### Erro ao salvar arquivo no VS Code, sem permissão de escrita

Ocorre porque alguns arquivos são criados por algum container, com o usuário padrão da aplicação que o criou, por exemplo, arquivos do Angular com o usuário "node".

Resumidamente, o que precisa ser feito, a princípio, é definir o proprietário de todos os arquivos para "user", mas lembrando de retornar para "www-data" o diretório storage:

    `sudo chown user:user ./* -R`
    
    `sudo chown www-data:www-data storage -R`

Estes passos executados anteriormente resolveriam o problema de imediato, mas pode ser que para novos arquivos que forem criados após isso o problema persista, então duas algumas soluções podem ser adotadas. Ou você pode executar o comando de definir proprietários dos arquivos para "user" (mostrado acima), ou então, quando for executar comandos dentro de containers, utilizar o usuário da aplicação (por exemplo, no container "host-angular, antes de executar algum comando, utilizar `su node` para definir o usuário para "node" e no container "host-laravel" utilizar `su user` para definir o usuário para "user").


------

## Instalação - Ambiente de Produção 
	
------

### © Equipe EaD UFGD