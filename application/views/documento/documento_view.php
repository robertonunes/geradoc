<link rel="stylesheet" href="<?php echo base_url(); ?>css/pdf.css">
<link href="<?php echo base_url(); ?>js/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>bootstrap/css/bootstrap-select.min.css">

<script type="text/javascript" src="<?php echo base_url(); ?>js/datepicker/js/jquery.ui.datepicker-pt-BR.js"></script>
<script src="<?php echo base_url(); ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.maskedinput.min.js"></script>

<div class="areaimage">
	<center>
		<h4 class="text-mutted"><img src="{TPL_images}Actions-document-edit-icon.png" height="62px" /> <?php echo $titulo;?></h4>
	</cente>
</div>

<div id="msg" style="display: none;">
	<img src="{TPL_images}loader.gif" alt="Enviando" />Aguarde
	carregando...
</div>

<div id="view_content">

	<div class="row">
    
	    <div class="col-md-12 text-center">
	    	<div class="btn-group">
		    <?php
			    echo $link_back;
			    echo $link_export_sm;
			    echo $link_update_sm;
			    echo $link_history;
			    echo $link_workflow;
			    
			    if ($carimbos == 'yes'){

			    $texto_campoDataAlerta = ($campoDataAlerta != '') ? ': <strong class="text-danger">' . $campoDataAlerta . ' - ' . $campoHoraAlerta.'</strong>' : '';			
		    ?>
		    <div class="btn-group">
			  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			    <i class="cus-stamp_in"></i> Carimbo <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu" role="menu">
			    <li><?php echo $carimbo_pagina;?></li>
			    <li><?php echo $carimbo_via;?></li>
			    <li><?php echo $carimbo_confidencial;?></li>
			    <li><?php echo $carimbo_urgente;?></li>
			  </ul>
			</div> 
			<a href="#" class="btn btn-default btn-sm" data-original-title="" title="" data-toggle="modal" data-target="#modalAlerta"><i class="cus-bell_add"></i> Alerta<?php echo $texto_campoDataAlerta;?></a>
			<?php }?>

		  	</div> 
		  	
	    </div>

    </div>


	<div class="formulario">
	
	
	<div class="pagina">
	
	
	
	<?php 
	
	$header = '<div style="padding-bottom: 20px;">
				<table width="100%" style="vertical-align: bottom;">
				<tr>
				<td align="center">'.$cabecalho.'</td>
				</tr>
				</table>
				</div>';
	
	if($objeto->carimbo == 'S'){
		$header .= '<div style="position: absolute; float: right; text-align: right; margin-top:-119px; margin-left: 617px;">
						<img src="../../../images/carimbo_aesp.png" width="80px"/>
					</div>';
	}
	
	
	if($objeto->carimbo_via == 'S'){
		$header .= '<div  style="position: absolute; text-align: right; margin-top:0px; margin-left: 630px; ">
						<img src="../../../images/carimbo_via_2.png" width="35px"/>
					</div>';
	}

	if($objeto->carimbo_confidencial == 'S'){
		$header .= '<div style="position: absolute; text-align: right; margin-top:140px; margin-left: 630px; ">
						<img src="../../../images/carimbo_confidencial_2.png" width="40px"/>
					</div>';
	}
	
	if($objeto->carimbo_urgente == 'S'){
		$header .= '<div  style="position: absolute;  text-align: right; margin-top:300px; margin-left: 630px;">
						<img src="../../../images/carimbo_urgente_2.png" width="45px"/>
					</div>';
	}
	
	
	$content = '<div class="conteudo" style="min-height:900px; font-size:17px;">
				'.htmlspecialchars_decode($objeto->layout).'
			</div>';
	
	$footer = '
		<table width="100%" style="vertical-align: top;font-family:\'Times New Roman\',Times,serif; font-size: 11px;">
			<tr>
				<td align="center" colspan="2">
					<div style="padding-top: 20px;">'.$rodape.'</div>
				</td>
			</tr>
			<tr>
				<td style="font-size: 9px" align="left">'.$documento_identificacao.'</td>	
				<td align="right">página x de x</td>
			</tr>
		</table>
		';
	
	echo $header;
	echo $content;
	echo $footer;
	
	?>
	
	</div>	

	</div>
	<!-- fim da div formulario -->
	
	<div class="row" style="padding-top: 15px">
    
	    <div class="col-md-12 text-center">
	    	<div class="btn-group">
		    <?php

		    echo $link_back;
		    echo $link_export_sm;
		    echo $link_update_sm;
		    echo $link_history;
		    echo $link_workflow;
		   
		    if ($carimbos == 'yes'){

		    ?>
		     <div class="btn-group">
			  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			    <i class="cus-stamp_in"></i> Carimbo <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu" role="menu">
			    <li><?php echo $carimbo_pagina;?></li>
			    <li><?php echo $carimbo_via;?></li>
			    <li><?php echo $carimbo_confidencial;?></li>
			    <li><?php echo $carimbo_urgente;?></li>
			  </ul>
			</div> 
			<?php } ?>
			
		  	</div>  
	    </div>

    </div>
	
</div>
<!-- fim da div  view_content -->


<!-- Modal -->
<div class="modal fade" id="modalAlerta" tabindex="-1" role="dialog" aria-labelledby="Alerta">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><i class="fa fa-bell-o fa-lg" style="color: #f0ad4e;"></i> Adicionar alerta</h3>
      </div>
      
      <form  id="contactForm" role="form" action="<?php echo $form_action_alerta;?>" method="post">
   
			<div class="modal-body">
			
					<?php if($mostraFormAddAlerta == true){?>
					<div class="alert alert-warning text-justify" role="alert" style="padding: 10px;">
						<h4><strong>Leia-me!</strong></h4>
						<p class="text-justify"> Para adicionar um <strong>novo</strong> alerta para <strong>este documento</strong>, preencha os cambos abaixo:</p>
					</div>
				
			      	  <div class="row">
			      	  
			      	  	<div class="col-md-6">
					  	  	<div class="form-group">
				            	<label for="campoDataAlerta" class="control-label">Data:</label>
				            	<input type="text" name="campoDataAlerta" id="campoDataAlerta" value="" placeholder="Informe a data" maxlength="10" size="12" class="form-control" required style="width: 150px;">
				          	</div>
				         </div>
				          
				         <div class="col-md-3">
				          	<div class="form-group">
				           	 	<label for="campoHoraAlerta" class="control-label">Hora:</label>
				           	 	<input type="text" name="campoHoraMinutoAlerta" id="campoHoraMinutoAlerta" value="" placeholder="Informe a hora" maxlength="5" class="form-control text-left" required>
				          	</div>
				         </div>
				        
			          </div>

					  <div class="row">
					  	<div class="col-md-12">
				          <div class="form-group">
						  		<label for="campoMotivoAlerta" class="control-label">Motivo:</label>
							    <textarea class="form-control" rows="2" name="campoMotivoAlerta" id="campoMotivoAlerta" placeholder="Motivo" required></textarea>
						  </div>
						 </div>
					  </div>
					  
					  <?php }else{?>
						
						<div class="alert alert-danger text-justify" role="alert" style="padding: 10px;">
							<h4><strong>Leia-me!</strong></h4>
							<h4 class="text-center"> Já existe um alerta cadastrado para esse documento.</h4>
							<h4 class="text-center"> Data: <strong><?php echo $campoDataAlerta; ?></strong>, hora: <strong><?php echo $campoHoraAlerta;?></strong></h>
							<p></p>
							<p class="text-center"> <a href="<?php echo site_url('/alerta'); ?>" class="btn btn-default btn-sm"><i class="cus-bell"></i> Clique aqui para verificar seus alertas</a></p>
						</div>
						
					 <?php }?>
	  	       	
			</div>
			      
		      <div class="modal-footer">
		      
			        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
			        
			        <?php 
						if($campoDataAlerta == ''){
					?>
			        <button type="submit" class="btn btn-warning">Salvar <span class="glyphicon glyphicon glyphicon-ok"></span></button>
			        
			        <?php }?>
		        
		      </div>
			      
		</form>


    </div>
  </div>
</div>
<script type="text/javascript">

$(document).ready(function(){

	$.datepicker.setDefaults( $.datepicker.regional[ "pt-BR" ] );
	$( "#campoDataAlerta" ).datepicker({
		beforeShow: function() {
	        setTimeout(function(){
	            $('.ui-datepicker').css('z-index', 99999999999999);
	        }, 0);
	    }
	});

	$("#campoHoraMinutoAlerta").mask("99:99");

});
</script>