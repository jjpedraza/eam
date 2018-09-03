
<?php
require ("unica/config.php");
require ("unica/funciones.php");
 $sql = "select * from sms_dispositivos where estado=1 order by estado";
 	$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<article>"; 
        echo "<table><tr><td>";   

        echo "<a href='' title='IMEI:".$f['imei']." | ".$f['nombre']."'>";    
        
        echo "<img class='SMSiconMob' src='icon/celular.png'></td><td valign=top align=left>";     
        echo "</a>";   
        echo "<b class=' tchico'>".$f['nombre']."</b>";
        echo "<label class='tchico' style='color:white;'>".$f['descripcion']."</label><br>";
        echo "<b class='tchico' style='background-color:rgb(255,255,255,0.5); color:#00A1A2; border-radius:2px; padding:4px; ' >  ".$f['imei']."  </b>";


        echo "<label style='color:white'>".SMS_DispositivoAct0($f['imei']).", <b class='ejecutandose'>".SMS_DispositivoAct1($f['imei'])."</b></label>";
        echo "</td>";

        echo "<tr></table>";
        echo "<hr>";
        
            echo "<a href='sms.php?stop=".$f['imei']."' style='color:white;'>Detener</a>";
        


        echo "</article>";
    
    }
    echo "</table>";

function SMS_DispositivoAct($imei){
require("unica/config.php");
    $sql = "select * from sms where dispositivo = '".$imei."' ";    
    $rc= $conexion -> query($sql);
    if($f = $rc -> fetch_array())
	{
	    return $f['estado'];       
    }
    else {return "x";}

}


function SMS_DispositivoAct0($imei){
require("unica/config.php");
    $sql = "select count(*) as n from sms where dispositivo = '".$imei."' and estado='0' ";    
    $rc= $conexion -> query($sql);
    if($f = $rc -> fetch_array())
	{
	    return $f['n'];       
    }
    else {return 0;}

}


function SMS_DispositivoAct1($imei){
require("unica/config.php");
    $sql = "select count(*) as n from sms where dispositivo = '".$imei."' and estado='1' ";    
    $rc= $conexion -> query($sql);
    if($f = $rc -> fetch_array())
	{
	    return $f['n'];       
    }
    else {return 0;}

}

?>

