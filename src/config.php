<?php
//PARAMETROS INICIALES




$paginacion= 20;
	date_default_timezone_set('Mexico/General');
	mb_internal_encoding('UTF-8');
	mb_http_output('UTF-8');





	
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = '';
	$dbname = 'publicsms';

	if (function_exists('mysqli_connect')) {
		//mysqli está instalado
			//echo 'Si';
			$conexion = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
			$acentos = $conexion->query("SET NAMES 'utf8'"); // para los acentos
			global $conexion;
		}else{
			//mensaje("Hay un problema con la extension de mysqli",'');
			echo phpinfo();
		}




//PARAMETROS DE PREFERENCIA


 	$fecha = date('Y-m-d');
	$hora =  date ("H:i:s");
	
	global $fecha, $hora;


?>