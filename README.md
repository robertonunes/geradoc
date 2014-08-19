GeraDoc - Sistema Gerenciador de Documentos
===========================================

![Alt text](/screenshots/login.png?raw=true "Login")

Requisitos:
===================================

1. Apache 2.0.63
2. PHP 5.3.2
3. MySQL Server 5.1.44


MySQL:
===================================

1. Crie o banco de Dados <strong>geradoc</strong> e importe os dados do arquivo: geradoc/docs/geradoc.sql
2. Crie um usuário "geradoc" com permissões para criar e ler dados no banco criado.


Configuração da aplicação:
===================================

1. Copie a pasta <strong>geradoc</strong> para a pasta <strong>htdocs</strong> do Aapache

2. Altere os dados dos seguintes arquivos:

	a. geradoc/system/application/config/config.php
	
		$config['base_url']	= "http://localhost/geradoc/"; // colocar a url de seu servidor.

	b. geradoc/system/application/config/database.php

		$db['default']['hostname'] = "host"; 		// substituir pelo seu servidor
		$db['default']['username'] = "usuario";		// substituir pelo usuário do servidor
		$db['default']['password'] = "senha";		// substituir pela senha do usuário do servidor
	
3. Dê permissões de <strong>leitura e escrita</strong> para o servidor web nas pasta abaixo:

		geradoc/files

		geradoc/temp


Acesso ao sistema:
===================================

Acesse [http://localhost/geradoc](http://localhost/geradoc "http://localhost/geradoc") e informe os dados abaixo:

* Login: 11111111111  
* Senha: admin  


Observação:
===================================
 
Favor manter os créditos nos programas que compõem o sistema.

Em caso de dúvidas, envie e-mail para tarsodecastro@gmail.com

