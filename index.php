<?php
include('src/head.php');

echo "<div class='ventana' ><lu id ='menuprincipal'>";

echo "<li>";
echo "<table><tr>";
echo "<td><a href='clientes.php'><img src='img/clientes.png' ></a></td>";
echo "<td class='pc'><a href='clientes.php'>Agregar numeros de mis clientes </a></td>";
echo "</tr></table>";
echo "</li>";

echo "<li>";
echo "<table><tr>";
echo "<td><a href='secuenciador.php'><img src='img/tel.png' ></a></td>";
echo "<td class='pc'><a href='secuenciador.php'>Generador de Ramas Telefonicas </a></td>";
echo "</tr></table>";
echo "</li>";


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

echo "</lu></div>";


echo "<div class='ventana' >";

//Write table on result sms
$sql = "
SELECT DISTINCT(Brigada) AS QBrigada,
(select count(*) from sms where Brigada=QBrigada) as Total,
(select count(*) from sms where Brigada=QBrigada and estado='0') as Faltantes,
(select count(*) from sms where Brigada=QBrigada and estado='1') as  Avance,
(
	CONCAT(

		ROUND(
		(
			
				(100 / (select count(*) from sms where Brigada=QBrigada) ) * 
				(select count(*) from sms where Brigada=QBrigada and estado='1') 
			
		)

	)
	, '%')
	
) as Porcentaje,

(select sms.mensaje from sms where Brigada=QBrigada limit 1) as Mensaje,
(select sms.fecha from sms where Brigada=QBrigada limit 1) as Fecha,
(select sms.envia from sms where Brigada=QBrigada limit 1) as Admin




FROM sms


ORDER BY  Porcentaje DESC

-- Porcentaje = (total -avance)/total * 100
";
	$StOK='background-color:auto; color:#A2C30D;cursor:pointer;';
	$St='background-color:auto; color:auto; font-size:14pt;cursor:pointer;';

    echo "<table class='tabla'>";
    echo "<th>Brigada</th><th>Avance</th>";
    $r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
    	if ($f['Porcentaje']=='100%'){
    		echo "<tr style='$StOK'>";
    				echo "<td style='$StOK' title='".$f['Fecha']." | ".$f['Mensaje'].", Creada por ".$f['Admin']."'>".$f['QBrigada']."</td>";
    				echo "<td style='$StOK' title='Total de mensajes: ".$f['Total']."'>".$f['Porcentaje']."</td>";
		    echo "</tr>";
    	} else {
			echo "<tr style='$St'>";
    				echo "<td style='$St' title='".$f['Fecha']." | ".$f['Mensaje'].", Creada por ".$f['Admin']."'>".$f['QBrigada']."</td>";
    				echo "<td style='$St' title='Total de mensajes: ".$f['Total']."(Sin entregar: ".$f['Faltantes'].", Entregados: ".$f['Avance'].")'>".$f['Porcentaje']."</td>";
		    echo "</tr>";
    	}
    	

    }
    echo "</table>";

echo "<div><hr>";



// echo "<div id='modular2' class='ventana' >";
echo "<div  class='ventana' ><table class='tabla'><th>Lada</th><th>Reg</th>";
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


echo "<div  class='ventana' ><table class='tabla'><th>Lada</th><th>Rama</th>";

$sql = "
select  DISTINCT SUBSTRING(celular, 1, 3 ) as Lada,

SUBSTRING(celular, 4, 3 ) as Rama



from celulares

";
 	$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<tr>";
        echo "<td>".$f['Lada']."</td>";
        //echo "<td><label style='font-size:7pt'>";
        //echo str_replace(",", ", ", $f['Lugar']);
        //echo "</label></td>";
        echo "<td>".$f['Rama']."</td>";
        
        echo "</tr>";
    }
echo "</table></div> ";




include('src/footer.php');


?>

<script >
// function CargaAvancesSMS(){
//         $.get("sms_actividad.php", {}, function(htmlexterno){$("#SMS_actividad").html(htmlexterno);});
//         console.log("Consultando CargaAvancesSMS");
 
// }


// function CargaDispositivos(){
//         $.get("sms_actividadd.php", {}, function(htmlexterno){$("#autorizados").html(htmlexterno);});
//         console.log("Consultando Actvidad Dispositivos");
 
// }
 

setInterval(CargaAvancesSMS,5000);
// setInterval(CargaDispositivos,4000);


</script>