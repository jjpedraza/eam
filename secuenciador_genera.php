
    
<?php

require("src/seguridad.php"); 
require("src/config.php"); 
require("src/funciones.php"); 


if (isset($_POST['lada']) and isset($_POST['rama'])){
    sleep(5);
    // $lada = $_POST['lada']; $rama = $_POST['rama']; $celular = "";
    // $Array=permutations("0123456789",4);    
    // $total = COUNT($Array); $porcentaje =0;
    // // echo "<table class='tabla'><th></th><th>Numero</th><th></th><th></th>";
    // $errores = "";
    // FOR($i=0 ; $i < COUNT($Array) ; $i++) { 
    //         // echo "<tr>";
    //         // echo "<td>".$i."</td>";
    //         $celular = $lada.$rama. $Array[$i];
    //         // echo  "<td>".$celular."</td>";
    //         // echo "<td>";


    //         $sql = "INSERT INTO celulares(celular) VALUES ('".$celular."')";
    //         if ($conexion->query($sql) == TRUE)
    //             {
    //                 //  echo "OK";
    //             }
    //         else { 
    //             $errores = $errores. "Error al agregar el ".$celular."<label>".$sql."</label><br>";
    //         }
        
    //         $porcentaje = $i / $total * 100;
    
    
    //         sleep(0.5);
    // } 
    
    // echo "<p>Proceso terminado con exito:  ". COUNT($Array)." telefonos agregados </p>";
    // if ($errores<>""){
    //     echo "<p style='color:red'>Hubo los siguientes errores:<br>".$errores."</p>";
    // }

}

echo "Resultado";


?>