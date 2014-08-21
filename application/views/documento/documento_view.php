<div class="areaimage">
	<center>
		<img src="{TPL_images}Actions-document-edit-icon.png" height="72px" />
	</center>
</div>

<ol class="breadcrumb">
	<li><a href="<?php echo site_url('/documento/index'); ?>">Documentos</a></li>
  	<li class="active"><?php echo $titulo;?></li>
</ol>		

<div id="msg" style="display:none;"><img src="{TPL_images}loader.gif" alt="Enviando" />Aguarde carregando...</div> 

<div id="view_content">	

    <?php
    echo $link_back;
    echo $message;
    ?>
	
	<div class="formulario">	
	
	    <fieldset class="conteiner2" style="width: 800px; "> 

	        <legend class="subTitulo6">Documento</legend> 
	        
	          
	        <table class="table_form">
	        	<tbody>
	        		<tr>
			        	<td class=gray colspan="2">  
			        		<div class="btn-group">
			        		<?php echo $link_update ." ". $link_export;?>
			        		</div>
			        	</td>
		        	</tr>
	        		<tr>
			        	<td class=gray> Tipo:
			        	</td>
			        	<td class="green"><?php echo $objeto->tipoNome; ?> 
			        	</td>
		        	</tr>
		        	<tr>
			        	<td class=gray> Setor:
			        	</td>
			        	<td class="green"><?php echo $caminho; ?> 
			        	</td>
		        	</tr>
		        	<tr>
			        	<td class=gray> Número:
			        	</td>
			        	<td class="green"><?php echo $objeto->numero; ?> 
			        	</td>
		        	</tr>
		        	
		        	<?php if($objeto->tipo == 3 or $objeto->tipoID == 5){ ?>
		        	
		        	
		        	<tr>
			        	<td class=gray> Número do Processo:
			        	</td>
			        	<td class="green"><?php echo $objeto->num_processo; ?> 
			        	</td>
		        	</tr>
		        	<tr>
			        	<td class=gray> Interessado:
			        	</td>
			        	<td class="green"><?php echo $objeto->interessado; ?> 
			        	</td>
		        	</tr>
		        	
		        	<?php } ?>
		        	
		        	
		        	<tr>
			        	<td class=gray> Data:
			        	</td>
			        	<td class="green"><?php echo $objeto->data; ?> 
			        	</td>
		        	</tr>
		        	<tr>
			        	<td class=gray> Assunto:
			        	</td>
			        	<td class="green"><?php echo $objeto->assunto; ?> 
			        	</td>
		        	</tr>
		        	<tr>
			        	<td class=gray> Remetente:
			        	</td>
			        	<td class="green"><?php echo $objeto->remetNome . ", " . $objeto->remetCargoNome . " " . $objeto->remetSetorArtigo . " " . $objeto->setorSigla . "/" . $objeto->orgaoSigla; ?> 
			        	</td>
		        	</tr>

		        	<?php 
    					//if($objeto->tipoID != 4 and $objeto->tipoID != 6 and $objeto->tipoID != 7 and $objeto->tipoID != 8){ 
		        		if($objeto->tipoID == 1 or $objeto->tipoID == 2 or $objeto->tipoID == 3 and $objeto->tipoID == 5){ // 1 = COMUNICAO INTERNA, 2 = OFICIO, 3 = DESPACHO E 5 = PARECER JURIDICO
    				?>			
		        	<tr>
			        	<td class=gray> Destinatário:
			        	</td>
			        	<td class="green"><?php echo $objeto->para; ?> 
			        	</td>
		        	</tr>
		        	<?php } ?> 
		        	
		        	<?php if($objeto->tipoID == 1 or $objeto->tipoID == 2){ // 1 = COMUNICACAO INTERNA, 2 = OFÍCIO?>			
		        	<tr>
			        	<td class=gray> Referência:
			        	</td>
			        	<td class="green"><?php echo $objeto->referencia; ?> 
			        	</td>
		        	</tr>
		        	<?php } ?>
		        	
		        	<?php 		        	
	        		
        			if($objeto->objetivo  and !$objeto->redacao) {  // se for parecer tecnico 
					
						echo '<tr>
					        	<td class="gray"> Objetivo:
					        	</td>
					        	<td class="green">' . $objeto->objetivo .'
					        	</td>
				          	  </tr>';
						echo '<tr>
					        	<td class="gray"> Documentação:
					        	</td>
					        	<td class="green">' . $objeto->documentacao .'
					        	</td>
				          	  </tr>';
						echo '<tr>
					        	<td class="gray"> Análise:
					        	</td>
					        	<td class="green">' . $objeto->analise .'
					        	</td>
				          	  </tr>';
						echo '<tr>
					        	<td class="gray"> Conclusão e Parecer:
					        	</td>
					        	<td class="green">' . $objeto->conclusao .'
					        	</td>
				          	  </tr>';
					}else{
						echo '<tr>
					        	<td class="gray"> Redação:
					        	</td>
					        	<td class="green">'. $objeto->redacao .'
					        	</td>
				        	</tr>';
					}

					?> 

		        	<tr>
			        	<td class=gray colspan="2">
			        		<div class="btn-group">
			        			<?php echo $link_update ." ". $link_export;?>
			        		</div>
			        	</td>
		        	</tr>
		        	
	        	</tbody>
	        </table>
	    </fieldset>
	    
	    <input type="button" class="btn btn-success" value="&nbsp; OK &nbsp;" title=" OK " onclick="javascript:window.location ='<?php echo $bt_ok; ?>'" /><br><br>
				
    </div>

</form> 

</div><!-- fim: div view_content --> 
