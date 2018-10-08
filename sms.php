

<?php 

include('src/head.php');



echo "<a rel='modal:open'  href='#sms_brigada' class='btn btn-default ventana'>Crear Campaña</a>";
    	
echo "<div class='ventana'>";
$sql = "SELECT DISTINCT
( brigada ) as Brig,
(select count(*) from sms where sms.estado = 0 and brigada = Brig) as SentNOT,
(select count(*) from sms where sms.estado = 1 and  brigada = Brig) as SentOK, estado

FROM
sms order by SentNOT";
echo "<table class='tabla'><th>Camapaña</th><th>Por Enviar</th><th>Enviados</th>";
    $r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        if ($f['estado']=='2'){
            echo "<tr style='color:red;'>";
        } else {
            echo "<tr>";
        }
        
        echo "<td>".$f['Brig']."</td>";
        echo "<td>".$f['SentNOT']."</td>";
        echo "<td>".$f['SentOK']."</td>";
        if ($f['estado']<>'2'){
        echo "<td><a href='sms.php?stop=".$f['Brig']."' class='btn btn-default'>X</a></td>";
        }
        
        echo "</tr>";
    }
echo "</table>";

echo "</div>";



echo "<form id='sms_brigada' action='sms.php?brignew' method='post' class='modal'>";
echo "<div><input type='text' name='brigada' id='brigada' placeholder='Nombre de la campaña' required></div>";
echo "<div><select name='tipo' id='tipo' required>";
echo "<option value='TODOS'>Todos los clientes</option>";
$sql="select DISTINCT(clientes.ref)

from clientes";
$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<option value='".$f['ref']."'>".$f['ref']."</option>";
    }
echo "</select></div>";

echo "<span>";
echo "<label  id='sms_label'>Mensaje:</label>";
echo "<textarea  name='sms_mensaje' id='sms_mensaje' onkeypress='ValidaSMS();' style='height:200px;'  maxlength='150'></textarea>";
echo "</span>";



echo "<div><input type='submit' value='Enviar' name='BtnEnviar' class='btn btn-secundario' ></div>";
echo "</form>";










if (isset($_POST['BtnEnviar'])){
    //insertamos a la tabla sms segun corresponda
    if ($_POST['tipo']=='TODOS'){
        $sql='select InsertarSMSlite("'.$_POST['sms_mensaje'].'", "TODOS", "'.$_POST['brigada'].'")';
        $r = $conexion -> query($sql);
        if ($conexion->query($sql) == TRUE) {
            mensaje("Se han agreado correctamente la campaña ".$POST['brigada'],'sms.php');
        } else {
            mensaje("ERROR: hubo un problema = ".$sql,'');
        }

    }
    else {
        $sql='select InsertarSMSlite("'.$_POST['sms_mensaje'].'", "'.$_POST['tipo'].'", "'.$_POST['brigada'].'")';
        $r = $conexion -> query($sql);
        if ($conexion->query($sql) == TRUE) {
            mensaje("Se han agreado correctamente la campaña ".$_POST['brigada'],'sms.php');
        } else {
            mensaje("ERROR: hubo un problema = ".$sql,'');
        }

    }
       



}
if (isset($_GET['stop'])){
    
   
    $comentarios = "Detendido por ".$sms_user."[".$fecha.", ".$hora."]";
    $sql="UPDATE sms SET comentarios='".$comentarios."', estado='2' WHERE brigada='".$_GET['stop']."'";
    $r = $conexion -> query($sql);
    if ($conexion->query($sql) == TRUE) {
        // historia($nitavu,$comentarios1);        
        mensaje("Campaña ".$_GET['stop']." cancelada correctamente",'sms.php');
    } else{
        mensaje("Hubo un error al intentar cancelar: ".$sql,'sms.php');
    }

}


// if (isset($_POST['BtnAutDisp'])){
//     if ($nivel == 1){
//     $imei = $_POST['imei'];
//     $descripcion = $_POST['descripcion'];
//     $sql="UPDATE sms_dispositivos SET descripcion='".$descripcion."', estado='1' WHERE imei='".$imei."'";
//     $r = $conexion -> query($sql);
//     if ($conexion->query($sql) == TRUE) {
//         historia($nitavu,"Autorizo el dispositivo ".$imei." para gestionar el envio de sms de la plataforma");
//         mensaje("Dispositivo ".$imei." autorizado",'sms.php');
//     } else{
//         mensaje("Hubo un error al intentar autorizar el dispositivo: ".$sql,'sms.php');
//     }
//     }else {mensaje("ERROR: No autorizado para autorizar dispositivos",'sms.php');}
// }
   
















//require("unica/config.php"); require("unica/funciones.php");

// $sql = "select empleados.telefono_movil, nombre, nitavu from empleados where dpto=55";
// //echo $sql;
// $rawdata = array(); //creamos un array
// $i=0;
// $r2 = $conexion -> query($sql);
// while($f = $r2 -> fetch_array())
// { $rawdata[$i] = $f;
//     $i++;

// }
// echo "".json_encode($rawdata);

?>

<script>
    // function individual(){
    //     $('#sms_empleados').show();
    //     $('#sms_dptos').hide();
    //     $('#sms_todos').hide();
        

    // }
    // function dptos(){
    //     $('#sms_empleados').hide();
    //     $('#sms_dptos').show();
    //     $('#sms_todos').hide();
        

    // }

    //  function todos(){
    //     $('#sms_empleados').hide();
    //     $('#sms_dptos').hide();
    //     $('#sms_todos').show();
        

    // }

    function ValidaSMS(){
        LongitudSMS = $('#sms_mensaje').val().length;
        Restan = 154 - LongitudSMS;
        $('#sms_label').text('Mensaje ('+Restan+')');
        return Restan;
    }

</script>

<script >
// function CargaAvancesSMS(){
//         $.get("sms_actividad.php", {}, function(htmlexterno){$("#SMS_actividad").html(htmlexterno);});
//         console.log("Consultando CargaAvancesSMS");
 
// }


// function CargaDispositivos(){
//         $.get("sms_actividadd.php", {}, function(htmlexterno){$("#autorizados").html(htmlexterno);});
//         console.log("Consultando Actvidad Dispositivos");
 
// }
 

// setInterval(CargaAvancesSMS,1000);
// setInterval(CargaDispositivos,4000);


</script>

<?php

include('src/footer.php');

?>                                  

