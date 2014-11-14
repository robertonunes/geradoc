<?php

$header = '<table width="100%" style="vertical-align: bottom;">
			<tr>
			<td align="center">'.$cabecalho.'</td>
			</tr>
			</table>';

if($objeto->carimbo == 'S'){
	$header .= '<div style="text-align: right; margin-top:-105px; margin-right:-73px; font-size: 10pt; color: #555; line-height:200%;">
						<img src="./images/carimbo_aesp.png" width="80px"/>
				</div>';
}
/*
$content = '<div class="conteudo">
				'.$objeto->layout.'
				'.$objeto->tipoNome.' Nº '.$objeto->numero.'/'.$objeto->ano.' - '.$caminho_remetente.'
				<div class="data"><br>Fortaleza, '.$objeto->data.'.</div>
				<div class="destinatario">
				<p>
					'.$objeto->para.'
				</p>
				<p>
					<b>Assunto:</b> '.ucfirst($objeto->assunto).'
					<br><strong>Referência:</strong> '.$objeto->referencia.'
				</p>
			</div>
			<div class="redacao">
				<p>'.$objeto->redacao.'</p>
			</div>
			'.$objeto->assinatura = $objeto->remetNome . '<br>'.$objeto->remetCargoNome.' '.$objeto->remetSetorArtigo.' '.$objeto->remetSetorSigla.'';
*/

$content = '<div class="conteudo">
				'.htmlspecialchars_decode($objeto->layout).'
			</div>';

$footer = '
		<table width="100%" style="vertical-align: top;font-family:\'Times New Roman\',Times,serif; font-size: 11px;">
			<tr>
				<td align="center" colspan="2">
					'.$rodape.'
				</td>
			</tr>
			<tr>
				<td style="font-size: 9px">'.$documento_identificacao.'
				</td>	
				<td align="right">página {PAGENO} de {nbpg}</td>
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

$stylesheet = file_get_contents(base_url().'css/pdf.css'); // external css

$mpdf->mirrorMargins = 0; // Use different Odd/Even headers and footers and mirror margins

$mpdf->SetHTMLHeader(utf8_encode($header));
//if($objeto->tipoID != 4)
	$mpdf->SetHTMLFooter($footer);

$mpdf->debug = true;
//$mpdf->keep_table_proportions = false;

$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($content);

if($filename == null){
	$filename = $objeto->setorSigla.'_'.$objeto->tipoSigla.'_'.substr($objeto->data, -4).'_'.$objeto->numero.'.pdf';
}

//echo htmlspecialchars_decode($content);

$mpdf->Output($filename, 'I');

exit;
?>
