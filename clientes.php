<?php
include('src/head.php');

echo "<div class='forma'>";

echo "<h2>Agregar numeros telefonicos de mis Clientes </h2>";
echo ":=";
echo "<form method='post' action='clientes.php'>";
echo "<div><label>Nombre de mi cliente:</label>";
echo "<input type='text' name='nombre'>";
echo "</div>";


echo "<div><label>Referencia de cliente</label>";
echo "<input type='text' name='referencia'>";
echo "</div>";

echo "<div><label>Numero de Telefono:</label>";
echo "<input type='tel' name='telefono'>";
echo "</div>";

echo "<div><label></label>";
echo "<input type='submit' name='cliente' class='btn btn-default' value='Guardar'>";
echo "<a href='' class='btn btn-secundario  '>Importar</a>";
echo "</div>";


echo "</form>";
echo "</div>";
include('src/footer.php');


?>