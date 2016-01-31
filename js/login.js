$(document).ready(function(){
	$("#cpf").mask("999.999.999-99");
	$("#cpf").focus();
	
	$("#topo_right").click(function(){
		$(window.document.location).attr('href','http://www.ceara.gov.br');
	});
	
	$("#topo_right").mouseover(function(){
		$(this).css("cursor","pointer");
	});
	
	
	
	
	$('#txtSenha').keypress(function(e) { 
	    var s = String.fromCharCode( e.which );
	    if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
//	        alert('caps is on');
	       // $("#alertCapsLock").show();
	        $("#alertCapsLock").css("display","block");
	    }else{
	    	$("#alertCapsLock").css("display","none");
	    	
	    }
	});
		
});