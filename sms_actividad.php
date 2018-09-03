
<?php
require ("unica/config.php");
require ("unica/funciones.php");

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
    				echo "<td style='$StOK' title='".$f['Fecha']." | ".$f['Mensaje'].", Creada por ".nitavu_nombre($f['Admin'])."'>".$f['QBrigada']."</td>";
    				echo "<td style='$StOK' title='Total de mensajes: ".$f['Total']."'>".$f['Porcentaje']."</td>";
		    echo "</tr>";
    	} else {
			echo "<tr style='$St'>";
    				echo "<td style='$St' title='".$f['Fecha']." | ".$f['Mensaje'].", Creada por ".nitavu_nombre($f['Admin'])."'>".$f['QBrigada']."</td>";
    				echo "<td style='$St' title='Total de mensajes: ".$f['Total']."(Sin entregar: ".$f['Faltantes'].", Entregados: ".$f['Avance'].")'>".$f['Porcentaje']."</td>";
		    echo "</tr>";
    	}
    	

    }
    echo "</table>";


?>

