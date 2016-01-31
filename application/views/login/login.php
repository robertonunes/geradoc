<?php 
	$CI =& get_instance();
?>
<script>
    	if (window.innerWidth <= 900) window.location.replace("<?php echo base_url();?>index.php/login_mobile")
</script>
<div id="corpo">	
	<div id="area_livre" style="background-image: url(<?php echo $CI->config->item('base_url');?>images/bg_login_<?php echo $CI->config->item('orgao');?>.png);">
		<div class="titulo_aplicacao">
			GeraDoc
			<br>
			<div style='font-size:17pt; text-align:center;'>Sistema Gerenciador de Documentos</div>
			<div style='font-size:10pt; text-align:right; margin-right:30px;'>Versão 2.7</div>
		</div>
		<div style="text-align: left; padding-top:67px; padding-left:25px; font-size: 10pt; color: #555; line-height:200%;">
				<p>Melhor visualizado com:
				<br>
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
				
					<div class="form-group <?php echo (form_error('cpf') != '')? 'has-error':''; ?>" style="max-width: 150px; margin:0 auto;">
					    <label class="sr-only" for="cpf">CPF</label>
					   	<input type="text" class="form-control" name="cpf"  id="cpf" placeholder="Informe o CPF" value="<?php echo set_value('cpf');?>">
					</div>	
					<div style="padding: 5px;"></div>
					<div class="form-group <?php echo (form_error('txtSenha') != '')? 'has-error':''; ?>" style="max-width: 150px; margin:0 auto;">
					    <label class="sr-only" for="txtSenha">Senha</label>
					   	<input type="password" class="form-control" name="txtSenha" id="txtSenha" placeholder="Informe a senha">
					   	
					</div>
					
					<div class="alert alert-warning" role="alert" id="alertCapsLock" style="display: none; padding: 10px; margin-top: 10px;"><img class="img_align" src="{TPL_images}/error.png" alt="! " /> <strong>Caps Lock está ativado!</strong></div>
					
					<?php 
							echo form_error('cpf');
							echo form_error('txtSenha'); 
							echo $mensagem;
					?>
																			
					<div style="text-align: center; padding: 10px;">
					<!-- 
						<input class="btn btn-success" id="btnLogin" name="btnLogin" type="submit" value="Entrar" /><i class="fa fa-caret-right"></i>
						 -->
						<button type="submit" class="btn btn-success" id="btnLogin" name="btnLogin" aria-label="Entrar">
							  Entrar <i class="fa fa-caret-right fa-lg"></i>
						</button>
						 
					</div>	
					
					<div style="text-align: center;  padding-top: 5px;">
						<a href="<?php echo base_url() . "index.php/usuario/nova_senha"; ?>" class="btn btn-primary btn-sm"><strong>Esqueci a senha </strong><i class="fa fa-key fa-lg" style="color: #FFFF00"></i></a> 
					</div>	
					
							
				</form>	
				
				<?php } ?>		 	
			</div>	
			
			
		</div>
	</div>	
</div>	
		
