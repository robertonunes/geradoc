<?php

$postedValue = htmlspecialchars($objeto->redacao);

$stylesheet = file_get_contents(base_url().'css/pdf.css');

$html = '<div class="conteudo">';
$isOrdinary = ($objeto->tipoID == 1 or $objeto->tipoID == 2) ? TRUE : FALSE;
if($isOrdinary){
			// linhas acrescentadas em fevereiro de 2013 para retirar os "<br>" do campo "para"
			$pedacos = explode("<br />", $objeto->para);
			foreach ($pedacos as $key => $value) {
				if(strlen($pedacos[$key]) < 2){
					unset($pedacos[$key]);
				}	
				$texto_para = implode("<br />", $pedacos);
			}
			$objeto->para = $texto_para;
			// fim

	$html .= $objeto->tipoNome.' Nº '.$objeto->numero.'/'.$objeto->ano.' - '.$caminho_remetente.'

			<div class="data"><br> Fortaleza, '.$objeto->data.'.</div>

					<div class="destinatario">
					<p>'.$objeto->para.'</p>
							<p>
							<b>Assunto:</b> '.ucfirst($objeto->assunto);

}
else if($objeto->tipoID == 3 or $objeto->tipoID == 5){//Despacho ou Parecer
	if(isset($data_despacho))
		$date = new DateTime(str_replace('-','/',$data_despacho));
	else if(isset($objeto->data_despacho))
		$date = new DateTime(str_replace('-','/',$objeto->data_despacho));
	$tmpl = array ('table_open'  => '<table width="100%" style="border: solid 1px black; background-color: #FFF; color: black; border-collapse: collapse;">');
	$this->table->set_template($tmpl);
	$tipo_do_documento_aesp = $objeto->tipoID == 3 ? 'DESPACHO' : 'PARECER';
	$this->table->add_row("<center><b>$tipo_do_documento_aesp Nº ".$objeto->numero.'/'.$objeto->ano.'</b></center>');
	$html .= $this->table->generate();
	$html .= '<br>';
	$this->table->clear();
	$tmpl = array ('table_open'  => '<table class="despacho_head">');
	$this->table->set_template($tmpl);
	$this->table->add_row('<b>Nº. do processo: </b>'.$despacho_head['num_processo'],'<b>De: </b>'.$despacho_head['de']);
	$this->table->add_row('<b>Interessado: </b>'.$despacho_head['interessado']     ,'<b>Para: </b>'.$despacho_head['para']);
	$this->table->add_row('<b>Assunto: </b>'.$objeto->assunto     ,'<b>Data do despacho: </b>'.$date->format('d/m/Y'));
	$html .= $this->table->generate();
	$html .= '<br>';
}
if($objeto->referencia and $isOrdinary){

	$html .= ' <br><strong>Referência:</strong> '.$objeto->referencia;

}

if(!$objeto->assinatura){
	$objeto->assinatura = $objeto->remetNome . '<br>'.$objeto->remetCargoNome.' '.$objeto->remetSetorArtigo.' '.$objeto->remetSetorSigla.'';
}





if($objeto->tipoID == 3 or $objeto->tipoID == 5){
	$html .= '
		</p>
		</div>
		<div class="redacao" style="border: solid 1px black; height: 1000px; padding-left: 10px; padding-right: 10px;">
		<p style="margin: 10px;">'.htmlspecialchars_decode($postedValue).'	
			<br>
			<div class="remetente">
			<br>'.$objeto->assinatura.'
			</div>		
		</p>
		</div>';
}else{
	$html .= '
		</p>
		</div>
		<div class="redacao">
		<p>'.htmlspecialchars_decode($postedValue).'</p>
		</div>';
	
	if($objeto->tipoID != 4){
		$html .= '
			<br>	
			<div class="remetente">
			<br>'.$objeto->assinatura.'
			</div>';
	}

}



$header = '
		<table width="100%" style="vertical-align: bottom;">
		<tr>
		<td align="center">'.$cabecalho.'</td>
		</tr>
		</table>
		';

if($objeto->carimbo == 'S'){
	$header .= '<div style="text-align: right; margin-top:-105px; margin-right:-73px; font-size: 10pt; color: #555; line-height:200%;">
						<img src="./images/carimbo_aesp.png" width="80px"/>
				</div>';
}



$footer = '
		<table width="100%" style="vertical-align: top;font-family:\'Times New Roman\',Times,serif; font-size: 11px;">
		<tr>
		<td align="center">
		'.$rodape.'
		</td>
		</tr>
		<tr>
		<td align="right">{PAGENO}</td>
		</tr>
		</table>
		';




//MPDF

include("scripts/mpdf57/mpdf.php");

$mode = 'pt';
$format = 'A4';
$default_font_size = 12;
$default_font = 'times';
$margin_top = 35;
$margin_right = 20;
$margin_bottom = 30;
$margin_left = 25;
$margin_header = 8;
$margin_footer  = 10;
$orientation = '';


//$mpdf=new mPDF ($mode, $format, $default_font_size, $default_font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer, $orientation);

$mpdf=new mPDF ($mode, $format, $default_font_size, $default_font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer, $orientation);

$mpdf->mirrorMargins = 0; // Use different Odd/Even headers and footers and mirror margins

$mpdf->SetHTMLHeader(utf8_encode($header));
if($objeto->tipoID != 4)
	$mpdf->SetHTMLFooter($footer);

$mpdf->debug = true;
$mpdf->WriteHTML($html);

// define um nome para o arquivo PDF
if($filename == null){
	$filename = $objeto->setorSigla.'_'.$objeto->tipoSigla.'_'.substr($objeto->data, -4).'_'.$objeto->numero.'.pdf';
}

//echo $html;

$mpdf->Output($filename, 'I');

exit;
?>
