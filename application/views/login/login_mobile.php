<div class="container">

      <form class="form-signin" role="form" action="<?php echo $form_action; ?>" method="post">
        <h2 class="form-signin-heading">GeraDoc</h2>
        
        <?php 
			echo form_error('cpf');
			echo form_error('txtSenha'); 
			echo $mensagem;
		?>
        
        <input type="text" class="form-control" placeholder="CPF" required autofocus  name="cpf"  id="cpf" value="<?php echo set_value('cpf');?>">
        <input type="password" class="form-control" placeholder="Senha" required name="txtSenha" id="txtSenha">
        
       
        <button class="btn btn-lg btn-success btn-block" type="submit">Entrar</button>
        
       
		<a href="<?php echo base_url() . "index.php/usuario/nova_senha"; ?>" class="btn btn-lg btn-primary btn-block">Esqueci a senha</a> 
		
        
	
      </form>
      
      

</div> <!-- /container -->