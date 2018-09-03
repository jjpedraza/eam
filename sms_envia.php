<?php 
require("unica/config.php"); require("unica/funciones.php");

$sql = "select empleados.telefono_movil, nombre, nitavu from empleados where dpto=55";
//echo $sql;
$rawdata = array(); //creamos un array
$i=0;
$r2 = $conexion -> query($sql);
while($f = $r2 -> fetch_array())
{ $rawdata[$i] = $f;
    $i++;

}
echo "".json_encode($rawdata);




?>