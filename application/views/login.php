<?php 
	$CI =& get_instance();
?>
<div id="corpo">	
	<div id="area_livre" style="background-image: url(<?php echo $CI->config->item('base_url');?>images/bg_login_<?php echo $CI->config->item('orgao');?>.gif);">
		<div class="titulo_aplicacao">
			GeraDoc
			<br>
			<div style='font-size:17pt; text-align:center;'>Sistema Gerenciador de Documentos</div>
			<div style='font-size:10pt; text-align:right; margin-right:30px;'>Versão 2.2</div>
		</div>
		<div style="text-align: left; padding-top:67px; padding-left:25px; font-size: 10pt; color: #555; line-height:200%;">
				<p>Melhor visualizado com:</p>
				<p>
					&nbsp; &nbsp; &nbsp; <a href="http://www.mozilla.org/pt-BR/firefox/fx/" target="_blank"><img src="{TPL_images}firefox_mini.png" width="95px"/></a>
				</p>
			</div>
	</div>
	<div id="caixa_login">		
		<div id="content_login">
			<div class="titulo_login"> Login </div>
			<div id="login">	
			
				<?php if(isset($setores) and $setores != null ){
				
					echo "<div style='padding: 7px; font-size: 12pt; line-height: 150%;'>Selecione o setor: <br>".$setores."</div>";
					
				}else{
				
				?>
				<form action="<?php echo $form_action; ?>" method="post"> 										
					<div>
						<label for="txtLogin">CPF:</label> 
						<input class="txt_login" type="text" value="<?php echo set_value('cpf');?>" name="cpf" id="cpf" type="text" maxlength="14" size="14" /> 
						<?php echo form_error('cpf'); ?>
					</div> 
					<div> 
						<label for="txtSenha">Senha:</label> 
						<input class="txt_login" type="password" name="txtSenha" id="txtSenha" type="password"  size="11" /> 
						<?php 
							echo form_error('txtSenha'); 
							echo $mensagem;
						?>
					</div>			
					<div style="text-align: center; padding: 10px;">
						<input class="button" id="btnLogin" name="btnLogin" type="submit" value="Acessar" /> 
					</div>	
					
					<div style="text-align: center;  padding: 10px;">
						<a href="<?php echo base_url() . "index.php/usuario/nova_senha"; ?>" class="link1">Esqueci a senha</a> 
					</div>	
					
							
				</form>	
				
				<?php } ?>		 	
			</div>	
			
			
		</div>
	</div>	
</div>	
		
