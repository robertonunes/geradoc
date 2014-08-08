$(document).ready(function(){		
	$("#cpf").mask("999.999.999-99");
	$("#cpf").focus();
	
	$("#topo_right").click(function(){
		$(window.document.location).attr('href','http://www.ceara.gov.br');
	});
	
	$("#topo_right").mouseover(function(){
		$(this).css("cursor","pointer");
	});
		
});