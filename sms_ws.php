<?php 
require("unica/config.php"); require("unica/funciones.php");
//WEBSERVICE PARA CONSUMIRSE CON LA APP ANDROID


// 1.- Validacion del dispositivo

//hay que validar el dispositivo del que viene la solicitud
if (isset($_GET['idi']) and isset($_GET['ext'])){//id del dispositivo (idi), no ponemos imei (porque si capturar la url, al menos no les diremos que es lo que es)

    
    if(isset($_GET['outID'])){//El Dispositivo Entrego el mensaje correctamente
        //si la primera letra del id de salida es una x, hubo un error        
        $error = "";
        $error = substr($_GET['outID'], 0,1);  $outID = substr($_GET['outID'], 1,strlen($_GET['outID'])); 

        if ($error=='x'){
            $comentariosActuales = sms_ComentarioSMS($outID);           
            $comentarios = "[".$fecha." ".$hora."] Ha marcado un error ".$_GET['ext']." <br>\n".$comentariosActuales;
            sms_ActualizaEstadoSMS($outID, '0', $comentarios, $_GET['idi']);
            

        } else { // si no hay error, marcar la entrega
            $comentariosActuales = sms_ComentarioSMS($outID);           
            $comentarios = "[".$fecha." ".$hora."] Entregado correctamente<br>\n".$comentariosActuales.$_GET['ext'];
            sms_ActualizaEstadoSMS($outID, '1', $comentarios, $_GET['idi']);

            // sms_EntregaSMSalDispositivo($_GET['idi'], $_GET['ext']);
        }
        //sms_EntregaSMSalDispositivo($_GET['idi'], $_GET['ext']);
        //echo $comentarios;
        sms_EntregaSMSalDispositivo($_GET['idi'], $_GET['ext'],"");
    }  else { // entregar mensaje al dispositivo para ser procesado
        sms_EntregaSMSalDispositivo($_GET['idi'], $_GET['ext'],"1");
    }

    

} 


function sms_ComentarioSMS($id){
    require("unica/config.php");
    $sql = "SELECT * FROM sms WHERE id='".$id."'";
    $resultado = $conexion -> query($sql);
    // if ($conexion->query($sql) == TRUE) { 
    if($f = $resultado -> fetch_array())
    {
        return $f['comentarios'];
    } else {
        return FALSE;
    }

        


}

function sms_ActualizaEstadoSMS($id, $estado, $comentarios, $imei){
    require("unica/config.php"); 
    $sql="UPDATE sms SET estado='".$estado."', comentarios='".$comentarios."', dispositivo='".$imei."' WHERE id='".$id."'";
    // echo $sql;
    $resultado = $conexion -> query($sql);
    if ($conexion->query($sql) == TRUE) { return TRUE; }
    else {return  FALSE;}


}


function sms_EntregaSMSalDispositivo($imei, $ext, $opt){
    require("unica/config.php"); 
if ($opt=="1"){
    if (sms_validaDispositivo($imei,$ext)==TRUE){ //validamos que sea un dispositivo aut
        $sql = "select id, celular, mensaje from sms where estado=0 and dispositivo='' limit 1 "; //consulta para enviar 
        //echo $sql;
        $myObj = new stdClass;
        $rawdata = array(); //creamos un array
        $r = $conexion -> query($sql);
        if ($conexion->query($sql) == TRUE) {
            $i=0; $r2 = $conexion -> query($sql);
            $c = 0;
            while($f = $r2 -> fetch_array())
            {            
                $myObj = new stdClass;
                $myObj->id = $f['id']; ///////////////Entregamos el mensaje
                $myObj->celular = $f['celular'];
                $myObj->mensaje = $f['mensaje'];
                $myJSON = json_encode($myObj);
                echo $myJSON;
                $c= $c+1;
           }
            
            if ($c==0){//no hubo resutlados
                    // $myObj = new stdClass;
                    // $myObj->id = "0000";
                    // $myObj->celular = "0000";
                    // $myObj->mensaje = "0000";
                    // $myJSON = json_encode($myObj);
                    // echo $myJSON;
                    echo "0";
            }
            historia('',"Se entrego webservice SMS al dispositivo con imei: ".$_GET['idi']."(".json_encode($rawdata).")");
        } else {//si hubo error en la bd
            // $myObj = new stdClass;
            // $myObj->id = "0";
            // $myObj->celular = "0";
            // $myObj->mensaje = "0";
            // $myJSON = json_encode($myObj);
            // echo $myJSON;
            echo "BD";
            
        }
    } else{
    //         $myObj = new stdClass;
    //         $myObj->id = "x";
    //         $myObj->celular = "x";
    //         $myObj->mensaje = "x";
    //         $myJSON = json_encode($myObj);
    //         echo $myJSON;
        echo "X";
     }
} else {//en caso que este informando
            // $myObj = new stdClass;
            // $myObj->id = "1";
            // $myObj->celular = "1";
            // $myObj->mensaje = "1";
            // $myJSON = json_encode($myObj);
            // echo $myJSON;
    echo "OK";
}
}


function sms_validaDispositivo($idi, $datoextra){//validamos el dispositivo, que sea nuestro y que este activo
    require("unica/config.php");
$sql = "SELECT * FROM sms_dispositivos  WHERE imei='".$idi."' and  estado=1"; 
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
    {
        historia('',"Se solicito webservice SMS desde el dispositivo con imei: ".$idi);
        return TRUE;
    } else
    {
        //informar que se intento acceder con un imei erroneo
        $msg = "<p>Se intento acceder desde el Webservice de SMS con el imei ".$idi." a las ".$hora." de ".$fecha.", en un dispositivo no autorizado</p><p><b>Informacion
        del dispositivo:</b><br>".$datoextra;        
        historia('',$msg);
        //notificacion_add ('2809', 'Acceso Denegado para imei '.$idi, $fecha, '2809', $msg);
        notificacion_add ('2809', 'ALERTA dispositivo '.$idi, $fecha, '2809', $msg);
        sms_AgregaDispositivo($idi,'', ''); //<-- agregamos y/o actualizamos los comentarios del dispositivo
        
        return FALSE;
    } 

}


function sms_AgregaDispositivo($imei, $nombre, $descripcion){
    require("unica/config.php");
    //1.- lo buscamos en los dispositivos
    $sql = "SELECT * FROM sms_dispositivos  WHERE imei='".$imei."'"; 
    $rc= $conexion -> query($sql);
    if($f = $rc -> fetch_array())
    {// si esta el dispositivo agregamos un comentarios e informamos a juanjo y agregamos una historia del evento
        //echo "si esta (".$sql.")<br>";
        $comentarios = "(".$fecha." | ".$hora.") Esta intentando acceder";
        $sql = "UPDATE sms_dispositivos SET comentarios='".$f['comentarios']."<br>".$comentarios."' wHERE imei='".$imei."'";
        $r = $conexion -> query($sql); 
        if ($conexion->query($sql) == TRUE) 
        {
            $dispositivo = "Equipo: ".$f['nombre'].", ".$f['descripcion'];
            notificacion_add ('2809', 'SMSAlerta Esta intentando entrar: '.$imei, $fecha, '2809', "<p>El dispositivo ".$dispositivo." esta intentando acceder a la plataforma");
            historia('',"<p>El dispositivo ".$dispositivo."con IMEI:<b>".$imei."</b> esta intentando acceder a la plataforma, se le ha negado el acceso");
            return TRUE;
        } else {return FALSE;}
        echo $sql;
    }
    else {//sino esta el dispositivo lo agregamos con estado 0
        //echo "No esta (".$sql.")<br>";
        $sql = "INSERT INTO sms_dispositivos
        (imei, estado, comentarios)  VALUES ('".$imei."','0','Intento acceder a  las ".$hora." de ".$fecha."')";
        
            if ($conexion->query($sql) == TRUE)
            {
                notificacion_add ('2809', 'SMSAlerta Esta intentando entrar: '.$imei, $fecha, '2809', "<p>El dispositivo ".$imei." esta intentando acceder a la plataforma");
                historia('',"<p>El dispositivo "."con IMEI:<b>".$imei."</b> esta intentando acceder a la plataforma, se le ha negado el acceso");
                return TRUE;
            }
            else {return FALSE; }
        
    }
        

}

?>