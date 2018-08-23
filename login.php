<?php 	require("src/funciones.php"); ?>
<?php 	require("src/config.php"); ?>
<?php // error_reporting(E_ALL ^ E_NOTICE);
?>
<!DOCTYPE html>
<head>
	<title>IDENTIFICATE</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9" >
	<meta charset="utf-8" />

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="src/estiloprincipal.css">
	


</head>
<body >
<?php

?>
<div id='login'	>
	<form action='login.php' method="post">	
	<?php
		session_start();		
	?>
		<input type='text' name='sms_username' placeholder='Escriba su correo' required>
		<input type="password" name="nip" placeholder='Escriba su NIP' required>

		<button class='btn btn-default'>
			Entrar			
		</button>
	</form>


<?php
if (isset($_POST['username'])){
	$id = $_POST['username'];
	$nip = $_POST['nip'];

	$sql="SELECT * FROM empleados WHERE (nitavu='".$id."' and nip='".$nip."')";
	$resultado = $conexion -> query($sql);
	if($fila = $resultado -> fetch_array())	
		{
		if ($fila['estado']=='')
		{
			
			session_start();
			$_SESSION['user']=$fila['nitavu'];	
		
			global $nitavu;
			$nitavu = $fila['nitavu'];	

			historia($nitavu,'Acceso a la plataforma<br>'.detectar().'');
			header('location:index.php');	
		}

		else {
			//session_start();
			//$_SESSION['user']=$fila['nitavu'];	
		
			//global $nitavu;
			//$nitavu = $fila['nitavu'];	

			historia($fila['nitavu'],'<b class="alerta">Acceso Denegado. </b> <br> estado del empleado = '.$fila['estado'].'<br>Desde: <br>'.detectar().'');	
			mensaje('Error inesperado','index.php');
		}

				

		} 
	else 
		{historia($id,'Acceso a la plataforma fallida con nip '.$nip); mensaje("Error en el usuario y nip",'index.php');} 
		

}
?>

</div>


</body>
</html>