
    
<?php

require("src/seguridad.php"); 
require("src/config.php"); 
require("src/funciones.php"); 


if (isset($_POST['lada']) and isset($_POST['rama'])){
    // sleep(5);
    $lada = $_POST['lada']; $rama = $_POST['rama']; $celular = "";
    $Array=permutations("0123456789",4);    
    $total = COUNT($Array); $porcentaje =0;
    // echo "<table class='tabla'><th></th><th>Numero</th><th></th><th></th>";
    $errores = "";
    $ok = 0; $x=0; $r="";
    FOR($i=0 ; $i < COUNT($Array) ; $i++) { 
            // echo "<tr>";
            // echo "<td>".$i."</td>";
            $celular = $lada.$rama. $Array[$i];
            // echo  "<td>".$celular."</td>";
            // echo "<td>";


            $sql = "INSERT INTO celulares(celular) VALUES ('".$celular."')";
            if ($conexion->query($sql) == TRUE)
                {
                    //  echo "OK";
                    $ok = $ok + 1;
                }
            else { 
                $errores = $errores. "Error al agregar el ".$celular."<label>".$sql."</label><br>";
                $x = $x + 1;
            }
        
            $porcentaje = $i / $total * 100;
    
    
            sleep(0.5);
    } 
    
    
    if ($errores<>""){
        echo "<p style='color:red'>Hubo los siguientes errores:<br>".$errores."</p>";
    } else {
        echo "<p>Proceso terminado con exito:  ".$ok." telefonos agregados, de ".COUNT($Array)." </p>";
    }

} else {
    echo "Valores no recibidos";
}

echo "Resultado";


?>