<div class="titulo1">	 			
	<?php echo $titulo; ?>
</div>

<div class="areaimage">
	<center>
		<img src="{TPL_images}secrecy-icon.png" height="72px"/>
	</center>
</div>
			
<div class="formulario">		
	<form id="frm1" name="frm1" action="<?php echo $form_action; ?>" method="post">
	
	<fieldset class="conteiner2"> 
	    
	        <legend class="subTitulo6">Senha</legend> 
	        
	        <table class="table_form">
	        	<tbody>
		        	<tr>
			        	<td class=gray><span class="text-red">*</span> Senha atual: </td>
			        	<td class="green">
			        	<input class="textbox" value="<?php echo set_value('txtSenhaAtual')?>"  name="txtSenhaAtual"  id="txtSenhaAtual" type="password" size="15" />
			        	<i>(Digite a senha atual)</i>
			        	<?php echo form_error('txtSenhaAtual'); ?>
			        	</td>
		        	</tr>
		        	<tr>
			        	<td class="gray"><span class="text-red">*</span> Nova Senha: </td>
			        	<td class="green">
			        	<input class="textbox" value="<?php echo set_value('txtSenhaNova')?>" name="txtSenhaNova"  id="txtSenhaNova" type="password"  size="15" />
			        	<?php echo form_error('txtSenhaNova'); ?>
			        	</td>
		        	</tr>
		        	<tr>
			        	<td class="gray"><span class="text-red">*</span> Confirmação: </td>
			        	<td class="green">
			        	<input class="textbox" value="<?php echo set_value('txtSenhaNovaConf')?>" name="txtSenhaNovaConf"  id="txtSenhaNovaConf" type="password"  size="15" />
			        	<i>(Digite a nova senha novamente)</i>	
						<?php echo form_error('txtSenhaNovaConf'); ?>
			        	</td>
		        	</tr>
	        	</tbody>
	        </table>
	    </fieldset>
	    
	    
		
		<br>
		
		<input type="button" class="button" value="Voltar" title="Voltar" onclick="javascript:window.history.back();"/> &nbsp; &nbsp;					
		<input type="submit" class="button" value="Salvar" title="Salvar"/>&nbsp;&nbsp;		
			
	</form>
</div>
