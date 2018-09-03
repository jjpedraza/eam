

<?php 
include ("./unica/body_head.php");
include ("./unica/body_menu.php");
?>



<?php

$id_aplicacion ="ap64"; //Id de la aplicacion a cargar
$nivel = aplicacion_nivel('ap64', $nitavu); $nivel=1;


if (sanpedro($id_aplicacion, $nitavu)==TRUE){echo "<h5>".app_detalle($id_aplicacion)."</h5>";

    echo "<section id='sms_submenu'>";		
    
    if ($nivel==1 or $nivel==2) {echo "<a rel='modal:open'  href='#sms_brigada' class='btn btn-tercero'>Enviar SMS a empleados</a>";}
    	
		
    echo "<hr></section>";
    

echo "<form id='sms_brigada' action='sms.php?brignew' method='post' class='modal'
style='
    background-color:#005BA0; color:white;
    
'
>";
echo "<h4 style='
background-color: #A3C30F;
color: white;
width: 100%;
margin: 0px;
height: 30px;
margin-left: -30px;
margin-right: 0px;
width: 113.7%;
margin-top: -15;
border-top-left-radius: 5px;
border-top-right-radius: 5px;
text-transform: uppercase;

'>¿A quien enviaras el mensaje?</h4>";
echo "<table class='tbl_dir'><tr>";
    echo "<td style='cursor:pointer;' onclick='individual();'>
        <input type='radio' id='radio_individual' name='sms_brigada_tipo'  value='individual'/><label for='radio_individual' style='color:white;'> Individual</label>
        </td>";
    echo "<td  style='cursor:pointer;' onclick='dptos();'>";
    echo "<input type='radio' id='radio_dptos' name='sms_brigada_tipo'  value='dptos'/><label for='radio_dptos' style='color:white;'>Departamentos</label>";
    echo "</td>";
    echo "<td style='cursor:pointer;' onclick='todos();'>";
    echo "<input type='radio' id='radio_todos' name='sms_brigada_tipo' value='todos'/><label for='radio_todos' style='color:white;'>Todos</label>"; 
    echo "</td>";
echo "</table>";

echo "<span id='sms_empleados'><select name='empleados'>";
    $sql = "select nitavu, nombre from empleados where estado ='' order by nombre ASC ";
    $r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<option value='".$f['nitavu']."'>".$f['nombre']."</option>";
    }
    // echo "<option value='' selected>Selecciona a un empleado</option>";
echo "</select></span>";

echo "<span id='sms_dptos'><select name='departamento'>";
    $sql = "select id, nombre from cat_gerarquia order by nombre ASC ";
    $r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
    {
        echo "<option value='".$f['id']."'>".$f['nombre']."</option>";
    }
    // echo "<option value='' selected>Selecciona a un Departamento</option>";
echo "</select></span>";


echo "<b id='sms_todos'>El mensaje se enviara a todos los empleados de ITAVU</b>";

echo "<span><label id='sms_label' style='color:#A3C30F;'>Mensaje (154):</label><textarea  maxlength='160' style='
    height:80px; margin-left: 8px;
    background-color:white; color:black;  font-size:14pt;
' rows='4'  oninput='ValidaSMS();' type='text' size='154' id='sms_mensaje'  name='sms_mensaje' required='required'></textarea></span>";
echo "<div><input placeholder='Nombre de la brigada' type='text' value='' name='Brigada' name='Brigada' required='required' ></div>";

echo "<div><input type='submit' value='Enviar' name='BtnEnviar' class='btn btn-secundario' ></div>";
echo "</form>";




echo "<div id='SMS_actividad'>";
//ACTIVIDAD DE AVANCE








echo "</div>";

echo "<div id='SMS_Autorizardispositivo'>";

   
    echo "<section id='autorizados'>";
   
    echo "</section>";


    echo "<div id='solicitantes'>";
    echo "<h6>DISPOSITIVOS DETENIDOS:</h6>";

    $sql = "select * from sms_dispositivos where estado=0 order by estado";
    echo "<table class='tabla'>";
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
    
        echo "<form id='formAut".$f['imei']."' action='sms.php' method='post' class='modal'>";
        echo "<table><tr><td>";
        echo "<label>IMEI del dispositivo:</label><input name='imei' type='text' value='".$f['imei']."' readonly>";
        echo "<span><label>Descripcion</label><textarea name='descripcion'></textarea></span >";
        echo "<input type='submit' name='BtnAutDisp' value='Autorizar' class='btn btn-default' >";
        echo "</td><td valign=top align=left class='tchico' width=40px;>";
        echo "<label class='tchico' style='padding-right: 5px; height:400px; overflow:auto; width:100%;'>".$f['comentarios'].$f['comentarios'].$f['comentarios']."</label>";
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
if (isset($_GET['stop'])){
    if ($nivel==1){
    $imei = $_GET['stop'];
    $comentarios0 = SMS_comentarios($imei);
    $comentarios1 = nitavu_nombre($nitavu)." DETUVO el dispositivo con imei ".$imei." para el envio de SMS";
    
    $comentarios = "[".$fecha.", ".$hora."] ".$comentarios1."<hr>".$comentarios0;
    $sql="UPDATE sms_dispositivos SET comentarios='".$comentarios."', estado='0' WHERE imei='".$imei."'";
    $r = $conexion -> query($sql);
    if ($conexion->query($sql) == TRUE) {
        historia($nitavu,$comentarios1);        
        mensaje("Dispositivo ".$imei." detenido<br>Ya no podra enviar mensajes apartir de ahora.",'sms.php');
    } else{
        mensaje("Hubo un error al intentar detener el dispositivo: ".$sql,'sms.php');
    }
}else {mensaje("ERROR: No autorizado para detener dispositivos",'sms.php');}


}

if (isset($_POST['BtnAutDisp'])){
    if ($nivel == 1){
    $imei = $_POST['imei'];
    $descripcion = $_POST['descripcion'];
    $sql="UPDATE sms_dispositivos SET descripcion='".$descripcion."', estado='1' WHERE imei='".$imei."'";
    $r = $conexion -> query($sql);
    if ($conexion->query($sql) == TRUE) {
        historia($nitavu,"Autorizo el dispositivo ".$imei." para gestionar el envio de sms de la plataforma");
        mensaje("Dispositivo ".$imei." autorizado",'sms.php');
    } else{
        mensaje("Hubo un error al intentar autorizar el dispositivo: ".$sql,'sms.php');
    }
    }else {mensaje("ERROR: No autorizado para autorizar dispositivos",'sms.php');}
}
   









} else {mensaje("ERROR: No tienes acceso a esta aplicación");}









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
    function individual(){
        $('#sms_empleados').show();
        $('#sms_dptos').hide();
        $('#sms_todos').hide();
        

    }
    function dptos(){
        $('#sms_empleados').hide();
        $('#sms_dptos').show();
        $('#sms_todos').hide();
        

    }

     function todos(){
        $('#sms_empleados').hide();
        $('#sms_dptos').hide();
        $('#sms_todos').show();
        

    }

    function ValidaSMS(){
        LongitudSMS = $('#sms_mensaje').val().length;
        Restan = 154 - LongitudSMS;
        $('#sms_label').text('Mensaje ('+Restan+')');
        return Restan;
    }

</script>

<script >
function CargaAvancesSMS(){
        $.get("sms_actividad.php", {}, function(htmlexterno){$("#SMS_actividad").html(htmlexterno);});
        console.log("Consultando CargaAvancesSMS");
 
}


function CargaDispositivos(){
        $.get("sms_actividadd.php", {}, function(htmlexterno){$("#autorizados").html(htmlexterno);});
        console.log("Consultando Actvidad Dispositivos");
 
}
 

setInterval(CargaAvancesSMS,1000);
setInterval(CargaDispositivos,4000);


</script>

<?php
include ("./unica/body_footer.php");

?>                                  

