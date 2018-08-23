<?php
//AUTORIZACION PARA ADMINISTRADOR
session_start();	
if (isset($_SESSION['sms_user'])){
		$sms_user = $_SESSION['sms_user'];
		require("config.php");  
		$sql = "SELECT * FROM usuarios WHERE correo='".$sms_user."'";
		$rc= $conexion -> query($sql);if($f = $rc -> fetch_array())
		{
			if ($f['estado']<>'0') {// si el campo edo, tiene algo expulsar
		
				session_destroy();
				$sms_user="";
				header("location:logout.php");		
			}else {
				//header("location:index2.php");		
			}

		}
		
}
else
{		
	$msg="Se requiere estar IDENTIFICADO<br>";
	header("location:login.php");		
	
}


?>