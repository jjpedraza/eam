<?php
//PARAMETROS INICIALES




$paginacion= 20;
	date_default_timezone_set('Mexico/General');
	mb_internal_encoding('UTF-8');
	mb_http_output('UTF-8');





	
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = '';
	$dbname = 'publisms';




//PARAMETROS DE PREFERENCIA


 	$fecha = date('Y-m-d');
	$hora =  date ("H:i:s");
	
	global $fecha, $hora;


?>