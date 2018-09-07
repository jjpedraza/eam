
    
<?php

require("src/seguridad.php"); 
require("src/config.php"); 
require("src/funciones.php"); 


if ( isset($_GET['valor']) ){
    // sleep(5);
    // $lada = $_POST['lada']; $rama = $_POST['rama']; $celular = "";
    $cantidad = 0;
    $sql = "select count(*) as n from celulares where celular like '".$_GET['valor']."%'";
    // echo $sql;
	$rc= $conexion -> query($sql);
	if($f = $rc -> fetch_array())
	{
        if ($f['n']==0){
            echo "<b style='color: green;'>Hay ".$f['n']." celulares con ".$_GET['valor']."</b > ";
        } else {
            echo "Hay <b>".$f['n']."</b > celulares con ".$_GET['valor']." ";
        }
	}
	

}


?>