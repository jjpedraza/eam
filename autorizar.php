

<?php 

include('src/head.php');



echo "<div id='SMS_Autorizardispositivo' class='ventana'>";
   
    echo "<div id='Dispositivos_autorizados'><h2>DISPOSITIVOS AUTORIZADOS</h2>";   
    $sql = "select * from sms_dispositivos where estado=1 order by estado";
    echo "<table class='tabla'>";
    echo "";
    $r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<tr>";
        echo "<td>";
        
        // echo "<a href='#formAut".$f['imei']."' rel='modal:open' class='aDispositivo' title='".$f['comentarios']."'>";
        echo "<b class='normal'>IMEI: ".$f['imei']."</b><br>";
        // echo "<label style='cursor:pointer;'>".$f['comentarios']."</label>";
        echo "<label style='cursor:pointer;'>".$f['descripcion']."</label>";
        // echo "</a>";
        echo "</td><td>";
        echo "<a href='autorizar.php?stop=".$f['imei']."' style='color:white;' class='btn btn-default'>Detener</a>";
        
        //contruccion de los from
    


        echo "</td>";
        
        echo "</tr>";

        

        
    }
    
    echo "</table>";
   
   
   
    echo "</div>";


    echo "<div id='Dispositivos_detenidos'>";
    echo "<h2>DISPOSITIVOS DETENIDOS:</h2>";

    $sql = "select * from sms_dispositivos where estado=0 order by estado";
    echo "<table class='tabla'>";
    echo "";
    $r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<tr>";
        echo "<td>";
        echo "<a href='#formAut".$f['imei']."' rel='modal:open' class='aDispositivo' title='".$f['comentarios']."'>";
        echo "<b class='normal'>IMEI: ".$f['imei']."</b><br>";
        // echo "<label style='cursor:pointer;'>".$f['comentarios']."</label>";
        echo "<label style='cursor:pointer;'>".$f['descripcion']."</label>";
        echo "</a>";
        
        
        //contruccion de los from
    
        echo "<form id='formAut".$f['imei']."' action='autorizar.php' method='post' class='modal'>";
        echo "<table class='tabla'><tr><td>";
        echo "<label>IMEI del dispositivo:</label><input name='imei' type='text' value='".$f['imei']."' readonly>";
        echo "<span><label>Descripcion</label><textarea name='descripcion'></textarea></span >";
        echo "<input type='submit' name='BtnAutDisp' value='Autorizar' class='btn btn-default' >";
        echo "</td><td valign=top align=left class='tchico' width=40px;>";
        echo "<label class='tchico' style='padding-right: 5px; height:400px; overflow:auto; width:100%;'>".$f['comentarios']."</label>";
        echo "</td></tr></table>";
        echo "</form>";


        echo "</td>";
        
        echo "</tr>";

        

        
    }
    
    echo "</table>";
    echo "<label>* Haz clic en la lista para autorizar</label>";
    echo "</div>";
    echo "<a style='color:white;text-decoration:none;' href='apk/EAMM.apk' title='Haga clic aqui para descargar'>Aplicacion para Android </a>";
echo "</div>";

function SMS_insertar($celular, $mensaje, $brigada, $comentarios, $gestiona){
require("unica/config.php");
    $sql = "INSERT INTO sms (celular, mensaje, estado, brigada, comentarios, envia, fecha, hora) VALUES ('".$celular."', '".$mensaje.":ITAVU', 0, '".$brigada."', '".$comentarios."', '".$gestiona."', '".$fecha."', '".$hora."')";
    // echo $sql;
    if ($conexion->query($sql) == TRUE)
    {
        historia($gestiona, "Enlisto SMS para ".$celular." en la brigada <b>".$brigada."</b>, con el mensaje: ".$mensaje);
        return TRUE;
    }
    else {return FALSE;}

}






if (isset($_POST['BtnEnviar'])){ 
    //insertamos a la tabla sms segun corresponda

    if ($_POST['sms_brigada_tipo']=='individual'){
        echo "seleccionado: ".$_POST['empleados'];
        $destinatario = $_POST['empleados'];
        if ($destinatario==''){mensaje("ERROR: no ha seleccionado a ninguno!",'sms.php?notxt');}
        else {
            $celular = nitavu_celular($destinatario);
            $quienEnvia = $nitavu;
            $destinatario_nombre = nitavu_nombre($destinatario);
            $mensaje = $_POST['sms_mensaje'];
            $comentarios = "Envio de prueba";
            $brigada = $_POST['Brigada'];
            if (SMS_insertar($celular, $mensaje, $brigada, $comentarios, $quienEnvia)==TRUE){

                mensaje("SMS enlistado correctamente",'sms.php?notxt');
            } else {
                mensaje("ERROR: no se ha enlistado por un error ",'sms.php?notxt');
            }
        }


        
    }

    if ($_POST['sms_brigada_tipo']=='dptos'){
        $dpto_seleccionado = $_POST['departamento'];
        if ($dpto_seleccionado == ''){mensaje("ERROR: no ha seleccionado a ninguno departamento!",'sms.php?notxt');}
        else{
            $cuantos = 0; $resumen="";
            $sql = "select nitavu, nombre, telefono_movil, dpto from empleados where dpto='".$dpto_seleccionado."' and estado='' order by nombre ASC ";
            $r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
            {
                $destinatario = $f['nitavu'];
                $celular = $f['telefono_movil'];
                $quienEnvia = $nitavu;
                $destinatario_nombre = $f['nombre'];
                $mensaje = $_POST['sms_mensaje'];
                $comentarios = "Envio de prueba";
                $brigada = $_POST['Brigada'];
                if (SMS_insertar($celular, $mensaje, $brigada, $comentarios, $quienEnvia)==TRUE){
                    $cuantos = $cuantos + 1;
                } else { } 

            }
            if ($cuantos >0 ){
                mensaje("Se han enlistado ".$cuantos." mensajes SMS correctamente, del departamento ".dpto_id($dpto_seleccionado)."",'sms.php?notxt');
            } else {mensaje("ERROR ha habido un error al intentar enlistar SMS",'sms.php?notxt');}
        }



    }

    if ($_POST['sms_brigada_tipo']=='todos'){
            $cuantos = 0; $resumen="";
            $sql = "select telefono_movil, nombre, nitavu, dpto from empleados where telefono_movil <> '' and estado ='' and
(length(replace(replace(replace(replace(telefono_movil,')',''),'(',''),'-',''),' ','')) = 10)
order by nombre";
            $r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
            {
                $destinatario = $f['nitavu'];
                $celular = $f['telefono_movil'];
                $quienEnvia = $nitavu;
                $destinatario_nombre = $f['nombre'];
                $mensaje = $_POST['sms_mensaje'];
                $comentarios = "Envio de prueba";
                $brigada = $_POST['Brigada'];
                if (SMS_insertar($celular, $mensaje, $brigada, $comentarios, $quienEnvia)==TRUE){
                    $cuantos = $cuantos + 1;
                } else { } 

            }
            if ($cuantos >0 ){
                mensaje("Se han enlistado ".$cuantos." mensajes SMS correctamente a todo el Instituto",'sms.php?notxt');
            } else {mensaje("ERROR ha habido un error al intentar enlistar SMS",'sms.php?notxt');}
    }


}

//DETERNER DISPOSITIVO
if (isset($_GET['stop'])){
    
    $imei = $_GET['stop'];
    
    $comentarios = $sms_user." DETUVO el dispositivo con imei ".$imei." para el envio de SMS, "."[".$fecha.", ".$hora."] ";
    $sql="UPDATE sms_dispositivos SET comentarios='".$comentarios."', estado='0' WHERE imei='".$imei."'";
    $r = $conexion -> query($sql);
    if ($conexion->query($sql) == TRUE) {
              
        mensaje("Dispositivo ".$imei." detenido<br>Ya no podra enviar mensajes apartir de ahora.",'autorizar.php');
    } else{
        mensaje("Hubo un error al intentar detener el dispositivo: ".$sql,'autorizar.php');
    }



}

if (isset($_POST['BtnAutDisp'])){

    $imei = $_POST['imei'];
    $descripcion = $_POST['descripcion'];
    $sql="UPDATE sms_dispositivos SET descripcion='".$descripcion."', estado='1' WHERE imei='".$imei."'";
    $r = $conexion -> query($sql);
    if ($conexion->query($sql) == TRUE) {
        
        mensaje("Dispositivo ".$imei." autorizado",'autorizar.php');
    } else{
        mensaje("Hubo un error al intentar autorizar el dispositivo: ".$sql,'autorizar.php');
    }
    
}
   














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


<?php

include('src/footer.php');

?>                                  

