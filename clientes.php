<?php
include('src/head.php');

// echo "<div class='forma'>";

// echo "<h2>Agregar numeros telefonicos de mis Clientes </h2>";
// echo ":=";
// echo "<form id='form1' method='post' action='clientes.php' class='modal'>";
// echo "<div><label>Nombre de mi cliente:</label>";
// echo "<input type='text' name='nombre' required>";
// echo "</div>";


// echo "<div><label>Referencia de cliente</label>";
// echo "<input type='text' name='referencia' >";
// echo "</div>";

// echo "<div><label>Numero de Telefono:</label>";
// echo "<input type='tel' name='telefono' required>";
// echo "</div>";

// echo "<div><label></label>";
// echo "<input type='submit' name='GuardarCliente' class='btn btn-default' value='Guardar'>";
// echo "</div>";


// echo "</form>";

// echo "<a href='#form1' rel='modal:open'>Abrir Modal </a> ";

// echo "<label>* Puede importar directo a la base de datos a la tabla clientes; desde su gestor de base favorito.</label>";

// echo "</div>";



echo "<div id='clientes_lista'>";
echo "<table class='tabla2'>";
echo "<th>Nombre</th><th>Celular</th><th></th><th></th>";

$sql = "select * from clientes";    
echo $sql;
$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<tr>";
        echo "<td>".$f['nombre']."</td>";
        echo "<td>".$f['celular']."</td>";
        echo "<td>".$f['ref']."</td>";   
        echo "<td><a class='btn btn-default' style='height:15px;' href='clientes.php?cl=".$f['celular']."'> > </a></td>";   
             
        echo "</tr>";       
    }


echo "</tr>";
echo "</table>";

echo "</div>";


echo "<div id='clientes_lista2'>";


if (isset($_GET['cl'])){
    $sql = "SELECT * from clientes where celular='".$_GET['cl']."'";
    echo $sql;
	$rc = $conexion -> query($sql);
	if($f = $rc -> fetch_array())
	{
		$nombre = $f['nombre']; $ref=$f['ref'];
	
    echo "<form id='form1' method='post' action='clientes.php' >";
    echo "<label>Nombre de mi cliente:</label><br>";
    echo "<input type='text' name='nombre'  value='".$f['nombre']."' required>";
    echo "<br><br>";


    echo "<label>Referencia de cliente</label><br>";
    echo "<input type='text' name='referencia' value='".$f['ref']."' ><br>";
    echo "<br>";

    echo "<label>Numero de Telefono:</label><br>";
    echo "<input type='tel' name='telefono' value='".$_GET['cl']."' required><br>";
    echo "<br>";

    echo "<hr>";
    echo "<input type='submit' name='ActualizaCliente' class='btn btn-default' value='Actualizar'>";
    
    echo "";
    echo "</form>";
    }
} else {
    echo "<form id='form1' method='post' action='clientes.php'>";
    
    echo "<label>Nombre de mi cliente:</label><br>";
    echo "<input type='text' name='nombre' required>";
    echo "<br><br>";


    echo "<label>Referencia de cliente</label><br>";
    echo "<input type='text' name='referencia' ><br>";
    echo "<br>";

    echo "<label>Numero de Telefono:</label><br>";
    echo "<input type='tel' name='telefono' required><br>";
    echo "<br>";

    echo "<hr>";
    echo "<input type='submit' name='GuardarCliente' class='btn btn-default' value='Guardar'>";
    echo "";
    echo "</form>";
}

    echo "<a href='clientes.php' class='btn btndefault'>Reg. Cliente Nuevo</a>";


if (isset($_POST['GuardarCliente'])){
    $sql = "INSERT INTO clientes    (nombre, celular, ref)    VALUES    ('".$_POST['nombre']."', '".$_POST['telefono']."', '".$_POST['referencia']."')";
    if ($conexion->query($sql) == TRUE)
    {
        mensaje("Cliente ".$_POST['nombre']." guardado correctamente",'clientes.php');
    }
    else
    {
        mensaje("ERROR al guardar el cliente ".$sql,'clientes.php');
    }


}


if (isset($_POST['ActualizaCliente'])){
    $sql = "UPDATE clientes  SET nombre='".$_POST['nombre']."', ref='".$_POST['referencia']."' WHERE celular='".$_POST['telefono']."'";
    if ($conexion->query($sql) == TRUE)
    {
        mensaje("Cliente ".$_POST['nombre']." actualizado correctamente",'clientes.php');
    }
    else
    {
        mensaje("ERROR al actualizar el cliente ".$sql,'clientes.php');
    }
}
    
echo "</div>";











include('src/footer.php');


?>