<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documento extends CI_Controller {
	/*
	 * Atributos opcionais para as views
	* public $layout;  define o layout default
	* public $title; define o titulo default da view
	* public $css = array('css1','css2'); define os arquivos css default
	* public $js = array('js1','js2'); define os arquivos javascript default
	* public $images = 'dir_images'; define a diretório default das imagens
	*
	*/
	
	public $layout = 'default';
	public $css = array('style','demo_page','demo_table_jui','jquery-ui-1.8.11.custom');
	public $js = array('jquery-1.7.1.min','jquery.dataTables.min','jquery.blockUI','about');
	public $js_custom;
	
	private $area            = "documento";
	private $tituloIndex     = "s";
	private $tituloAdd       = "Novo ";
	private $tituloView      = "Detalhes do ";
	private $tituloUpdate    = "Edita ";

	public function __construct (){
		parent::__construct();
		$this->load->library(array('restrict_page','table','form_validation','session', 'datas'));
		$this->load->helper('url');
		$this->load->model('Documento_model','',TRUE);
		$this->load->model('Grid_model','',TRUE);
		
		$this->modal = $this->load->view('about_modal', '', TRUE);
		session_start();

		if(isset($_SESSION['homepage']) == true and $_SESSION['homepage'] == "index/0"){
			$_SESSION['homepage'] = $this->area."/index";
		}

	}

	public function index($setor_escolhido=NULL, $inicio = 0){
		
		$this->_checa_tabelas();
	
		$_SESSION['homepage'] = current_url();
		
		$this->js[] = 'documento';

		$session_id = $this->session->userdata('session_id');
		$session_cpf = $this->session->userdata('cpf');
		$_SESSION['cpf'] = $session_cpf;
		$session_nome = $this->session->userdata('nome');
		$session_sobrenome = $this->session->userdata('sobrenome');
		$session_nivel = $this->session->userdata('nivel');
		
		$data['titulo']     = mb_convert_case($this->area, MB_CASE_TITLE, "ISO-8859-1").$this->tituloIndex;
		$data['link_add']   = anchor($this->area.'/add/','Novo documento',array('class'=>'add'));
		$data['link_back']  = anchor('documento/index/','Lista de Documentos',array('class'=>'back'));
		$data['form_action'] = site_url($this->area.'/search');

		// BUSCA
		$data['keyword_'.$this->area] = '';
		if(isset($_SESSION['keyword_'.$this->area]) == true and $_SESSION['keyword_'.$this->area] != null){
			$data['keyword_'.$this->area] = $_SESSION['keyword_'.$this->area];
			redirect($this->area.'/search/');
		}else{
			$data['keyword_'.$this->area] = 'pesquisa textual';
			$data['link_search_cancel'] = '';
		}
		// FIM DA BUSCA
			
		/***** SETORES *****/
		
		
		$this->load->model('Setor_model','',TRUE);
		
		$session_setor = $this->session->userdata('setor');
		
		$restricao = $this->Setor_model->get_by_id($session_setor)->row()->restricao;
		
		if($restricao == 'S'){
			
			$data['setores'] = $this->Setor_model->get_by_id($session_setor)->result();
			
		}else{
			
			$data['setores'] = $this->Setor_model->list_all()->result();
			
			$arraySetores['all'] = "TODOS";

		}
		
		foreach ($data['setores']as $setor){
			$arraySetores[$setor->id] = "$setor->nome";
		}
		$data['setoresDisponiveis']  =  $arraySetores;
			
		$uri_setor = substr($this->uri->segment(3), 1);
			
		$_SESSION['setorSelecionado'] = ($uri_setor) ? $uri_setor : $session_setor;
			
		$data['setorSelecionado'] = $_SESSION['setorSelecionado'];
       
		/***** SETORES FIM *****/
		
		$maximo = 10;

		$uri_segment = 4;
		
		$inicio = (!$this->uri->segment($uri_segment, 0)) ? 0 : ($this->uri->segment($uri_segment, 0) - 1) * $maximo;

		if($this->input->post('txt_busca')){
			$documentos = $this->Documento_model->lista_busca($this->session->userdata('cpf'), $this->input->post('txt_busca'))->result();
			$data['link_add'] .= '<div class="alerta1" style="width:80%;"><b> Texto da busca: </b>'.$this->input->post('txt_busca'). ' &nbsp; ' .anchor('documento/index/','cancelar',array('class'=>'delete')) . '</div>';
			
			$contagem = $this->Documento_model->lista_busca($this->session->userdata('cpf'), $this->input->post('txt_busca'))->num_rows();
			
		}else{

			if($data['setorSelecionado'] == 0){ // zero significa todos os documentos

				$documentos = $this->Documento_model->lista_todos_documentos($inicio, $maximo, $this->session->userdata('cpf'));
				
				$contagem = $this->Documento_model->conta_todos_documentos($this->session->userdata('cpf'));

			}else{

				$documentos = $this->Documento_model->lista_documentos_por_setor($inicio, $maximo, $data['setorSelecionado'], $this->session->userdata('cpf'));
				
				$contagem = $this->Documento_model->conta_documentos_por_setor($data['setorSelecionado'], $this->session->userdata('cpf'));
			
			}
		}
		
		//Inicio da Paginacao
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->area.'/index/s'.$data['setorSelecionado'].'/');
		$config['uri_segment']= 4;
		$config['total_rows'] = $contagem;
		$config['per_page'] = $maximo;
		
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

		// carregando os dados na tabela
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Identificação', 'Assunto', 'Autor', 'Criado em','Ação');

		//Monta a DataTable
        $tmpl = $this->Grid_model->monta_tabela_list();
        $this->_monta_linha($documentos);
        $this->table->set_template($tmpl);
        // Fim da DataTable

        // variaveis para a view
       	$data['table'] = $this->table->generate();
        $data["total_rows"] = $config['total_rows'];
       
       // echo $this->db->last_query();
        
		// load view
		$this->load->view($this->area.'/'.$this->area.'_list', $data);
		
		$this->audita();
		
	}

	function add(){
		
		$this->_checa_tabelas();

		$this->form_validation->set_error_delimiters('<span class="error_field"> <img class="img_align" src="{TPL_images}/error.png" alt="!" /> ', '</span>');
		
		//--- VARIAVEIS COMUNS ---//
		$data['titulo']         = $this->tituloAdd.$this->area;
		$data['message']        = '';
		$data['form_action']	= site_url($this->area.'/add/');
		$data['acao']          	= "add";
		$data['link_back'] 		= anchor($_SESSION['homepage'],'Voltar para a lista de '.$this->area.'s',array('class'=>'back'));
		$data['id'] = '';
		$data['sess_expiration'] = $this->config->item('sess_expiration');
		//--- FIM ---//
		
		//--- CONSTRUCAO DOS CAMPOS ---//
		$this->load->model('Campo_model','',TRUE);
		$data['campoData']            	= $this->Campo_model->documento('campoData');
		$data['campoData']['value']   	= date("d/m/Y");
		$data['campoSetor']           	= $this->Campo_model->documento('campoSetor');
		$data['campoPara']           	= $this->Campo_model->documento('campoPara');
		$data['campoAssunto']         	= $this->Campo_model->documento('campoAssunto');
		$data['campoReferencia']      	= $this->Campo_model->documento('campoReferencia');
		$data['campoRedacao']         	= $this->Campo_model->documento('campoRedacao');
		
		$data['campoObjetivo']         	= $this->Campo_model->documento('campoObjetivo');
		$data['campoDocumentacao']      = $this->Campo_model->documento('campoDocumentacao');
		$data['campoAnalise']         	= $this->Campo_model->documento('campoAnalise');
		$data['campoConclusao']         = $this->Campo_model->documento('campoConclusao');
		
		$data['campoCarimbo']           = $this->Campo_model->documento('campoCarimbo');
		$data['carimbosDisponiveis'] 	= $this->Campo_model->documento('arrayCarimbos');
		$data['carimboSelecionado']  	= $this->uri->segment(8) ? $this->uri->segment(8) : 'N';
		
		$data['desp_num_processo']      = $this->Campo_model->documento('desp_num_processo');
		$data['desp_interessado']      	= $this->Campo_model->documento('desp_interessado');

		//--- FIM ---//

		//--- POPULA O DROPDOWN DE REMENTETES ---//
		
		$this->load->model('Setor_model','',TRUE);
		
		$session_setor = $this->session->userdata('setor');
		
		$restricao = $this->Setor_model->get_by_id($session_setor)->row()->restricao;
		
		if($restricao == 'S'){
			$remetentes = $this->Contato_model->list_all_actives($session_setor)->result();		
		}else{
			$remetentes = $this->Contato_model->list_all_actives()->result();
		}
		
		$this->load->model('Contato_model','',TRUE);
		//$remetentes = $this->Contato_model->list_all_actives()->result();
		$arrayRemetentes[0] = "SELECIONE";
		if($remetentes){
			foreach ($remetentes as $remetente){
				$arrayRemetentes[$remetente->id] = $remetente->nome;
			}
		}else{
			$arrayRemetentes[1] = "";
		}
		$data['remetentesDisponiveis']  =  $arrayRemetentes;

		$_SESSION['remetenteSelecionado'] = $this->uri->segment(4) ? $this->uri->segment(4) : 0;
			
		$data['remetenteSelecionado'] = $this->input->post('campoRemetente') ? $this->input->post('campoRemetente') : $_SESSION['remetenteSelecionado'];
		//--- FIM ---//
		
		//--- MOSTRA A SIGLA DO SETOR E COLOCA O SETORID EM UM CAMPO HIDDEN ---//
		if($data['remetenteSelecionado'])
			$setor = $this->Documento_model->get_setor($data['remetenteSelecionado'])->row();
		else $setor = null;
		if(isset($setor)){
			if($setor->setorPaiSigla == "NENHUM"){
				$data['campoSetor']['value'] = "$setor->sigla";
			}else{
				$data['campoSetor']['value'] = "$setor->sigla/$setor->setorPaiSigla";
			}
	
			$data['setorId'] = $setor->setorId;
		}
		else{
			$data['campoSetor']['value'] = ' ';
			$data['setorId'] = ' ';
		}
		//--- FIM ---//
		
		//--- POPULA O DROPDOWN DE TIPOS ---//
		$this->load->model('Tipo_model','',TRUE);
		$tipos = $this->Tipo_model->list_all_actives()->result();
		$arrayTipos[0] = "SELECIONE";
		if($tipos){
			foreach ($tipos as $tipo){
				$arrayTipos[$tipo->id] = $tipo->nome;	
			}
		}else{
			$arrayTipos[1] = "";
		}
		
		//--------------------------------------------------------------------------------------------------------------------------//
		//--- RETIRA DE $arrayTipos ATO ADMINISTRATIVO E NOTA DE INSTRUCAO CASO O SETOR DO USUARIO NÃO SEJA DIRECAO GERAL ---//
		//echo $this->session->userdata('setor');
		if($this->session->userdata('setor') != 11 and $this->session->userdata('setor') != 7){ // 11 = DIRETORIA GERAL DA AESP, 7 = CTIC
		//if($this->session->userdata('setor') != 11){ // 11 = DIRETORIA GERAL DA AESP
			unset($arrayTipos[6]);// 6 = ATO ADMINISTRATIVO
			unset($arrayTipos[8]);// 8 = NOTA DE ELOGIO
		}
		//--------------------------------------------------------------------------------------------------------------------------//
		//--- RETIRA DE $arrayTipos: ATO ADMINISTRATIVO E NOTA DE INSTRUCAO CASO O SETOR DO USUARIO NÃO SEJA DIRECAO GERAL ---//
		if($this->session->userdata('setor') != 41 and $this->session->userdata('setor') != 7){ // 41 = ASSESSORIA JURÍDICA
			unset($arrayTipos[5]);// PARECER JURIDICO
		}
		//--------------------------------------------------------------------------------------------------------------------------//
		
		
		$data['tiposDisponiveis']  =  $arrayTipos;
		
		$_SESSION['tipoSelecionado'] = $this->uri->segment(6) ? $this->uri->segment(6) : 0;
			
		$data['tipoSelecionado'] = $this->input->post('campoTipo') ? $this->input->post('campoTipo') : $_SESSION['tipoSelecionado'];
		
		
		
		//--- FIM ---//
		
		//--- o tipo de validacao ($tipo_validacao) varia de acordo com o tipo de documento selecionado ($data['tipoSelecionado']) ---//
		$tipo_validacao = $this->set_tipo_validacao($data['tipoSelecionado']);
		//--- fim --///
		
		//echo $data['tipoSelecionado'];
		if($data['tipoSelecionado'] != null){
			$obj_tipo = $this->Tipo_model->get_by_id($data['tipoSelecionado'])->row();
			
			$data['flag_redacao'] = $obj_tipo->redacao;
			$data['flag_objetivo'] = $obj_tipo->objetivo;
			$data['flag_documentacao']= $obj_tipo->documentacao;
			$data['flag_analise'] = $obj_tipo->analise;
			$data['flag_conclusao'] = $obj_tipo->conclusao;
			
			$this->valida_campos_especiais($obj_tipo);	
			
		}else{
			//echo "aqui";
			$data['flag_redacao'] = 'N';
			$data['flag_objetivo'] = 'N';
			$data['flag_documentacao']= 'N';
			$data['flag_analise'] = 'N';
			$data['flag_conclusao'] = 'N';
		}
		
		
		if ($this->form_validation->run($tipo_validacao) == FALSE) {
			
			
			if($data['tipoSelecionado'] == 4){ // 4 = parecer tecnico
				
				$this->load->view($this->area . "/" . $this->area.'_edit_parecer_tecnico', $data);
				
			}else{
				$this->load->view($this->area . "/" . $this->area.'_edit', $data);
			}

		}else{
			
			$obj_do_form = array(
					'dono' =>  $this->session->userdata('nome')." ".$this->session->userdata('sobrenome'),
					'dono_cpf' =>  $this->session->userdata('cpf'),
					'dono' =>  $this->session->userdata('nome')." ".$this->session->userdata('sobrenome'),
					'data_criacao' =>  date("Y-m-d"),
					'data' => $this->datas->dateToUS($this->input->post('campoData')),
					'remetente' => $this->input->post('campoRemetente'),
					'setor' => $this->input->post('setorId'),
					'tipo' => $this->input->post('campoTipo'),
					'assunto' => $this->input->post('campoAssunto'),
					'referencia' => $this->input->post('campoReferencia'),
					'para' => $this->input->post('campoPara'),
					'redacao' => $this->input->post('campoRedacao'),
					'carimbo' => $this->input->post('campoCarimbo'),
					
					//'objetivo' => $this->input->post('campoObjetivo'),
					//'documentacao' => $this->input->post('campoDocumentacao'),
					//'analise' => $this->input->post('campoAnalise'),
					//'conclusao' => $this->input->post('campoConclusao'),
					
			);
			
			if($this->input->post('campoObjetivo')){
				$obj_do_form['objetivo'] = $this->input->post('campoObjetivo');
			}
			
			if($this->input->post('campoDocumentacao')){
				$obj_do_form['documentacao'] = $this->input->post('campoDocumentacao');
			}
			
			if($this->input->post('campoAnalise')){
				$obj_do_form['analise'] = $this->input->post('campoAnalise');
			}
			
			if($this->input->post('campoConclusao')){
				$obj_do_form['conclusao'] = $this->input->post('campoConclusao');
			}
			

			//--- MAGICA DA CONTAGEM, MIOLO DO SISTEMA! ---//
			//$inicio_contagem = $this->Documento_model->get_tipo($obj_do_form['tipo'])->row()->inicio;
			$inicio_contagem = $this->Documento_model->get_inicio_contagem($obj_do_form['tipo'], $this->datas->get_year_US($obj_do_form['data']));
			
			//echo "<br>inicio_contagem = ".$inicio_contagem;

			$obj_do_form["numero"] =  $this->_set_number($obj_do_form['setor'], $obj_do_form['tipo'], $inicio_contagem, $this->datas->get_year_US($obj_do_form['data']));
			
			//echo "<br>obj_do_form['numero'] = ". $obj_do_form["numero"];
			
			//$checa_existencia = $this->Documento_model->get_by_numero($obj_do_form['numero'], $obj_do_form['tipo'], $obj_do_form['setor'])->row();
			$checa_existencia = $this->Documento_model->get_by_numero($obj_do_form['numero'], $obj_do_form['tipo'], $obj_do_form['setor'], $this->datas->get_year_US($obj_do_form['data']))->row();
			//--- FIM ---//
			
			// se a checagem retornar um valor diferente de nulo
			if ($checa_existencia != null){
				
				echo  '<br> Já existe um documento com essa numeração. <br>';

			}else{

				$id = $this->Documento_model->save($obj_do_form);
				
				if ($id < 1 or $id == null){
				
					echo  '<br> Erro na conexão com o banco. <br>';
				
				}else{
				
					$this->js_custom = 'var sSecs = 3;
                                function getSecs(){
                                    sSecs--;
                                    if(sSecs<0){ sSecs=59; sMins--; }
                                    $("#clock1").html(sSecs);
                                    setTimeout("getSecs()",1000);
                                    var s =  $("#clock1").html();
                                    if (s == "1"){
                                        window.location.href = "' . site_url('/'.$this->area."/view/".$id) . '";
                                    }
                                }
                                ';
				
					$data['mensagem'] = "<br /> Redirecionando em... ";
					$data['mensagem'] .= '<span id="clock1"> ' . "<script>setTimeout('getSecs()',1000);</script> </span>";
					$data['link1'] = '';
					$data['link2'] = '';
				
					$this->audita();
					$this->load->view('success', $data);
				
				}
				//fim
				
			}
			
		}

	}
	
	public function valida_campos_especiais($campo){

		$config = array();

		if($campo->objetivo == 'S'){
			array_push($config, array(
			'field'   => 'campoObjetivo',
			'label'   => '<strong>Objetivo</strong>',
			'rules'   => 'trim|required'
					));
		}
			
		if($campo->documentacao == 'S'){			
			array_push($config, array(
			'field'   => 'campoDocumentacao',
			'label'   => '<strong>Documentação</strong>',
			'rules'   => 'trim|required'
					));

		}
			
		if($campo->analise == 'S'){			
			array_push($config, array(
			'field'   => 'campoAnalise',
			'label'   => '<strong>Análise</strong>',
			'rules'   => 'trim|required'
					));
		}
			
		if($campo->conclusao == 'S'){
			array_push($config, array(
			'field'   => 'campoConclusao',
			'label'   => '<strong>Conclusão</strong>',
			'rules'   => 'trim|required'
					));
		}

		return $this->form_validation->set_rules($config);


	}

	function update($id){
		
		//--- VARIAVEIS COMUNS ---//
		$data['titulo']         = $this->tituloAdd.$this->area;
		$data['message']        = '';
		$data['form_action']	= site_url($this->area.'/update/'.$id);
		$data['acao']          	= "update";
		$data['link_back']      = anchor($_SESSION['homepage'].'#d'.$id,'Voltar para a lista de '.$this->area.'s',array('class'=>'back'));
		$data['id'] = '';
		$data['sess_expiration'] = $this->config->item('sess_expiration');
		//--- FIM ---//
		
		$obj = $this->Documento_model->get_by_id($id)->row();
		
		$permissao = $this->get_permissao($obj->setor, $this->session->userdata('id_usuario'));

		if($obj->dono_cpf != $this->session->userdata('cpf') and $permissao < 2){
			//redirect($this->area.'/index/');
			redirect($this->area . '/negado/'.$id);
		}
		
		if($obj->cancelado == 'S'){
			redirect($this->area . '/cancelado/'.$id);
		}
	
		$this->form_validation->set_error_delimiters('<span class="error_field"> <img class="img_align" src="{TPL_images}/error.png" alt="!" /> ', '</span>');

	
		//--- CONSTRUCAO DOS CAMPOS ---//
		$this->load->model('Campo_model','',TRUE);
		$data['campoData']            	= $this->Campo_model->documento('campoData');
		$data['campoSetor']           	= $this->Campo_model->documento('campoSetor');
		$data['campoPara']           	= $this->Campo_model->documento('campoPara');
		$data['campoAssunto']         	= $this->Campo_model->documento('campoAssunto');
		$data['campoReferencia']      	= $this->Campo_model->documento('campoReferencia');
		$data['campoRedacao']         	= $this->Campo_model->documento('campoRedacao');
		
		$data['campoObjetivo']         	= $this->Campo_model->documento('campoObjetivo');
		$data['campoDocumentacao']      = $this->Campo_model->documento('campoDocumentacao');
		$data['campoAnalise']         	= $this->Campo_model->documento('campoAnalise');
		$data['campoConclusao']         = $this->Campo_model->documento('campoConclusao');

		$data['campoCarimbo']           = $this->Campo_model->documento('campoCarimbo');
		$data['carimbosDisponiveis'] 	= $this->Campo_model->documento('arrayCarimbos');
		$data['carimboSelecionado']  	= $this->input->post('campoCarimbo') ? $this->input->post('campoCarimbo') : $obj->carimbo;
		
		$data['desp_num_processo']      = $this->Campo_model->documento('desp_num_processo');
		$data['desp_interessado']      	= $this->Campo_model->documento('desp_interessado');
		//--- FIM ---//
	
	
		//--- POPULA O DROPDOWN DE REMENTETES ---//
		$this->load->model('Contato_model','',TRUE);
		$remetentes = $this->Contato_model->list_all()->result();
		if($remetentes){
			foreach ($remetentes as $remetente){
				//limita os remetentes ao original
				if($remetente->id == $obj->remetente){
					$arrayRemetentes[$remetente->id] = $remetente->nome;
				}
				
			}
		}else{
			$arrayRemetentes[1] = "";
		}
		$data['remetentesDisponiveis']  =  $arrayRemetentes;
	
		$_SESSION['remetenteSelecionado'] = $this->uri->segment(4) ? $this->uri->segment(4) : $obj->remetente;
			
		$data['remetenteSelecionado'] = $this->input->post('campoRemetente') ? $this->input->post('campoRemetente') : $_SESSION['remetenteSelecionado'];
		//--- FIM ---//
	
	
		//--- MOSTRA A SIGLA DO SETOR E COLOCA O SETORID EM UM CAMPO HIDDEN ---//
		if($data['remetenteSelecionado']){
			$setor = $this->Documento_model->get_setor($data['remetenteSelecionado'])->row();
		}else{
			$setor = $this->Documento_model->get_setor($obj->remetente)->row();
		}
		if(isset($setor)){
			if($setor->setorPaiSigla == "NENHUM"){
				$data['campoSetor']['value'] = "$setor->sigla";
			}else{
				$data['campoSetor']['value'] = "$setor->sigla/$setor->setorPaiSigla";
			}
	
			$data['setorId'] = $setor->setorId;
		}
		else{
			$data['campoSetor']['value'] = "$setor->sigla/$setor->setorPaiSigla";
			$data['setorId'] = $obj->setor;
		}
		//--- FIM ---//
	
		//--- POPULA O DROPDOWN DE TIPOS ---//
		$this->load->model('Tipo_model','',TRUE);
		$tipos = $this->Tipo_model->list_all()->result();
		$arrayTipos[0] = "SELECIONE";
		if($tipos){
			foreach ($tipos as $tipo){
				$arrayTipos[$tipo->id] = $tipo->nome;
			}
		}else{
			$arrayTipos[1] = "";
		}
		$data['tiposDisponiveis']  =  $arrayTipos;
	
		$_SESSION['tipoSelecionado'] = $this->uri->segment(6) ? $this->uri->segment(6) : $obj->tipo;
			
		$data['tipoSelecionado'] = $this->input->post('campoTipo') ? $this->input->post('campoTipo') : $_SESSION['tipoSelecionado'];
		
		$num_of_tipos = sizeof($data['tiposDisponiveis']);
		for($i=0 ; $i<=$num_of_tipos ; $i++)
		if($i != $data['tipoSelecionado'])
		unset($data['tiposDisponiveis'][$i]);
		
		//$data['obj_tipo'] = $this->Tipo_model->get_by_id($data['tipoSelecionado'])->row();
		
		//--- FIM ---//
		
		$data['campoData']['value']          = $this->_trata_dataDoBancoParaForm($obj->data);
		$data['campoAssunto']['value']       = $obj->assunto;
		$data['campoReferencia']['value']    = $obj->referencia;
		$data['campoPara']['value']       	 = $obj->para;
		$data['campoRedacao']['value']       = $obj->redacao;
		
		$data['campoObjetivo']['value']      = $obj->objetivo;
		$data['campoDocumentacao']['value']  = $obj->documentacao;
		$data['campoAnalise']['value']       = $obj->analise;
		$data['campoConclusao']['value']     = $obj->conclusao;
		
		if($obj->tipo == 3 or $obj->tipo == 5){
			$tmp = $this->Documento_model->get_despacho_head($id);
			$data['despacho_head'] = $tmp[0];
			$data['desp_num_processo']['value']  = $data['despacho_head']['num_processo'];
			$data['desp_interessado']['value']   = $data['despacho_head']['interessado'];
			$tmp = NULL;
		}
			
		//--- o tipo de validacao ($tipo_validacao) varia de acordo com o tipo de documento selecionado ($data['tipoSelecionado']) ---//
		$tipo_validacao = $this->set_tipo_validacao($data['tipoSelecionado']);
		//--- fim --///
		
		if($data['tipoSelecionado'] != null){
			$obj_tipo = $this->Tipo_model->get_by_id($data['tipoSelecionado'])->row();
			
			$data['flag_redacao'] = $obj_tipo->redacao;
			$data['flag_objetivo'] = $obj_tipo->objetivo;
			$data['flag_documentacao']= $obj_tipo->documentacao;
			$data['flag_analise'] = $obj_tipo->analise;
			$data['flag_conclusao'] = $obj_tipo->conclusao;
			
			$this->valida_campos_especiais($obj_tipo);	
			
		}	
		
		if ($this->form_validation->run($tipo_validacao) == FALSE) {
			
			if($data['tipoSelecionado'] == 4){ // 4 = parecer tecnico
			
				$this->load->view($this->area . "/" . $this->area.'_edit_parecer_tecnico', $data);
			
			}else{
				$this->load->view($this->area . "/" . $this->area.'_edit', $data);
			}

		}else{
				
			$obj_do_form = array(
					'dono' =>  $this->session->userdata('nome')." ".$this->session->userdata('sobrenome'),
					'dono_cpf' =>  $this->session->userdata('cpf'),
					'dono' =>  $this->session->userdata('nome')." ".$this->session->userdata('sobrenome'),
					'data_criacao' =>  date("Y-m-d"),
					'data' => $this->datas->dateToUS($this->input->post('campoData')),
					'remetente' => $this->input->post('campoRemetente'),
					'setor' => $this->input->post('setorId'),
					'tipo' => $this->input->post('campoTipo'),
					'assunto' => $this->input->post('campoAssunto'),
					'referencia' => $this->input->post('campoReferencia'),
					'para' => $this->input->post('campoPara'),
					'redacao' => $this->input->post('campoRedacao'),
					'carimbo' => $this->input->post('campoCarimbo'),
					
					//'objetivo' => $this->input->post('campoObjetivo'),
					//'documentacao' => $this->input->post('campoDocumentacao'),
					//'analise' => $this->input->post('campoAnalise'),
					//'conclusao' => $this->input->post('campoConclusao'),
			);
			
			if($this->input->post('campoObjetivo')){
				$obj_do_form['objetivo'] = $this->input->post('campoObjetivo');
			}
				
			if($this->input->post('campoDocumentacao')){
				$obj_do_form['documentacao'] = $this->input->post('campoDocumentacao');
			}
				
			if($this->input->post('campoAnalise')){
				$obj_do_form['analise'] = $this->input->post('campoAnalise');
			}
				
			if($this->input->post('campoConclusao')){
				$obj_do_form['conclusao'] = $this->input->post('campoConclusao');
			}
			
			
			//--- MAGICA DA CONTAGEM, MIOLO DO SISTEMA! ---//
			//$inicio_contagem = $this->Documento_model->get_tipo($obj_do_form['tipo'])->row()->inicio;
			$inicio_contagem = $this->Documento_model->get_inicio_contagem($obj_do_form['tipo'], $this->datas->get_year_US($obj_do_form['data']));
				
			//$obj_do_form["numero"] =  $this->_set_number($obj_do_form['setor'], $obj_do_form['tipo'], $inicio_contagem);
			$obj_do_form["numero"] =  $this->_set_number($obj_do_form['setor'], $obj_do_form['tipo'], $inicio_contagem, $this->datas->get_year_US($obj_do_form['data']));
				
			//$checa_existencia = $this->Documento_model->get_by_numero($obj_do_form['numero'], $obj_do_form['tipo'], $obj_do_form['setor'])->row();
			$checa_existencia = $this->Documento_model->get_by_numero($obj_do_form['numero'], $obj_do_form['tipo'], $obj_do_form['setor'], $this->datas->get_year_US($obj_do_form['data']))->row();
			//--- FIM ---//
	
			// se a checagem retornar um valor diferente de nulo
			if ($checa_existencia != null){
	
				echo  '<br> Já existe um documento com essa numeração. <br>';
				
				echo $this->db->last_query();
	
			}else{
	
				/*
				$this->Documento_model->update($id,$obj_do_form);
				$this->audita();
				redirect($this->area."/view/".$id);
				*/
				
				//inicio
				if ($this->Documento_model->update($id,$obj_do_form) === FALSE){
						
					echo  '<br> Erro na atualização. <br>';
				
				}else{
				
					//redirect($this->area."/view/".$id);
				
					$this->js_custom = 'var sSecs = 3;
                                function getSecs(){
                                    sSecs--;
                                    if(sSecs<0){ sSecs=59; sMins--; }
                                    $("#clock1").html(sSecs);
                                    setTimeout("getSecs()",1000);
                                    var s =  $("#clock1").html();
                                    if (s == "1"){
                                        window.location.href = "' . site_url('/'.$this->area."/view/".$id) . '";
                                    }
                                }
                                ';
						
					$data['mensagem'] = "<br /> Redirecionando em... ";
					$data['mensagem'] .= '<span id="clock1"> ' . "<script>setTimeout('getSecs()',1000);</script> </span>";
					$data['link1'] = '';
					$data['link2'] = '';
						
					$this->audita();
					$this->load->view('success', $data);

				}
				//fim
	
			}
				
		}
	
	}
	function view($id){

		$data['titulo']         = $this->tituloView.$this->area;
		$data['message']        = '';
		
		$data['link_back']      = anchor($_SESSION['homepage'].'#d'.$id,'Voltar para a lista de '.$this->area.'s',array('class'=>'back'));
		
		//echo "SESSION['homepage'] =". $_SESSION['homepage'];
		
		$data['link_update'] 	= anchor($this->area.'/update/'.$id,'alterar',array('class'=>'update'));
		$data['link_export'] 	= anchor($this->area.'/export/'.$id,'exportar',array('class'=>'pdf', 'target'=>'_blank'));
		$data['bt_ok']    		= $_SESSION['homepage'].'#d'.$id;
		
		// popula o array com os dados do objeto alimentado pela consulta
		$data['objeto'] = $this->Documento_model->get_by_id($id)->row();
		if(!$data['objeto']) die('Documento não encontrado!<br>É tudo o que sabemos.<br><br>CTIC/AESP<br><a href="'.site_url('documento').'">&lt;- &nbsp;Voltar para a lista de documentos</a>');
		if($data['objeto']->tipoID == 3 or $data['objeto']->tipoID == 5){
			$tmp = $this->Documento_model->get_despacho_head($id);
			$data['despacho_head'] = $tmp[0];
			$data['objeto']->num_processo  = $data['despacho_head']['num_processo'];
			$data['objeto']->interessado   = $data['despacho_head']['interessado'];
			$tmp = NULL;
		}

		// trata os dados vindos do banco
		$data['objeto']->tipoNome = mb_convert_case($data['objeto']->tipoNome, MB_CASE_TITLE, "UTF-8");
		$data['objeto']->data_despacho = $data['objeto']->data;
		$date = new DateTime($data['objeto']->data);
		$data['objeto']->ano = $date->format('Y');
		$data['objeto']->data = $this->_trata_data($data['objeto']->data);
		
		$data['caminho'] = $this->getCaminho($data['objeto']->setor);

		if($data['objeto']->destSexo = "M"){
			$data['objeto']->destSexo = 'Ao Sr. ';
		}else{
			$data['objeto']->destSexo = 'À Sra. ';
		}
		 
		$data['objeto']->remetNome          = $this->_trata_contato($data['objeto']->remetNome);
		$data['objeto']->remetCargoNome      = mb_convert_case($data['objeto']->remetCargoNome, MB_CASE_TITLE, "UTF-8");
		$data['objeto']->remetSetorArtigo    ="d".mb_convert_case($data['objeto']->remetSetorArtigo, MB_CASE_LOWER, "UTF-8");
		

		if(isset($_SESSION['keyword'.$this->area]) == true and $_SESSION['keyword'.$this->area] != null and strstr($_SESSION['homepage'], 'search', true)){

			
			$data['objeto']->numero = $this->highlight($data['objeto']->numero, $_SESSION['keyword'.$this->area]);
			$data['objeto']->remetNome = $this->highlight($data['objeto']->remetNome, $_SESSION['keyword'.$this->area]);
			$data['objeto']->remetCargoNome = $this->highlight($data['objeto']->remetCargoNome, $_SESSION['keyword'.$this->area]);
			$data['objeto']->remetSetorArtigo = $this->highlight($data['objeto']->remetSetorArtigo, $_SESSION['keyword'.$this->area]);
			$data['objeto']->para = $this->highlight($data['objeto']->para, $_SESSION['keyword'.$this->area]);
			$data['objeto']->assunto = $this->highlight($data['objeto']->assunto, $_SESSION['keyword'.$this->area]);
			$data['objeto']->referencia = $this->highlight($data['objeto']->referencia, $_SESSION['keyword'.$this->area]);
			$data['objeto']->redacao = $this->highlight($data['objeto']->redacao, $_SESSION['keyword'.$this->area]);
		
		}

		//fim do tratamento

		$this->load->view($this->area.'/documento_view', $data);
		$this->audita();
		
	}

	function export($id){
		// carrega as variaveis padroes
		$data['titulo']         = $this->tituloView.$this->area;
		$data['message']        = '';
		$data['link_back']      = anchor('documento/index/'.$_SESSION['homepage'],'Voltar para a lista de '.$this->area.'s',array('class'=>'back'));

		// popula o array com os dados do objeto alimentado pela consulta
		$data['objeto'] = $this->Documento_model->get_by_id($id)->row();
		if(!$data['objeto']) die('Documento não encontrado!<br>É tudo o que sabemos.<br><br>CTIC/AESP<br><a href="'.site_url('documento').'">&lt;- &nbsp;Voltar para a lista de documentos</a>');
		if($data['objeto']->tipoID == 3 or $data['objeto']->tipoID == 5){
			$tmp = $this->Documento_model->get_despacho_head($id);
			$data['despacho_head'] = $tmp[0];
			$tmp = NULL;
		}
		$data['objeto']->data_despacho = $data['objeto']->data;

		// trata os dados vindos do banco
		$data['objeto']->tipoNome = mb_convert_case($data['objeto']->tipoNome, MB_CASE_TITLE, "UTF-8");
		$date = new DateTime($data['objeto']->data);
		$data['objeto']->ano = $date->format('Y');
		$data['objeto']->data = $this->_trata_data($data['objeto']->data);
		$data['caminho_remetente'] = $this->getCaminho($data['objeto']->setor);
				
		$data['objeto']->remetNome          = $this->_trata_contato($data['objeto']->remetNome);
		$data['objeto']->remetCargoNome      = mb_convert_case($data['objeto']->remetCargoNome, MB_CASE_TITLE, "UTF-8");
		$data['objeto']->remetSetorArtigo    ="d".mb_convert_case($data['objeto']->remetSetorArtigo, MB_CASE_LOWER, "UTF-8");

			

		// Definindo o cabecalho e o rodape do documento
		$this->load->model('Tipo_model','',TRUE);
		$timbre = $this->Tipo_model->get_by_id($data['objeto']->tipoID)->row();
		
		if($timbre->cabecalho == null or $timbre->cabecalho == ''){
			$data['cabecalho'] = '<img src="./images/header_'.$_SESSION['orgao_documento'].'.png"/>';
		}else{
			$data['cabecalho'] = str_replace("../../../", "./", $timbre->cabecalho);
		}
		
		if($timbre->rodape == null or $timbre->rodape == ''){
			$data['rodape'] = $_SESSION['rodape_documento'];
		}else{
			$data['rodape'] = $timbre->rodape;
		}
		
		
		if($data['objeto']->tipoID == 3){ // 3 = Despacho, legado da AESP, nao da pra tirar por causa dos registros da tabela despacho_head, criada pelo Bruno. 
			
			$this->load->view($this->area.'/despacho_pdf', $data);
			
		}else{
			
			$data['objeto']->layout = str_replace('[tipo_doc]', $data['objeto']->tipoNome, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[numero]', $data['objeto']->numero, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[ano_doc]', $data['objeto']->ano, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[setor_doc]', $data['caminho_remetente'], $data['objeto']->layout);
			
			$data['objeto']->layout = str_replace('[data]', $data['objeto']->data, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[destinatario]', $data['objeto']->para, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[assunto]', $data['objeto']->assunto, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[referencia]', $data['objeto']->referencia, $data['objeto']->layout);
			
			$data['objeto']->layout = str_replace('[redacao]', $data['objeto']->redacao, $data['objeto']->layout);
			
			if(!$data['objeto']->assinatura){
				$data['objeto']->assinatura = $data['objeto']->remetNome . '<br>'.$data['objeto']->remetCargoNome.' '.$data['objeto']->remetSetorArtigo.' '.$data['objeto']->remetSetorSigla.'';
			}
			$data['objeto']->layout = str_replace('[remetente_assinatura]', $data['objeto']->assinatura, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[remetente_nome]', mb_convert_case($data['objeto']->remetNome, MB_CASE_UPPER, "UTF-8"), $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[remetente_cargo]', mb_convert_case($data['objeto']->remetCargoNome . ' ' . $data['objeto']->remetSetorArtigo.' '.$data['objeto']->remetSetorSigla, MB_CASE_UPPER, "UTF-8"), $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[remetente_setor_artigo]', $data['objeto']->remetSetorArtigo, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[remetente_setor_sigla]', $data['objeto']->remetSetorSigla, $data['objeto']->layout);
			
			// --- Parecer Tecnico ---//
			$data['objeto']->layout = str_replace('[objetivo]', $data['objeto']->objetivo, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[documentacao]', $data['objeto']->documentacao, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[analise]', $data['objeto']->analise, $data['objeto']->layout);
			$data['objeto']->layout = str_replace('[conclusao]', $data['objeto']->conclusao, $data['objeto']->layout);
			
			$this->load->view($this->area.'/pdf', $data);
			
		}
		

		//echo $data['objeto']->layout;
		//exit;
			
		/*
		switch ($data['objeto']->tipoID) {
		
			case 1:
				$this->load->view($this->area.'/documento_pdf', $data); // Comuicacao Interna
				break;
		
			case 2:
				$this->load->view($this->area.'/documento_pdf', $data); // Oficio
				break;
		
			case 3:
				$this->load->view($this->area.'/documento_pdf', $data); // Despacho
				break;
		
			case 4:
				$this->load->view($this->area.'/documento_pdf_parecer_tecnico', $data); // Parecer Tecnico
				break;
		
			case 5:
				$this->load->view($this->area.'/documento_pdf', $data); // Parecer Juridico
				break;
		
			case 6:
				$this->load->view($this->area.'/documento_pdf_ato_adm', $data); // Ato Administrativo
				break;
		
			case 7:
				$this->load->view($this->area.'/documento_pdf_ato_adm', $data); // Nota de Instrucao
				break;
		
			case 8:
				$this->load->view($this->area.'/documento_pdf_ato_adm', $data); // Nota de Elogio
				break;
		
			case 9:
				$this->load->view($this->area.'/documento_pdf_cepad', $data); // Despacho da CEPAD (Comissao Especial Permanente de Acompanhamento Disciplinar das Galaxias)
				break;
		
		}
		*/
		
		$this->audita();
		
		
		
	}
	
	function export_rtf($id){
		// carrega as variaveis padroes
		$data['titulo']         = $this->tituloView.$this->area;
		$data['message']        = '';
		$data['link_back']      = anchor('documento/index/'.$_SESSION['homepage'],'Voltar para a lista de '.$this->area.'s',array('class'=>'back'));
	
		// popula o array com os dados do objeto alimentado pela consulta
		$data['objeto'] = $this->Documento_model->get_by_id($id)->row();
		if(!$data['objeto']) die('Documento não encontrado!<br>É tudo o que sabemos.<br><br>CTIC/AESP<br><a href="'.site_url('documento').'">&lt;- &nbsp;Voltar para a lista de documentos</a>');
		if($data['objeto']->tipoID == 3 or $data['objeto']->tipoID == 5){
			$tmp = $this->Documento_model->get_despacho_head($id);
			$data['despacho_head'] = $tmp[0];
			$tmp = NULL;
		}
		$data['objeto']->data_despacho = $data['objeto']->data;
	
		// trata os dados vindos do banco
		$data['objeto']->tipoNome = mb_convert_case($data['objeto']->tipoNome, MB_CASE_TITLE, "UTF-8");
		$date = new DateTime($data['objeto']->data);
		$data['objeto']->ano = $date->format('Y');
		$data['objeto']->data = $this->_trata_data($data['objeto']->data);
	
	
		$data['objeto']->remetNome          = $this->_trata_contato($data['objeto']->remetNome);
		$data['objeto']->remetCargoNome      = mb_convert_case($data['objeto']->remetCargoNome, MB_CASE_TITLE, "UTF-8");
		$data['objeto']->remetSetorArtigo    ="d".mb_convert_case($data['objeto']->remetSetorArtigo, MB_CASE_LOWER, "UTF-8");
	
		$this->audita();
	
		if($data['objeto']->tipoID == 4 or $data['objeto']->tipoID == 6 or $data['objeto']->tipoID == 7 or $data['objeto']->tipoID == 8){ // 4 = PARECER TECNICO, 7 = ATO ADMINISTRATIVO, 8 = NOTA DE INSTRUCAO, 9 = NOTA DE ELOGIO
			$this->load->view($this->area.'/documento_pdf_ato_adm', $data);
		}else{
			$this->load->view($this->area.'/documento_pdf', $data);
		}
	
	}

	function lock($id){
		$obj["cadeado"] = "S";
		$this->Documento_model->update($id,$obj);
		redirect('documento/index/');
	}

	function unlock($id){ 
		$obj["cadeado"] = "N";
		$this->Documento_model->update($id,$obj);
		redirect('documento/index/');
	}

	function hide($id){ 
		$obj["oculto"] = "S";
		$this->Documento_model->update($id,$obj);
		$this->audita();
		redirect($_SESSION['homepage'].'#d'.$id);

	}

	function show($id){
		$obj["oculto"] = "N";
		$this->Documento_model->update($id,$obj);
		$this->audita();
		redirect($_SESSION['homepage'].'#d'.$id);
	}

	function cancela($id){
		$obj["oculto"] = "S";
		$obj["cancelado"] = "S";
		$this->Documento_model->update($id,$obj);
		$this->audita();
		redirect($_SESSION['homepage'].'#d'.$id);
	}
	
	function cancelado($id){
		//--- VARIAVEIS COMUNS ---//
		$data['titulo']         = "Documento cancelado";
		$data['message']        = '';
		$data['link_back'] 		= anchor($_SESSION['homepage'].'#d'.$id,'Voltar para a lista de '.$this->area.'s',array('class'=>'back'));
		$data['bt_ok']    		= $_SESSION['homepage'].'#d'.$id;
		//--- FIM ---//
		
		$this->load->view($this->area . "/" . $this->area.'_cancelado', $data);
		$this->audita();
	}
	
	public function search($page = 1) {
		$_SESSION['homepage'] = current_url();
		$this->js[] = 'documento';
	
		$data['link_add']   = anchor($this->area.'/add/','Novo documento',array('class'=>'add'));
		$data['link_search_cancel'] = anchor($this->area.'/search_cancel/','CANCELAR PESQUISA',array('class'=>'button_cancel'));
		$data['form_action'] = site_url($this->area.'/search');
	
		$this->load->library(array('pagination', 'table'));
	
		$data['keyword_'.$this->area] = '';
		if(isset($_SESSION['keyword'.$this->area]) == true and $_SESSION['keyword'.$this->area] != null and $this->input->post('search') == null){
			$keyword = $_SESSION['keyword'.$this->area];
		}else{
			$keyword = ($this->input->post('search') == null or $this->input->post('search') == "pesquisa textual") ? redirect($this->area.'/index/') : $this->input->post('search');
			 
			$_SESSION['keyword'.$this->area] = $keyword;
		}
		$data['keyword_'.$this->area] = $keyword;
	
		$data['titulo'] = 'Resultado da busca por <font color="black">"'. $keyword .'"</font> nos conteúdos dos documentos.';
		$this->audita($keyword);
	
		$maximo = 10;
	
		$uri_segment = 3;
	
		$inicio = ($this->uri->segment($uri_segment)) ? ($this->uri->segment($uri_segment, 0) - 1) * $maximo : 0;
	
		// Definindo o universo de documentos a serem pesquisados.
		// Se o setor for restiro, ira listar apenas os domentos do setor mais os documentos criados pelo o usuário para outros setore.
		
		$session_setor = $this->session->userdata('setor');
		$this->load->model('Setor_model','',TRUE);
		$restricao = $this->Setor_model->get_by_id($session_setor)->row()->restricao;
		
		if($restricao == 'S'){
			
			$rows = $this->Documento_model->listAllSearchPag($keyword, $maximo, $inicio, $this->session->userdata('cpf'), $session_setor);
			
			$config['total_rows'] = $this->Documento_model->count_all_search($keyword, $this->session->userdata('cpf'), $session_setor);

		}else{
			
			$rows = $this->Documento_model->listAllSearchPag($keyword, $maximo, $inicio, $this->session->userdata('cpf'));
			
			$config['total_rows'] = $this->Documento_model->count_all_search($keyword, $this->session->userdata('cpf'));

		}
		// Fim da restricao
		
	
		$config['base_url'] = site_url($this->area.'/search');
		$config['uri_segment'] = $uri_segment;
		$config['per_page'] = $maximo;
	
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
	
		// carregando os dados na tabela
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Identificação', 'Assunto', 'Autor', 'Criado em','Ação');
	
		//Monta a DataTable
		$tmpl = $this->Grid_model->monta_tabela_list();
		$this->_monta_linha($rows);
		$this->table->set_template($tmpl);
		// Fim da DataTable
	
		// variaveis para a view
		$data['table'] = $this->table->generate();
		$data["total_rows"] = $config['total_rows'];
		$data['pagination'] = $this->pagination->create_links();
	
		// load view
		$this->audita();
		$this->load->view($this->area.'/'.$this->area.'_list', $data);
	
	}
	
	function negado($id){
		//--- VARIAVEIS COMUNS ---//
		$data['titulo']         = "Permissão negada";
		$data['message']        = 'Você não tem permissão para editar este arquivo';
		$data['link_back'] 		= anchor($_SESSION['homepage'].'#d'.$id,'Voltar para a lista de '.$this->area.'s',array('class'=>'back'));
		$data['bt_ok']    		= $_SESSION['homepage'].'#d'.$id;
		//--- FIM ---//
	
		$this->load->view($this->area . "/" . $this->area.'_negado', $data);
		$this->audita();
		$_SESSION['homepage'] = null;
	}

	function loadDestinatario(){		
		$this->layout = 'json';
		$keyword = $this->input->post('term');
		$data['response'] = 'false'; //Set default response
		$query = $this->Documento_model->lista_autocomplete($keyword); //Search DB

		if($query->num_rows() > 0){
			$data['response'] = 'true'; //Set response
			$data['message'] = array(); //Create array
			foreach($query->result() as $row){
				$data['message'][] = array('label'=> $row->para, 'value'=> $row->para); //Add a row to array
			}
		}
		echo json_encode($data);
	}

	function valid_date($str){

		if(!preg_match('^(0[1-9]|1[0-9]|2[0-9]|3[01])/(0[1-9]|1[012])/([0-9]{4})$^', $str)){
			
			$this->form_validation->set_message('valid_date', 'A data deve ter o formato: dd/mm/aaaa. Valores para dia: 01 a 31. Valores para mês: 01 a 12.');
			
			return false;
			
		}else{
			
			$array_data_postada = explode('/', $str);
				
			$numero_data_postada = $array_data_postada[2].$array_data_postada[1].$array_data_postada[0];
			
			$ano_postado = $array_data_postada[2];
				
			$array_hoje = explode('/', date('d/m/Y'));
			
			$ano_atual = $array_hoje[2];

			if($ano_postado < $ano_atual){
				$this->form_validation->set_message('valid_date', 'O ano informado ('.$ano_postado.') era menor que o ano atual! Corrigimos para a data de hoje');
				return false;
			}
			
			
			return true;
		}
		
		
		
		
	}

	function _trata_data($str){

		//trata a data
		$ano = substr($str,0,4);
		$mes = substr($str,5,2);
		$dia = substr($str,8,2);

		switch ($mes) {
			case "01":
				$mes = "janeiro";
				break;
			case "02":
				$mes = "fevereiro";
				break;
			case "03":
				$mes = "março";
				break;
			case "04":
				$mes = "abril";
				break;
			case "05":
				$mes = "maio";
				break;
			case "06":
				$mes = "junho";
				break;
			case "07":
				$mes = "julho";
				break;
			case "08":
				$mes = "agosto";
				break;
			case "09":
				$mes = "setembro";
				break;
			case "10":
				$mes = "outubro";
				break;
			case "11":
				$mes = "novembro";
				break;
			case "12":
				$mes = "dezembro";
				break;
		}
		return "$dia de $mes de $ano";
	}

	function _trata_data_update($str){
		$ano = substr($str,0,4);
		$mes = substr($str,5,2);
		$dia = substr($str,8,2);
		return "$dia-$mes-$ano";
	}

	function _trata_dataDoBancoParaForm($str){
		$ano = substr($str,0,4);
		$mes = substr($str,5,2);
		$dia = substr($str,8,2);
		return "$dia/$mes/$ano";
	}

	function _trata_dataDoFormParaBanco($str){
		$dia = substr($str,0,2);
		$mes = substr($str,3,2);
		$ano = substr($str,6,4);
		return "$ano-$mes-$dia";
	}

	function _trata_contato($str){
		$str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
		$str = str_replace('Da ', 'da ', $str);
		$str = str_replace('De ', 'de ', $str);
		$str = str_replace('Do ', 'do ', $str);
		return $str;
	}

	function _set_number($setor, $tipo, $inicio_contagem, $ano){

		$ultimoAno = $this->Documento_model->get_ano($tipo)->ultimoAno; // ultimo ano de um tipo de documento produzido
		
		$proximoNumero = $this->Documento_model->proximo_numero($setor,$tipo,$ano)->row()->proximoNumero;

		if($proximoNumero < $inicio_contagem){
			$number = $inicio_contagem;
		}else{
			$number = $proximoNumero;
		}

		return $number;
		

	}

	function _monta_assunto($assunto, $maximo = 45){

		$texto = null;

		if(strlen($assunto)>$maximo){
			$texto = substr($assunto, 0, $maximo) . "...";
			$ultimo_espaco = strripos($texto, " ");
			$texto = substr($assunto, 0, $ultimo_espaco) . "...";
		}else{
			$texto = $assunto;
		}

		return mb_convert_case($texto, MB_CASE_UPPER, "UTF-8");
	}
	
	
	public function get_permissao($setor, $usuario){
	
		$this->load->model('Setor_model', '', TRUE);
	
		$permissao = $this->Setor_model->get_permissao($setor, $usuario)->row();
	
		if($permissao){
			return $permissao->permissao;
		}else{
			return 1;
		}
	
	}

	function _monta_linha($documentos){
		
		$linha = null;

		foreach ($documentos as $documento){

			$tipoNome = $this->Documento_model->get_tipo($documento->tipo)->row();

			$obj = $this->Documento_model->get_by_id($documento->id)->row();
			 
			if($documento->oculto == "N" or $documento->cadeado == null){
				//$link_hide = anchor('#doc_'.$documento->id,'');
				$link_hide = anchor('documento/hide/'.$documento->id.'#d'.$documento->id,'<div class="hide">visível</div>').'&nbsp; ';
				$ocultado = "";
			}else{
				$ocultado = "&nbsp;*";
				$link_hide = anchor('documento/show/'.$documento->id.'#d'.$documento->id,'<div class="show">oculto</div>').'&nbsp;';
			}
			
			$setorRemetente = $this->getCaminho($obj->setor);
			
			//$setorRemetente = ($obj->orgaoSigla == "NENHUM") ? $obj->setorSigla : $obj->setorSigla . "/" . $obj->orgaoSigla;
			
			
//------------------------------------------------------------------------------------------------------------------------------------//
//----- ACOES

			$permissao = $this->get_permissao($obj->setor, $this->session->userdata('id_usuario'));
			
			$acoes 	= 	null;
			//$acoes .= 	$permissao;
			$acoes .= 	anchor('documento/view/'.$documento->id,'<div class="view">visualizar</div>');
			
			if($documento->cancelado == "N" or $documento->cancelado == null){
				
				$acoes .=	anchor('documento/export/'.$documento->id,'<div class="pdf">exportar</div>',array('target'=>'_blank'));
				
				if($documento->dono_cpf == $this->session->userdata('cpf') or $permissao >= 2){
					$acoes .=	anchor('documento/update/'.$documento->id,'<div class="edit">alterar</div>');
				}
				
				if($documento->dono_cpf == $this->session->userdata('cpf') or $permissao == 3){
					$acoes .=	$link_hide .' ';
					$acoes .=	anchor('documento/cancela/'.$documento->id,'<div class="delete">cancelar</div>',array('onclick' => "return confirm('Deseja REALMENTE cancelar esse registro?')")).'&nbsp; ';
				}
				
			}else{
				
				$acoes .= anchor($_SESSION['homepage'].'#d'.$documento->id,'<div class="cancelado"><BR>CANCELADO</div>');
				
			}
			
//------------------------------------------------------------------------------------------------------------------------------------//
				
			$linha = $this->table->add_row(
					'<a name="d'.$documento->id.'" id="d'.$documento->id.'"></a>' .
					"$tipoNome->abreviacao Nº $documento->numero <br> $setorRemetente",
					$this->_monta_assunto($documento->assunto),
					$obj->dono,
					$this->_trata_dataDoBancoParaForm($documento->data_criacao),
					$acoes
			);

		}

		return $linha;

	}
	
	function highlight($text, $words) {
		/*
		preg_match_all('~\w+~', $words, $m);
		if(!$m)
			return $text;
		$re = '~\\b(' . implode('|', $m[0]) . ')\\b~i';
		return preg_replace($re, "<span style='background-color:#FFFF00'>$0</span>", $text);
		*/
		
		return str_replace($words, "<span style='background-color:#FFFF00'>$words</span>", $text);
	}


    public function search_cancel() { 
        
        $_SESSION['keyword'.$this->area] = null;
        
        $this->audita();
        redirect('documento/index/');

    }
    
    public function getTipo ($idTipo){

    	$this->load->model('Tipo_model', '', TRUE);
    	$tipo = $this->Tipo_model->get_by_id($idTipo)->result();
    		
    	return $tipo;
    }
    
    public function getSetor ($id_setor){
    
    	$this->load->model('Setor_model', '', TRUE);
		$setor =  $this->Setor_model->get_by_id($id_setor)->row();
    
    	return $setor;
    }
    
    public function getCaminho ($id_setor){
    
    	$this->load->model('Setor_model', '', TRUE);
    	$setor =  $this->Setor_model->get_by_id($id_setor)->row();
    	
    	if($setor->setorPaiSigla and $setor->setorPaiSigla != "NENHUM" and $setor->setorPaiSigla != "AESP"){
    		$caminho =  $setor->sigla ."/" . $setor->setorPaiSigla ."/" . $setor->orgaoSigla;
    	}else{
    		$caminho =  $setor->sigla ."/" . $setor->orgaoSigla;
    	}
    	
    	return $caminho;
    }
    
    public function audita ($informacao_adicional = null){
    	
    	$this->load->model('Auditoria_model','',TRUE);
    	
    	$complemento = null;
    	if($informacao_adicional){
    		$complemento = "?".$informacao_adicional;
    	}
    
   		$obj_audit = array(
				'usuario' => $this->session->userdata('id_usuario'),
				'usuario_nome' => $this->session->userdata('nome'),
				'data' => date("Y/m/d H:i:s"),
				'url' => current_url().$complemento,
		);
		
		if(isset($_SESSION['current_url']) == true and $_SESSION['current_url'] != current_url()){

			$_SESSION['current_url'] = current_url();
			$this->Auditoria_model->save($obj_audit);			
			
		}else{
			
			$_SESSION['current_url'] = current_url();
			
		}

    }
    
    function set_tipo_validacao($tipoSelecionado){
    	
    	$tipo_validacao = $this->area."/add"; // valor default
    	
    	switch ($tipoSelecionado) {
    		
    		case 1: 
    			$tipo_validacao = $this->area."/add"; // Comuicacao Interna
    		break;
    		
    		case 2: 
    			$tipo_validacao = $this->area."/add"; // Oficio
    		break;
    		
    		case 3: 
    			$tipo_validacao = $this->area."/add"; // Despacho
    		break;
    		
    		case 4: 
    			$tipo_validacao = $this->area."/add_parecer_tecnico"; // Parecer Tecnico
    		break;
    		
    		case 5: 
    			$tipo_validacao = $this->area."/add"; // Parecer Juridico
    		break;
    		
    		case 6: 
    			$tipo_validacao = $this->area."/add_sem_para"; // Ato Administrativo
    		break;
    		
    		case 7: 
    			$tipo_validacao = $this->area."/add_sem_para"; // Nota de Instrucao
    		break;
    		
    		case 8: 
    			$tipo_validacao = $this->area."/add_sem_para"; // Nota de Elogio
    		break;
    		
    		case 9: 
    			$tipo_validacao = $this->area."/add_sem_para"; // Despacho da CEPAD (Comissao Especial Permanente de Acompanhamento Disciplinar das Galaxias)
    		break;
    		
    	}
    	
    	/*
    	if($tipoSelecionado == 4 or $tipoSelecionado == 6 or $tipoSelecionado == 7 or $tipoSelecionado == 8 or $tipoSelecionado == 9){ // 4 = PARECER TECNICO, 6 = ATO ADMINISTRATIVO, 7 = NOTA DE INSTRUCAO E 8 = NOTA DE ELOGIO
    		$tipo_validacao = $this->area."/add_sem_para";
    	}else{
    		$tipo_validacao = $this->area."/add";
    	}
    	*/
    	
    	
    	return $tipo_validacao;
    	
    	
    }

    
    function _checa_tabelas(){
    	
    		$data['message'] = '';
    		
			$this->load->model('Tipo_model','',TRUE);
			if($this->Tipo_model->count_all() == 0){
				$data['message'] .= 'Nenhum tipo de documento cadastrado. Cadastre um.<br>';
			}
			
			if($this->Tipo_model->list_all_actives()->result() == null){
				$data['message'] .= 'Nenhum tipo de documento publicado. Pelo menos um deve ser publicado.<br>';
			}
			
			$this->load->model('Orgao_model','',TRUE);
			if($this->Orgao_model->count_all() == 0){
				$data['message'] .= 'Nenhum órgão cadastrado. Cadastre um.<br>';
			}
			
			$this->load->model('Setor_model','',TRUE);
			if($this->Setor_model->count_all() == 0){
				$data['message'] .= 'Nenhum setor cadastrado. Cadastre um.<br>';
			}
			
			$this->load->model('Cargo_model','',TRUE);
			if($this->Cargo_model->count_all() == 0){
				$data['message'] .= 'Nenhum cargo cadastrado. Cadastre um.<br>';
			}
			
			$this->load->model('Contato_model','',TRUE);
			if($this->Contato_model->count_all() == 0){
				$data['message'] .= 'Nenhum remetente cadastrado. Cadastre um.<br>';
			}
			
			$_SESSION['message'] = $data['message'];
			
			if($data['message'] != ''){
				redirect('documento/erro_tabelas/');
			}
    }
    
    function erro_tabelas(){
    	 
    	$data['titulo'] = 'Erro';
		
        $data['message'] = $_SESSION['message'];
        
		$data['link_back'] = '';

		$this->load->view('erro', $data);
    		
    }
    
}
?>
