GeraDoc - Sistema Gerenciador de Documentos
===========================================


Descrição:
===================================

O GeraDoc é um sistema de Gerenciamento de Conteúdo Corporativo (ECM - Enterprise Content Management).

Ele foi desenvolvido para facilitar a criação de documentos oficiais padronizados, como ofícios, comunicações internas, despachos, pareceres técnicos, pareceres jurídicos, atos administrativos e notas de instrução nos diversos setores de instituições governamentais, mantendo as formas, controlando numerações, preservando as informações, controlando acessos e permissões, bem como possibilitando pesquisas textuais nos conteúdos dos documentos produzidos.


Benefícios:
===================================

1. <strong>Padronização dos formatos</strong> dos documentos, como cabeçalhos, rodapés, posicionamentos de destinatários, assuntos, datas, referências, assinaturas, famílias e tamanhos de fontes;

2. <strong>Controle da numeração</strong> dos documentos produzidos em cada setor;

3. <strong>Maior praticidade</strong>, uma vez que os documentos são armazenados em nuvem e o usuário pode gerenciá-los a partir de qualquer computador conectado à internet ou rede interna;

4. <strong>Controle de acesso e de alteração</strong>;

5. <strong>Agilidade na obtenção das informações</strong> através das consultas textuais no universo de documentos produzidos nos setores de um mesmo órgão;

6. <strong>Facilidade de acompanhamento dos trabalhos</strong> desenvolvidos nos setores, através da visualização dos documentos gerados;

7. <strong>Rapidez na elaboração de um documento</strong>, uma vez que o usuário não se preocupa com as características da forma do tipo de documento, focando sua atenção e criatividade no conteúdo;

8. <strong>Possibilidade de colaboração</strong> entre os membros de um mesmo setor durante a criação de um documento antes de imprimi-lo;

9. <strong>Armazenamento seguro</strong>, uma vez que os registros são gravados em nuvem, com rotinas de backup, e não nos computadores dos usuários.


Requisitos:
===================================

1. Apache 2.0.63
2. PHP 5.3.2
3. MySQL Server 5.1.44


MySQL:
===================================

1. Crie a base de dados <strong>geradoc</strong> e importe o arquivo: <strong>geradoc/docs/geradoc.sql</strong>
2. Crie um usuário <strong>geradoc</strong> com permissões para <strong>criar e ler</strong> dados no banco criado.


Configuração da aplicação:
===================================

1. Copie a pasta <strong>geradoc</strong> para a pasta <strong>htdocs</strong> do Apache

2. Altere os dados do seguinte arquivo:

	a. geradoc/application/config/config.php
	
		$config['base_url']	= "http://localhost/geradoc/"; // colocar a url de seu servidor.

3. Na pasta <strong>geradoc/application/config/</strong> crie um arquivo com o nome <strong>database.php</strong> com o seguinte conteúdo:
		
		<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
		$active_group = 'default';
		$active_record = TRUE;

		$db['default']['hostname'] = 'localhost';
		$db['default']['username'] = ''; //informe o usuário do banco
		$db['default']['password'] = ''; //informe a senha do usuário do banco
		$db['default']['database'] = 'geradoc';
		$db['default']['dbdriver'] = 'mysql';
		$db['default']['dbprefix'] = '';
		$db['default']['pconnect'] = TRUE;
		$db['default']['db_debug'] = TRUE;
		$db['default']['cache_on'] = FALSE;
		$db['default']['cachedir'] = '';
		$db['default']['char_set'] = 'utf8';
		$db['default']['dbcollat'] = 'utf8_unicode_ci';
		$db['default']['swap_pre'] = '';
		$db['default']['autoinit'] = TRUE;
		$db['default']['stricton'] = FALSE;

4. Ainda na pasta <strong>geradoc/application/config/</strong> crie um arquivo com o nome <strong>email.php</strong> com o seguinte conteúdo:

		<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

		//Para envio de e-mail usando uma conta g-mail com TLS

		$config['protocol']='smtp';
		$config['smtp_host']='smtp.gmail.com';
		$config['smtp_crypto'] = 'tls';
		$config['smtp_port']= 587;
		$config['starttls'] = TRUE;
		$config['validate']= TRUE;
		$config['smtp_user']='';
		$config['smtp_pass']='';
		$config['mailtype']='html';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = 'TRUE';
		$config['newline']="\r\n"; 
	
5. Dê permissões de <strong>leitura e escrita</strong> para o servidor web nas pastas abaixo:

		geradoc/files

		geradoc/temp




Acesso ao sistema:
===================================

Acesse [http://localhost/geradoc](http://localhost/geradoc "http://localhost/geradoc") e informe os dados abaixo:

* E-mail: admin@geradox.com.br  
* Senha: admin  


Observação:
===================================
 
Favor manter os créditos nos códigos que compõem o sistema.

Em caso de dúvidas, envie e-mail para tarsodecastro@gmail.com


Demonstração:
===================================

Para ver uma demonstração do sistema pronto acesse [http://www.geradox.com.br](http://www.geradox.com.br "http://www.geradox.com.br").




