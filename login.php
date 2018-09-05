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
<div id='login' 	>
	<h1> Debes identificarte: </h1>
	<form action='login.php' method="post">	
	<?php
		session_start();		
	?>
		<input type='text' name='sms_username' placeholder='Escriba tu usuario (correo)' required>
		<input type="password" name="nip" placeholder='Escriba tu password' required>

		<input type='submit' class='btn btn-default' value='ENTRAR' name='login'>
	</form>


<?php
if (isset($_POST['login'])){
	$id = $_POST['sms_username'];
	$nip = $_POST['nip'];

	$sql="SELECT * FROM usuarios WHERE (correo='".$id."' and password='".$nip."')";
	// echo $sql;
	$resultado = $conexion -> query($sql);
	if($fila = $resultado -> fetch_array())	
	{
		if ($fila['estado']==0)
			{
				
				session_start();
				$_SESSION['sms_user']=$fila['correo'];	
			
				global $sms_user;
				$sms_user = $fila['correo'];	

				
				header('location:index.php');	
			}

		else {

				mensaje('Error inesperado','index.php');
			}

				

		} 
	else 
		{
			// historia($id,'Acceso a la plataforma fallida con nip '.$nip); mensaje("Error en el usuario y nip",'index.php');
			mensaje("Password incorrecto",'login.php');
		} 
		

} else {

}
?>

</div>


</body>
</html>