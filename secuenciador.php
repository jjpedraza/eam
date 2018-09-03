
    
<?php

include('src/head.php');

echo "<form action='secuenciador.php' method='post'>";
echo "<h3>Generador de ramas telefonicas</h3>";

echo "<div><label>Lada</label><input type='text' size=3 placeholder='000' name='lada' min=1 max=999 maxlength=3></div>";
echo "<div><label>Rama</label><input type='text' size=3 placeholder='000' name='rama' min=1 max=999 maxlength='3'></div>";

echo "<div><input type='submit' value='Generar' class='btn btn-default' name='genera'></div>";
echo "</form>";

if (isset($_POST['genera'])){
    $lada = $_POST['lada']; $rama = $_POST['rama']; $celular = "";
    $Array=permutations("0123456789",4);    
    $total = COUNT($Array); $porcentaje =0;
    echo "<table class='tabla'><th></th><th>Numero</th><th></th><th></th>";

    FOR($i=0 ; $i < COUNT($Array) ; $i++) { 
            echo "<tr>";
            echo "<td>".$i."</td>";
            $celular = $lada.$rama. $Array[$i];
            echo  "<td>".$celular."</td>";
            echo "<td>";


            $sql = "INSERT INTO celulares(celular) VALUES ('".$celular."')";
            if ($conexion->query($sql) == TRUE)
                { echo "OK";}
            else { echo "X <br><label>".$sql."</label>";}
        
            echo "</td>";

            echo "<td>";
            $porcentaje = $i / $total * 100;
            echo "<b>".$porcentaje."%</b>";

            echo "</td>";
            echo "</tr>"; 
            sleep(0.5);
    } 
    echo "</table>";
    echo "<h1>Total de Permutaciones: ". COUNT($Array)."</h1>";

}


include('src/footer.php');

?>