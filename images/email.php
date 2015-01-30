<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['protocol']='smtp';
$config['smtp_host']='mail.geradox.com.br';
$config['smtp_crypto'] = 'ssl';
$config['smtp_port']= 465;
//$config['smtp_crypto'] = 'ssl';
//$config['smtp_port']= 465;
$config['starttls'] = TRUE;
$config['validate']= TRUE;
$config['smtp_user']='contato@geradox.com.br';
$config['smtp_pass']='vidas1234!@#$';
$config['mailtype']='html';
$config['charset'] = 'utf-8';
$config['wordwrap'] = 'TRUE';
$config['newline']="\r\n"; 