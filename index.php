<?php
include('src/head.php');

echo "<lu id ='menuprincipal'>";

echo "<li>";
echo "<table><tr>";
echo "<td><img src='img/clientes.png' style='width:80px;'></td>";
echo "<td><a href='clientes.php'>Agregar numeros de mis clientes </a></td>";
echo "</tr></table>";
echo "</li>";




echo "</lu>";



echo "<div id='modular' class='ventana' style='width:90%; height:400px; overflow:auto;'><table class='tabla'><th>Lada</th><th>Reg</th>";
$sql = "
SELECT DISTINCT
	SUBSTRING(celular, 1, 3 ) as Lada
    -- ,(SELECT  GROUP_CONCAT(clave) FROM  ladas WHERE  clave like  CONCAT('%',Lada,'%'))	as Lugar
    ,(select count(*) from celulares where celular like CONCAT('',Lada,'%')) as n
	
FROM
	celulares 

order by n desc
";
 	$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<tr>";
        echo "<td>".$f['Lada']."</td>";
        //echo "<td><label style='font-size:7pt'>";
        //echo str_replace(",", ", ", $f['Lugar']);
        //echo "</label></td>";
        echo "<td>".$f['n']."</td>";
        
        echo "</tr>";
    }
echo "</table></div>";


include('src/footer.php');


?>