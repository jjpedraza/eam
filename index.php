<?php
include('src/head.php');

echo "<lu id ='menuprincipal'>";

echo "<li>";
echo "<table><tr>";
echo "<td><a href='clientes.php'><img src='img/clientes.png' ></a></td>";
echo "<td class='pc'><a href='clientes.php'>Agregar numeros de mis clientes </a></td>";
echo "</tr></table>";
echo "</li>";

// echo "<li>";
// echo "<table><tr>";
// echo "<td><a href='secuenciador.php'><img src='img/tel.png' ></a></td>";
// echo "<td class='pc'><a href='secuenciador.php'>Generador de Ramas Telefonicas </a></td>";
// echo "</tr></table>";
// echo "</li>";


echo "<li>";
echo "<table><tr>";
echo "<td><a href='autorizar.php'><img src='img/dispositivo.png' ></a></td>";
echo "<td class='pc'><a href='autorizar.php'>Autorizar dispositivos </a></td>";
echo "</tr></table>";
echo "</li>";

echo "<li>";
echo "<table><tr>";
echo "<td><a href='sms.php'><img src='img/sms.png' ></a></td>";
echo "<td class='pc'><a href='sms.php' class='pc'>Crear campaña</a></td>";
echo "</tr></table>";
echo "</li>";

// echo "<li>";
// echo "<table><tr>";
// echo "<td><a href='monitor.php'><img src='img/monitor.png' ></a></td>";
// echo "<td><a href='monitor.php' class='pc'>Monitorear</a></td>";
// echo "</tr></table>";
// echo "</li>";

echo "</lu>";


echo "<div class='ventana' id='SMS_actividad'>";
echo "<div>";



// echo "<div id='modular' class='ventana' style='width:90%; height:400px; overflow:auto;'>";
// echo "<div id='modular' class='ventana' style='width:40%; height:400px; overflow:auto;'><table class='tabla'><th>Lada</th><th>Reg</th>";
// $sql = "
// SELECT DISTINCT
// 	SUBSTRING(celular, 1, 3 ) as Lada
//     -- ,(SELECT  GROUP_CONCAT(clave) FROM  ladas WHERE  clave like  CONCAT('%',Lada,'%'))	as Lugar
//     ,(select count(*) from celulares where celular like CONCAT('',Lada,'%')) as n
	
// FROM
// 	celulares 

// order by n desc
// ";
//  	$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
//     {
//         echo "<tr>";
//         echo "<td>".$f['Lada']."</td>";
//         //echo "<td><label style='font-size:7pt'>";
//         //echo str_replace(",", ", ", $f['Lugar']);
//         //echo "</label></td>";
//         echo "<td>".$f['n']."</td>";
        
//         echo "</tr>";
//     }
// echo "</table></div>";


// echo "<div id='modular' class='ventana' style='width:40%; height:400px; overflow:auto;'><table class='tabla'><th>Lada</th><th>Rama</th>";

// $sql = "
// select  DISTINCT SUBSTRING(celular, 1, 3 ) as Lada,

// SUBSTRING(celular, 4, 3 ) as Rama



// from celulares

// ";
//  	$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
//     {
//         echo "<tr>";
//         echo "<td>".$f['Lada']."</td>";
//         //echo "<td><label style='font-size:7pt'>";
//         //echo str_replace(",", ", ", $f['Lugar']);
//         //echo "</label></td>";
//         echo "<td>".$f['Rama']."</td>";
        
//         echo "</tr>";
//     }
// echo "</table></div> </div>";




include('src/footer.php');


?>

<script >
function CargaAvancesSMS(){
        $.get("sms_actividad.php", {}, function(htmlexterno){$("#SMS_actividad").html(htmlexterno);});
        console.log("Consultando CargaAvancesSMS");
 
}


// function CargaDispositivos(){
//         $.get("sms_actividadd.php", {}, function(htmlexterno){$("#autorizados").html(htmlexterno);});
//         console.log("Consultando Actvidad Dispositivos");
 
// }
 

setInterval(CargaAvancesSMS,1000);
// setInterval(CargaDispositivos,4000);


</script>