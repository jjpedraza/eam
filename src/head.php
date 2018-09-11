<?php 	
require("seguridad.php"); 
require("src/config.php"); 
require("src/funciones.php"); 

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SMS-PRO PrymeCode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="src/estiloprincipal.css" />
    

    <script src="src/jquery331.js"></script>
</head>
<body>
<?php
echo "<div id='menu'>";
echo "<table style='width:100%;'>";
echo "<tr>";
echo "<td align=left width=200px>";
echo "<a href='index.php' title='Haz clic aqui para regresar a la pagina principal'>
<img src='img/logo2.png' style='width:200px;'></a>";
echo "</td>";
echo "<td>";
if (isset($sms_user)){
    echo "Bienvenido ".$sms_user;
}
echo "</div> | <a href='logout.php' title='haz clic aqui para salir'>Salir </a>";
echo "</td>";

echo "</tr>";
echo "</table>";
echo "</div>";

?>