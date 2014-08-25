<div class="areaimage">
	<center>
		<img src="{TPL_images}Actions-document-edit-icon.png" height="72px" />
	</center>
</div>

<p class="bg-success lead text-center">Documento</p>

<div id="msg" style="display: none;">
	<img src="{TPL_images}loader.gif" alt="Enviando" />Aguarde
	carregando...
</div>

<div id="view_content">

 <?php
    echo $link_back;
    echo $message;
    ?>

	<div class="formulario">
	
	
	<form class="form-horizontal" role="form" id="form" name="form" disabled="disabled">

	
			<div class="panel panel-default">
			
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo $titulo; ?></h3>
				</div>
			

				<div class="panel-body">

						
					<div class="form-group">
						<label for="campoRemetente" class="col-sm-3 control-label"><span style="color: red;">*</span> Remetente</label>
						<div class="col-md-7">
							<?php
								echo form_dropdown('campoRemetente', $remetentesDisponiveis, $remetenteSelecionado);
							?> 
						</div>
					</div>
					
					
					<div class="form-group">
						<label for="campoSetor" class="col-sm-3 control-label">Setor</label>
						<div class="col-md-7">
							<input type="hidden" name="setorId" id="setorId" value="<?php echo $setorId; ?>" />
							<?php echo form_input($campoSetor); ?>
						</div>
					</div>
					  
					<div class="form-group">
						<label for="campoData" class="col-sm-3 control-label"><span style="color: red;">*</span> Data</label>
						<div class="col-md-2">
							<?php echo form_input($campoData); ?>
						</div>
					</div>
					
					<div class="form-group">
						<label for="campoCarimbo" class="col-sm-3 control-label">Carimbo de folha</label>
						<div class="col-md-3">
							<?php
								echo form_dropdown('campoCarimbo', $carimbosDisponiveis, $carimboSelecionado);
							?>
						</div>
					</div>
					
					<div class="form-group">
						<label for="campoTipo" class="col-sm-3 control-label"><span style="color: red;">*</span> Tipo</label>
						<div class="col-md-3">
							<?php
								echo form_dropdown('campoTipo', $tiposDisponiveis, $tipoSelecionado);
							?>
						</div>
					</div>
					
					<div class="form-group">
						<label for="campoAssunto" class="col-sm-3 control-label"><span style="color: red;">*</span> Assunto</label>
						<div class="col-md-7">
							<?php echo form_input($campoAssunto);?> 
						</div>
					</div>

								<?php 
						
								$campos_dinamicos_pequenos = '';
								
								if($tipoSelecionado != null){
										
										$obj_tipo = $this->Tipo_model->get_by_id($tipoSelecionado)->row();
										
										$this->load->model('Coluna_model','',TRUE);
										$campos_especiais = $this->Coluna_model->list_all();
							
										foreach ($campos_especiais as $key => $nome_campo){
							
											if(strpos($obj_tipo->$nome_campo, ';') != FALSE){
												$campo = explode(';' , $obj_tipo->$nome_campo);
											}else{
												$campo[0] = $obj_tipo->$nome_campo;
												$campo[1] = $nome_campo;
											}
											
											$coluna = $this->Coluna_model->get_by_nome($nome_campo);
											
											if($campo[0] == 'S' and $coluna['tipo'] == 'string'){

												$campos_dinamicos_pequenos .= '	
					
													<!--  Campo '.$nome_campo.' -->
		
													<div class="form-group">
														<label for="'.'campo_'.$nome_campo.'" class="col-sm-3 control-label"><span style="color: red;">*</span> '.$campo[1].'</label>
															<div class="col-md-7">
															'.$input_campo[$nome_campo].'
															</div>
													</div>
				
													<!--  Fim do campo '.$nome_campo.' -->
												';	
		
											}	
								
										}	
							
									}
									
									echo $campos_dinamicos_pequenos;
								
								?>
					
						<div class="form-group">
							<label for="campoPara" class="col-sm-3 control-label"><span style="color: red;">*</span> Destinatário</label>
							<div class="col-md-7">
								<input type="text" name="campoBusca" value="pesquisa textual" id="campoBusca" size="30" class="form-control" />
								
								<?php echo form_textarea($campoPara); ?>
								<span class="error_field" id="para_error" style="display: none;"></span> 
							</div>
						</div>
					

						<div class="form-group">
							<div class="col-md-12">
								<div style="width: 330px; margin-top: 3px; margin-left: auto; margin-right: auto; display:block; display: table; background-color: #eee;">
									<div style="float: left; color: #333; height:37px; border: 1px solid #ccc; line-height: 200%;"> &nbsp;Esta sessão expira em:&nbsp;</div>
									<div id="defaultCountdown" style="width: 170px; height:37px; float: right; color: #C00000;"></div>
								</div>
								<div class="error_field" id="monitor" style="background-color: #fff; position:relative; float: right; top: -23px; padding-right: 20px;"></div>
							</div>
						</div>

						
						<?php 
						
						$campos_dinamicos_grandes = '';
						
						if($tipoSelecionado != null){
								
								$obj_tipo = $this->Tipo_model->get_by_id($tipoSelecionado)->row();
								
								$this->load->model('Coluna_model','',TRUE);
								$campos_especiais = $this->Coluna_model->list_all();
					
								foreach ($campos_especiais as $key => $nome_campo){
					
									if(strpos($obj_tipo->$nome_campo, ';') != FALSE){
										$campo = explode(';' , $obj_tipo->$nome_campo);
									}else{
										$campo[0] = $obj_tipo->$nome_campo;
										$campo[1] = $nome_campo;
									}
									
									$coluna = $this->Coluna_model->get_by_nome($nome_campo);
									
									if($campo[0] == 'S' and $coluna['tipo'] == 'blob'){

										$campos_dinamicos_grandes .= '	
					
											<!--  Campo '.$nome_campo.' -->
		
												<div class="col-lg-11">
		
												<div class="text-left form-group">
													<label class="control-label text-left"><span style="color: red;">*</span> '.$campo[1].'</label>
													
													<script type="text/javascript">
														$().ready(function() {				
															 $("textarea#campo_'.$nome_campo.'").tinymce({
															      script_url : "'. base_url() .'js/tinymce/tinymce.min.js",
															      language : "pt_BR",
															  	  menubar : false,
															  	  browser_spellcheck : true,
															  	  content_css : "'. base_url() .'css/style_editor.css" ,
															  	  width : 800,
															  	  relative_urls: false,
															  	  setup : function(ed){
															  		ed.on("init", function() {
															  			   this.getDoc().body.style.fontSize = "10.5pt";
															  			});
															  	},
												
															  	plugins: "preview image jbimages spellchecker textcolor table lists code",
												
															  	toolbar: "undo redo | bold italic underline strikethrough | subscript superscript removeformat | alignleft aligncenter alignright alignjustify | forecolor backcolor | bullist numlist outdent indent | preview code | fontsizeselect table | jbimages ",
															  	statusbar : false,
															  	relative_urls: false
												
															   });
														});
												   </script>
													'.$input_campo[$nome_campo].'

												</div>
										
											</div>
		
											<!--  Fim do campo '.$nome_campo.' -->
										';	

									}	
						
								}	
					
							}
							
							echo $campos_dinamicos_grandes;
						?>

					</div>
					<!-- fim da div panel-body -->
					
			</div>
			<!-- fim da div panel -->	
			
			<div class="btn-group">
		   		<?php
			    	echo $link_cancelar;
			    	echo $link_salvar;
			    ?>
		</div>	
			
		</form>

	</div>
	<!-- fim da div formulario -->
	
</div>
<!-- fim da div  view_content -->
