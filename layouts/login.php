<?php 
	$CI =& get_instance();
	$CI->load->library('datas');
	$today = $CI->datas->getMinDateExtenso(); 	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="pt-br" />
	<meta name="author" content="Tarso de Castro">
	<meta name="reply-to" content="tarsodecastro@gmail.com">
	<meta name="revised" content="Tarso de Castro, 12/09/2013" />
	<meta name="description" content="GeraDoc - Sistema desenvolvido para facilitar a criação de documentos oficiais padronizados nos 45 setores da Academia Estadual de Segurança Pública do Estado do Ceará.">
	<meta name="abstract" content="GeraDoc - AESP-CE.">
	<meta name="keywords" content="aluno on-line, fale conosco, aesp, geradoc, documento, oficio, comunicacao interna, memorando, despacho, portaria, php, software livre, corpo de bombeiros">
	<meta name="ROBOT" content="Index,Follow">

	<link rel="shortcut icon" href="{TPL_images}<?php echo $CI->config->item('orgao');?>.ico" type="image/x-icon" />
	<link rel="icon" href="{TPL_images}<?php echo $CI->config->item('orgao');?>.ico" />	

	<title><?php echo $CI->config->item('title');?></title>
	{TPL_css}
    {TPL_js}
</head>
<body>
	<div id="geral"> 
		<div id="topo">			
				<div id="topo_left"></div>				
				<div id="campo_data"><?php echo $today; ?></div>					
				<div id="topo_right"></div>	
		</div>
		 
		<div id="conteudo">				 
			{TPL_content}		 
		</div>		
				 
	  <div id="rodape">
	  	<?php echo $CI->config->item('rodape_sistema');?>
	  </div>	 
	</div>
</body>
</html>
