<?php
define("FTP_SERVER","172.16.90.3"); //IP o Nombre del Servidor
define("FTP_PORT",21); //Puerto desde fuera 2323
define("FTP_USER","desarrollo2"); //Nombre de Usuario
define("FTP_PASSWORD","jpedraza"); //Contraseña de acceso
define("FTP_DIR","/home/desarrollo2/public_html/"); //ruta del  ftp
?>


<?php

function EstoyenDelegacion($nitavu){
	require("config.php");
	$miDpto = nitavu_dpto($nitavu);
	$sql = "SELECT * FROM cat_gerarquia WHERE (id='".$miDpto."')";
	$rc= $conexion -> query($sql);
	if($f = $rc -> fetch_array())
	{
		return $f['nivel'];
	}
	else
	{
		return '';
	}
}


function LimpiandoCelular($celular){
    $celular0 = str_replace(" ", "", $celular); //quitando espacios
    $celular0 = str_replace("-", "", $celular0); //quitando guiones
    $celular0 = str_replace("*", "", $celular0); //quitando asteriscos
    $celular0 = str_replace("(", "", $celular0); //quitando asteriscos
    $celular0 = str_replace(")", "", $celular0); //quitando asteriscos
    
    if (is_numeric($celular0)){ // si no tiene letras

        if (strlen($celular0) ==10){ // solo pasamos los que tengan longitud 10
            return $celular0;
        } else {
            return FALSE;
        }
    
    } else {
        return FALSE;
    }

    
}

function SMS_comentarios($imei){
	require("config.php"); $sql = "SELECT * FROM sms_dispositivos WHERE imei='".$imei."'";
	$rc= $conexion -> query($sql);	if($f = $rc -> fetch_array())
	{
		return $f['comentarios'];
	}
	
	else
	{ return FALSE;}
}


function xmlNomina($id){
require("config.php");
$sql = "SELECT * FROM nominas WHERE id='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
	return $f['xmlCont'];
}

else
{ return FALSE;}
}


function mysql_XML($nombreItem) {
require("config.php");
$sql = "SELECT * FROM nominas WHERE id='".$nombreItem."'";
$resultado= $conexion -> query($sql);
$msg="";
//if($f = $resultado -> fetch_array())
//{	
	$campo = array();
	
	// llenamos el array de nombres de campos
	for ($i=0; $i<mysql_num_fields($resultado); $i++)
		$campo[$i] = mysql_field_name($resultado, $i);
	
	// creamos el documento XML	
	$dom = new DOMDocument('1.0', 'UTF-8');
	$doc = $dom->appendChild($dom->createElement($nombreDoc));
	
	// recorremos el resultado
	for ($i=0; $i<mysql_num_rows($resultado); $i++) {
		
		// creamos el item
		$nodo = $doc->appendChild($dom->createElement($nombreItem));
		
		// agregamos los campos que corresponden
		for ($b=0; $b<count($campo); $b++) {
			$campoTexto = $nodo->appendChild($dom->createElement($campo[$b]));
			$campoTexto->appendChild($dom->createTextNode(mysql_result($resultado, $i, $b)));
		}
	}

	// retornamos el archivo XML como cadena de texto
	$dom->formatOutput = true; 
	return $dom->saveXML();    
//}


}

function NominaAdd($nitavu, $xmlCont, $FechaIni, $FechaFin, $periodo, $autorizo){
require("config.php");
$sql = "INSERT INTO nominas
(nitavu, xmlCont, FechaIni, FechaFin, historia_nitavu, historia_fecha, historia_hora, periodo)
VALUES
('$nitavu', '$xmlCont', '$FechaIni','$FechaFin','$autorizo','$fecha','$hora','$periodo')";
if ($conexion->query($sql) == TRUE)
{	//echo "ok";
	//notificamos
	$mensaje="<p>Buen dia ".nitavu_nombre($nitavu)."</p>";
	$mensaje=$mensaje."<p>Ya esta disponible el recibo de tu nomina en la plataforma, correspondiente al perido ".$periodo." que comprende de ".$FechaIni." a ".$FechaFin."</p>";
	$mensaje=$mensaje."<p>Para descargarlo entra a la plataforma con tus datos de acceso, y en la parte inferior en preferencias, encontraras la pestaña <b>Mi Nomina</b></p><p>Si no te ha llegado notificacion a tu correo, te sugiero activarlo en la plataforma o comunicarte al departamento de Informatica.</p><p>Un Saludo</p>";
	$mensaje=$mensaje."";
	$quienEnvia = titular('57'); //Titular de Contabilidad
	notificacion_add($nitavu, 'Nomina '.$FechaIni.' a '.$FechaFin, $fecha, $quienEnvia, $mensaje);
	historia($autorizo,"Integro a la plataforma el recibo de nomina del empleado ".nitavu_nombre($nitavu)."");
	return TRUE;

}
	else
{	//echo $sql;
	return FALSE;
}
}


function Nitavu_real($NEmpleado){
    $real = substr($NEmpleado, 1, 10);
    if (substr($real, 0, 1) == '0'){
        $real = substr($NEmpleado, 2, 10);    
        
        if (substr($real, 0, 1) == '0'){
			$real = substr($NEmpleado, 3, 10);    
				if (substr($real, 0, 1) == '0'){
					$real = substr($NEmpleado, 4, 10);    
				}

        }    
    }
    return $real;
}


function NominaPeriodo($FechaInicio, $FechaFin){
require("config.php");
$sql = "select numeroperiodo from nom10002 where nom10002.fechainicio >='".$FechaInicio."' and nom10002.fechafin <='".$FechaFin."'";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['numeroperiodo'];}
else
	{return 'FALSE';}
}

function SexoNomina($NEmpleado){
require("config.php");
$sql = "select sexo from nom10001 where nom10001.codigoempleado ='".$NEmpleado."'";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['sexo'];}
else
	{return '';}
}


function EdoCivilNomina($NEmpleado){
require("config.php");
$sql = "select estadocivil from nom10001 where nom10001.codigoempleado ='".$NEmpleado."'";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['estadocivil'];}
else
	{return '';}
}

function recibirCorreos($id){
require("config.php");
$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['recibirCorreos'];}
else
	{return FALSE;}
}

function informar_usuariosapps($idapp, $contenido,$yo){
require("config.php"); $texto="";
$sql = "select empleados.nitavu, nombre from empleados, aplicaciones_permisos WHERE empleados.nitavu = aplicaciones_permisos.nitavu AND aplicaciones_permisos.idapp='".$idapp."'";
echo $sql;
$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
{
	$texto ="<p> Buen Dia <b>".$f['nombre']."</b></p>";
	$texto = $texto."<p>Con el áfan de mejorar el modo en el que interactuas con la plataforma y apoyarte en su manejo, hemos actualizado la ayuda de la aplicacion ".app_nombre($idapp).".</p>";
	$texto = $texto."<br><br><b>ACTUALIZACION:</b>".$contenido;
	notificacion_add($f['nitavu'], 'Actualizacion de '.app_nombre($idapp), $fecha, '2809', $contenido);
	historia($yo,"Informo de la actualizacion de ".app_nombre($idapp)." a ".$f['nombre']);
}


}

function insertar_widget($idapp, $usuario)
{
	if (sanpedro($idapp, $usuario)==TRUE){
		echo "<div class='widget'>";	
		include("widget_actividad.php");
		echo "</div>";
	}
}

function sonido_mensaje($n){
if ($n>0){
$tmp="";
$tmp = $tmp.'<script >';
$tmp = $tmp.'var sounds = new Array(';
$tmp = $tmp.'new Audio("audios/mensaje.wav"), ';
$tmp = $tmp.'	
var i = 1;
playSnd();

function playSnd() {
    i++;
    if (i == sounds.length) return;
    sounds[1].currentTime = -5;
    sounds[i].addEventListener("ended", playSnd);
    sounds[i].play();
}
</script>

';
echo $tmp;
}


}


function ayuda_nombre($id){
require("config.php");
$sql = "SELECT * FROM aplicaciones WHERE idapp='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
	{ return "<h1>".$f['nombre']."</h1>"."<label>(".$f['descripcion'].")</label>";  }
else {return FALSE;}

}


function ayuda_ayuda($id){
require("config.php");
$sql = "SELECT ayuda, idapp FROM aplicaciones WHERE idapp='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
	{ return $f['ayuda'];}
else {return FALSE;}

}


function misnotificaciones_n($user){
require("config.php");
$sql = "SELECT count(*) as n FROM notificaciones	 WHERE lectura_fecha='0000-00-00' and nitavu='".$user."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
	{	
		return $f['n'];}
else {return 0;}

}

function traerContenidoNotificacion($usuario, $agregadas){
require("config.php");
	//echo $agregadas;
	$sql = "SELECT nitavu_manda,contenido FROM notificaciones WHERE lectura_fecha='0000-00-00' and nitavu='".$usuario."' ORDER BY entregar_fecha DESC LIMIT ".$agregadas."";
	//echo $sql;
	$contenido="";
	if(!empty($sql) == true){
		$rc= $conexion -> query($sql);
		if($rc){
			while($f = $rc -> fetch_array()){
				$contenido = $contenido.'/'.$f['nitavu_manda'].','.$f['contenido'];
			}
		}
		
	}
	return $contenido;
}

function actualizarNuevoNumero($n,$user){
require("config.php");
$sql = "UPDATE empleados SET NumNoti=".$n." WHERE nitavu='".$user."'";
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
	return TRUE;
}
else {
	return FALSE;
}
}

function ultimoNumero($user){
require("config.php");
$sql = "SELECT NumNoti FROM empleados WHERE nitavu='".$user."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array()){	
	return $f['NumNoti'];
}else{
	return 0;
}

}


function org_json(){
//ESTA FUNCION AFECTA 3 NIVELES EN SU BUSQUEDA, DE NECESITARSE MAS AJUSTAR LA BUSQUED A MAS	
require("config.php");
$j="";
$sql = "SELECT * FROM cat_gerarquia WHERE id='0'";

if ($conexion->query($sql) == TRUE){
	$r2 = $conexion -> query($sql = "SELECT * FROM cat_gerarquia WHERE id='0'"); while($f = $r2 -> fetch_array())
	{if (org_dependencias($f['id'])==0){$j=$j."{'name' : '".$f['nombre']."', 'title': '".nitavu_nombre($f['titular'])."', 'className': '".$f['nivel']."'},";}
	else{
		$j=$j."'name' : '".$f['nombre']."', 'title': '".nitavu_nombre($f['titular'])."', 'className': '".$f['nivel']."', 'children':[";


		$r3 = $conexion -> query($sql = "SELECT * FROM cat_gerarquia WHERE dependencia='".$f['id']."'"); while($f3 = $r3 -> fetch_array())
		{if (org_dependencias($f3['id'])==0){$j=$j."{'name' : '".$f['nombre']."', 'title': '".nitavu_nombre($f3['titular'])."', 'className': '".$f3['nivel']."'},";}
		else{
			$j=$j."{'name' : '".$f3['nombre']."', 'title': '".nitavu_nombre($f3['titular'])."', 'className': '".$f3['nivel']."', 'children':[";


		$r4 = $conexion -> query($sql = "SELECT * FROM cat_gerarquia WHERE dependencia='".$f3['id']."'"); while($f4 = $r4 -> fetch_array())
		{if (org_dependencias($f4['id'])==0){$j=$j."{'name' : '".$f4['nombre']."', 'title': '".nitavu_nombre($f4['titular'])."', 'className': '".$f4['nivel']."'},";}
		else{
			$j=$j."{'name' : '".$f4['nombre']."', 'title': '".nitavu_nombre($f4['titular'])."', 'className': '".$f4['nivel']."', 'children':[";


		$r5 = $conexion -> query($sql = "SELECT * FROM cat_gerarquia WHERE dependencia='".$f4['id']."'"); while($f5 = $r5 -> fetch_array())
		{if (org_dependencias($f5['id'])==0){$j=$j."{'name' : '".$f5['nombre']."', 'title': '".nitavu_nombre($f5['titular'])."', 'className': '".$f5['nivel']."'},";}
		else{
			$j=$j."{'name' : '".$f5['nombre']."', 'title': '".nitavu_nombre($f5['titular'])."', 'className': '".$f5['nivel']."', 'children':[";


		
		$r6 = $conexion -> query($sql = "SELECT * FROM cat_gerarquia WHERE dependencia='".$f5['id']."'"); while($f6 = $r6 -> fetch_array())
		{if (org_dependencias($f6['id'])==0){$j=$j."{'name' : '".$f6['nombre']."', 'title': '".nitavu_nombre($f6['titular'])."', 'className': '".$f6['nivel']."'},";}
		else{
			$j=$j."{'name' : '".$f6['nombre']."', 'title': '".nitavu_nombre($f6['titular'])."', 'className': '".$f6['nivel']."', 'children':[";


		$r7 = $conexion -> query($sql = "SELECT * FROM cat_gerarquia WHERE dependencia='".$f6['id']."'"); while($f7 = $r7 -> fetch_array())
		{if (org_dependencias($f7['id'])==0){$j=$j."{'name' : '".$f7['nombre']."', 'title': '".nitavu_nombre($f7['titular'])."', 'className': '".$f7['nivel']."'},";}
		else{
			$j=$j."{'name' : '".$f7['nombre']."', 'title': '".nitavu_nombre($f7['titular'])."', 'className': '".$f7['nivel']."', 'children':[";

		$r8 = $conexion -> query($sql = "SELECT * FROM cat_gerarquia WHERE dependencia='".$f7['id']."'"); while($f8 = $r8 -> fetch_array())
		{if (org_dependencias($f8['id'])==0){$j=$j."{'name' : '".$f8['nombre']."', 'title': '".nitavu_nombre($f8['titular'])."', 'className': '".$f8['nivel']."'},";}
		else{
			$j=$j."{'name' : '".$f8['nombre']."', 'title': '".nitavu_nombre($f8['titular'])."', 'className': '".$f8['nivel']."', 'children':[";


		


		$j =$j."]},"; //3
		}  
		//$j = substr($j, 0, -2);//quita coma
		}	
		


		$j =$j."]},"; //3
		}  
		//$j = substr($j, 0, -2);//quita coma
		}	
		


		$j =$j."]},"; //3
		}  
		//$j = substr($j, 0, -2);//quita coma
		}	


		$j =$j."]},"; //3
		}  
		//$j = substr($j, 0, -2);//quita coma
		}	



		$j =$j."]},"; //3
		}  
		//$j = substr($j, 0, -2);//quita coma
		}	



		$j =$j."]},"; //3
		}
		//$j = substr($j, 0, -2);//quita coma
		}	


		$j =$j."]"; //3
	}}

}

return $j;

}


function org_dependencias($nodo){
//ESTA FUNCION AFECTA 3 NIVELES EN SU BUSQUEDA, DE NECESITARSE MAS AJUSTAR LA BUSQUED A MAS	
require("config.php");
$j="";
$sql = "select count(*) as n from cat_gerarquia where dependencia = '".$nodo."'";
if ($conexion->query($sql) == TRUE)
{	$rc = $conexion -> query($sql);
	if($f = $rc -> fetch_array())
	{
		return $f['n'];
	}
	else {
		return 0;
	}

} else {
	return 0;
}



}



function submenu_add($url, $icono, $texto1, $texto2){
		echo "<article>";
		echo "<table width=100%><tr><td width=50%>";		
		echo "<a href='$url' rel='modal:open'><img src='icon/$icono'></a></td>";
		echo "<td width=50%><a href='$url'>$texto1<br><b> $texto2</b></a></td>";
		echo "</tr></table>";
		echo "</article>";
}

function quienesmijefe($nuc){
//ESTA FUNCION AFECTA 3 NIVELES EN SU BUSQUEDA, DE NECESITARSE MAS AJUSTAR LA BUSQUED A MAS	
require("config.php");
$midpto = nitavu_dpto($nuc);

$sql = "SELECT * FROM cat_gerarquia WHERE id='".$midpto."'";
//echo $sql;
if ($conexion->query($sql) == TRUE)
{	$rc= $conexion -> query($sql);
	if($f = $rc -> fetch_array())
	{if ($f['titular']==''){//si no hay titular
			//buscamos de quien depende el dpto------------------------------------------ 2
			$sql = "SELECT * FROM cat_gerarquia WHERE id='".$f['dependencia']."'";			
			//echo $sql;
			if ($conexion->query($sql) == TRUE)
			{	$rc2= $conexion -> query($sql);
				if($f2 = $rc2 -> fetch_array()){
				if ($f2['titular']==''){//si este tampoco tiene titular vamos al siguiente
					//buscamos de quien depende este dpto------------------------------------------ 3
					$sql = "SELECT * FROM cat_gerarquia WHERE id='".$f2['dependencia']."'";
					//echo $sql;
					if ($conexion->query($sql) == TRUE)
					{	$rc3= $conexion -> query($sql);
						if($f3 = $rc3 -> fetch_array()){
						if ($f3['titular']==''){//si este tampoco tiene titular vamos al siguiente
							
									//buscamos de quien depende este dpto------------------------------------------ 4
										$sql = "SELECT * FROM cat_gerarquia WHERE id='".$f3['dependencia']."'";
										//echo $sql;
										if ($conexion->query($sql) == TRUE)
										{	$rc4= $conexion -> query($sql);
											if($f4 = $rc4 -> fetch_array()){
											if ($f4['titular']==''){//si este tampoco tiene titular vamos al siguiente
												


												
											}
											else{return $f4['titular']; //Damos el titular de este dpto en su nivel 3		
											}
										}}



						}
						else{return $f3['titular']; //Damos el titular de este dpto en su nivel 3		
						}
					}}




				} else{return $f2['titular']; //Damos el titular de este dpto
				}

			}}




	}
		else{return $f['titular']; //Titular de tu Dpto
	}}
}
else { return FALSE;}

	

}


function refresh($page){
//header('location:$page');
echo "<script> 

window.location.replace('$page'); 

</script>";
	
}
function estoy_enmesadetemas($id, $tema){
require("config.php");
$sql = "SELECT * FROM pendientes_eq WHERE integrante='".$id."' and nombre='".$tema."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return TRUE;
}
else
{ return FALSE;}
}


function tema_estado($id){
require("config.php");
$sql = "SELECT * FROM pendientes WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['estado'];
}
else
{ return FALSE;}
}




function pendientes_eq_nombre($id){
require("config.php");
$sql = "SELECT * FROM pendientes_eq WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['nombre'];
}
else
{ return FALSE;}
}

function pendientes_autor($id){
require("config.php");
$sql = "SELECT * FROM pendientes WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['autor'];
}
else
{ return FALSE;}
}




function pendientes_id_nombre($id){
require("config.php");
$sql = "SELECT * FROM pendientes WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['tema'];
}
else
{ return FALSE;}
}



function pendientes_tema_equipo($id){
require("config.php");
$sql = "SELECT * FROM pendientes WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['equipo'];
}
else
{ return FALSE;}
}


function correo($mail_dest, $mail_dest_name, $replymail, $replymail_name, $asunto, $contenido, $nuc){
//sleep(3);//retraso programado
require("config.php");
if (recibirCorreos($nuc)==0){
require_once('mailer/PHPMailerAutoload.php');
$limite="";
$footer="
<br><br>

<hr><p style=color:gray; font-family:Verdana, Geneva, sans-serif; font-size:10pt;> 
	Este correo electronico es enviado de manera automatizada mediante la Plataforma de ITAVU.<br>	
	<b style=color:#005BA0>Dpto. de Informatica | </b>.
	 Tel. 318-5516 Ext.: <b>46612</b>, <b>46524</b>, <b>46580</b>,  <b>46530</b>, <b>46516</b> y <b>46543</b>
</p>

";
$footer = $footer.'
<p charset=UTF-8  style=font-size:8pt;color: gray;><b>AVISO DE PRIVACIDAD DEL CORREO ELECTRONICO INSTITUCIONAL DEL GOBIERNO DEL ESTADO DE TAMAULIPAS</b><br>
<em>El contenido de este mensaje por medio electronico incluyendo datos, texto, imagenes y/o enlaces a otros contenidos tiene el caracter de confidencial y
 de uso exclusivo del Gobierno del Estado de Tamaulipas, asi como de las personas y/o empresas a las que se dirige. No se considera oferta, propuesta o 
 acuerdo sino hasta que sea confirmado en documento por escrito que contenga la firma autografa del servidor publico autorizado legalmente para esta
  operacion. </em><em> El contenido es de caracter confidencial por lo cual no podra distribuirse y/o difundirse por ningun medio sin la previa autorizacion 
  del emisor original.</em><em>Si usted no es el destinatario se le prohibe su utilizacion total o parcial para cualquier fin. Se pone a su disposicion
   el Aviso de privacidad del correo electronico institucional en el siguiente enlace..</em><em> 
   <b style=color:green;font-size:10pt;>El arbol que servira para hacer el papel, tardara 7 años  en crecer. No imprimas este mensaje si no es necesario.</b>
<br>
Puede consultar aquí el <a href="http://www.tamaulipas.gob.mx/aviso-de-privacidad-correo/" 
target="_blank" data-saferedirecturl="https://www.google.com/url?hl=es&amp;q=http://www.tamaulipas.gob.mx/aviso-de-privacidad-correo/&amp;source=gmail&amp;ust=1519403848535000&amp;usg=AFQjCNFslLVHkZnjBZsv-9m0Yw2D_CR14w">Aviso de Privacidad</a> y <a href="http://www.tamaulipas.gob.mx/politicas-correo-institucional/" target="_blank" data-saferedirecturl="https://www.google.com/url?hl=es&amp;q=http://www.tamaulipas.gob.mx/politicas-correo-institucional/&amp;source=gmail&amp;ust=1519403848535000&amp;usg=AFQjCNFKV3H0b5lcf26v_YCtczEDmSB_Yg">Políticas y Normas.</a>
</p>';
// if (nitavu_correo_valido($nuc)==TRUE){} else{
// 	$footer = "<b style=color:red>El correo electronico de ".$mail_dest_name." (".$mail_dest.") aun no se ha sido verficado, si contestara este correo, verifique que este correcta la direccion de correo antes de enviarla. </b><br><br>".$footer;
// }

if ($replymail==''){
	$replymail = 'itavu.informatica@tam.gob.mx';
	$replymail_name='Dpto. de Informatica de ITAVU';
}
$contenido = "<p charset=UTF-8>".$contenido."</p>";
$limite = correo_limite(); if ($limite>0){
////////CONFIGURACION DEL CORREO DE LA PLATAFORMA////////
	//date_default_timezone_set('Etc/UTC');
	
	$mail = new PHPMailer;
	$mail->isSMTP(); $mail->SMTPDebug = 0; // 0 = off (for production use)// 1 = client messages// 2 = client and server messages
	$mail->Debugoutput = 'html'; $mail->Host = 'smtp.gmail.com';  // use // $mail->Host = gethostbyname('smtp.gmail.com'); 
	$mail->Helo = "smtp.gmail.com";
	$mail->Port = 587; $mail->SMTPSecure = 'tls'; $mail->SMTPAuth = true; 
	$mail->Username = "itavu.informatica@tam.gob.mx"; $mail->Password = "plataforma"; //CUENTA MASTER
	$mail->setFrom('itavu.informatica@tam.gob.mx', $replymail_name); //Quie envia
	$mail->addReplyTo($replymail, $replymail_name); //Reponder a nombre de 
	$mail->addAddress($mail_dest, $mail_dest_name); //Set Destinatario
	$mail->Subject = $asunto;  //Set asunto
	//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__)); //--- PARA AÑADIR CONTENIDO DESDE UN ARCHIVO
	$mail->msgHTML($contenido);
	$mail->AltBody = 'El mensaje no puede ser entregado, debido a que su cliente de correo no puede leer el formato html';
	//adjuntar imagenes //$mail->addAttachment('https:/itavu.dyndns.org/img/logo_copia.png');
	$correo_historia="";
	if (!$mail->send()) {//Si se envia		
		
		
		historia($nuc,$correo_historia);
		$sql = "INSERT INTO correos (nuc, asunto, contenido, fecha, hora, correo, correo_name, responder_a, responder_a_name, estado, historia)";
		$sql = $sql." VALUES ('".$nuc."', ";
		$sql = $sql."'".$asunto."',";
		$sql = $sql."'".$contenido."',";
		$sql = $sql."'".$fecha."',";
		$sql = $sql."'".$hora."',";
		$sql = $sql."'".$mail_dest."',";
		$sql = $sql."'".$mail_dest_name."',";
		$sql = $sql."'".$replymail."',";
		$sql = $sql."'".$replymail_name."',";
		$sql = $sql."'0',";
		$sql = $sql."'Error: ".$correo_historia."'";

		$sql = $sql.")";
		$correo_historia= "No se ha podido enviar el correo (".$sql."): ".$mail->ErrorInfo;
		//echo $sql;
		if ($conexion->query($sql) == TRUE)
			{}
			else {}		
		return FALSE;
	} else {
		
		$estado_historia="Enviado con exito a las ".$hora." del ".fecha_larga($fecha);
		historia($nuc,"Correo para ".$mail_dest.", ".$mail_dest_name." enviado por ".$replymail_name." , ".$replymail."".$correo_historia.", Limite actual: ".$limite."<hr>".$contenido."<hr>");

		$sql = "INSERT INTO correos (nuc, asunto, contenido, fecha, hora, correo, correo_name, responder_a, responder_a_name, estado, historia)";
		$sql = $sql." VALUES ('".$nuc."', ";
		$sql = $sql."'".$asunto."',";
		$sql = $sql."'".$contenido."',";
		$sql = $sql."'".$fecha."',";
		$sql = $sql."'".$hora."',";
		$sql = $sql."'".$mail_dest."',";
		$sql = $sql."'".$mail_dest_name."',";
		$sql = $sql."'".$replymail."',";
		$sql = $sql."'".$replymail_name."',";
		$sql = $sql."'1',";
		$sql = $sql."'".$estado_historia."'";
		
		$sql = $sql.")";
		//echo $sql;
		if ($conexion->query($sql) == TRUE)
			{}
			else {}

		return TRUE;
	}
	//notificacion_add ('119460', 'chat', $fecha, $nuc, "Informandote se  utilizo el correo: Correo para ".$mail_dest.", ".$mail_dest_name." enviado por ".$replymail_name." , ".$replymail."".$correo_historia.", Limite actual: ".$limite."");
}else{
	


		$correo_historia= "No se envio el correo electronico, Se termino el limite de envio (".$mail_dest.")";
		historia($nuc,$correo_historia);
		$sql = "INSERT INTO correos (nuc, asunto, contenido, fecha, hora, correo, correo_name, responder_a, responder_a_name, estado, historia)";
		$sql = $sql." VALUES ('".$nuc."', ";
		$sql = $sql."'".$asunto."',";
		$sql = $sql."'".$contenido."',";
		$sql = $sql."'".$fecha."',";
		$sql = $sql."'".$hora."',";
		$sql = $sql."'".$mail_dest."',";
		$sql = $sql."'".$mail_dest_name."',";
		$sql = $sql."'".$replymail."',";
		$sql = $sql."'".$replymail_name."',";
		$sql = $sql."'0',";
		$sql = $sql."'Error: ".$correo_historia."'";

		$sql = $sql.")";
		//echo $sql;
		if ($conexion->query($sql) == TRUE)
			{}
			else {}			
		//mensaje("No se envio el correo ya que se ha excedido el limite de envio diario (".$limite.")",'');s

}//limite
}
else {
	//no dio permiso para enviarle
	$contenido2="<p>Se le intento enviar un correo a ".nitavu_nombre($nuc).", pero no se pudo ya que desactivo la opcion para recibir correos</p>";
	$contenido2 = $contenido2."<p>Contenido del correo: <br><br>".$contenido."</p>";
	notificacion_add (quienesmijefe($nuc), "chat", $fecha, $nuc, $contenido2);
}
}











function pases_dptosaut_n($nitavu){
require("config.php"); $dptos = "";
$sql = "SELECT count(*) as n FROM empleados_salidas_autoriza_excepcion WHERE (nitavu='".$nitavu."')";
//echo $sql;
	$rc = $conexion -> query($sql);
	if($f = $rc -> fetch_array())
	{
		return $f['n'];
	}

}





function pases_dptosaut($nitavu){
require("config.php"); $dptos = "";
$sql = "SELECT * FROM empleados_salidas_autoriza_excepcion WHERE (nitavu='".$nitavu."')";
$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
{
	$dptos = $dptos.$f['dpto'].", ";
}
return substr($dptos, 0, -2);
}



function pases_dptosaut_nombre($nitavu){
require("config.php"); $dptos = "";
$sql = "SELECT * FROM empleados_salidas_autoriza_excepcion WHERE (nitavu='".$nitavu."')";
//echo $sql;
$r2 = $conexion -> query($sql); while($f = $r2 -> fetch_array())
{
	$dptos = $dptos.dpto_id($f['dpto']).", ";
}
return substr($dptos, 0, -2);
}


function misdptos($nuc){
	require("config.php");
//recuperacion del nivel 1	
$sql = "
SELECT
	cat_gerarquia.id as Edpto,
	cat_gerarquia.titular as Etitular,
	cat_gerarquia.nombre,
	(SELECT nombre from empleados where nitavu=Etitular) as nombre
FROM	cat_gerarquia
where dependencia = '".nitavu_dpto($nuc)."'


";	
//echo "<hr>1:<br>".$sql;
$misdptos=nitavu_dpto($nuc).", ";
$rc= $conexion -> query($sql);
while($f = $rc -> fetch_array()) {
	//"<hr> 1:<br>".var_dump($f)."";
	$misdptos = $misdptos.$f['Edpto'].", ";

	//recuperacion del nivel 2	
	$sql = "
	SELECT
		cat_gerarquia.id as Edpto,
		cat_gerarquia.titular as Etitular,
		cat_gerarquia.nombre,
		(SELECT nombre from empleados where nitavu=Etitular) as nombre
	FROM	cat_gerarquia
	where dependencia = '".$f['Edpto']."'


	";	
	//echo "<hr>2:<br>".$sql;
	$rc2= $conexion -> query($sql);
	while($f2 = $rc2 -> fetch_array()) {
		//"<hr> 2:<br>".var_dump($f2)."";
		$misdptos = $misdptos.$f2['Edpto'].", ";

		//recuperacion del nivel 3
		//recuperacion del nivel 2	
		$sql = "
		SELECT
			cat_gerarquia.id as Edpto,
			cat_gerarquia.titular as Etitular,
			cat_gerarquia.nombre,
			(SELECT nombre from empleados where nitavu=Etitular) as nombre
		FROM	cat_gerarquia
		where dependencia = '".$f2['Edpto']."'


		";	
		//	echo "<hr>3:<br>".$sql;
		$rc3= $conexion -> query($sql);
		while($f3 = $rc3-> fetch_array()) {
			//"<hr> 3:<br>".var_dump($f3)."";
			$misdptos = $misdptos.$f3['Edpto'].", ";

			//recuperacion del nivel 4
			$sql = "
			SELECT
				cat_gerarquia.id as Edpto,
				cat_gerarquia.titular as Etitular,
				cat_gerarquia.nombre,
				(SELECT nombre from empleados where nitavu=Etitular) as nombre
			FROM	cat_gerarquia
			where dependencia = '".$f3['Edpto']."'


			";	
			//	echo "<hr>4:<br>".$sql;
			$rc4= $conexion -> query($sql);
			while($f4 = $rc4 -> fetch_array()) {
				//"<hr> 4:<br>".var_dump($f4)."";
				$misdptos = $misdptos.$f4['Edpto'].", ";


		}//4

	}//3

}//2

}//1
return substr($misdptos, 0, -2);
}



function misdptos_sinmi($nuc){
	require("config.php");
//recuperacion del nivel 1	
$sql = "
SELECT
	cat_gerarquia.id as Edpto,
	cat_gerarquia.titular as Etitular,
	cat_gerarquia.nombre,
	(SELECT nombre from empleados where nitavu=Etitular) as nombre
FROM	cat_gerarquia
where dependencia = '".nitavu_dpto($nuc)."'


";	
//echo "<hr>1:<br>".$sql;
$misdptos="";
$rc= $conexion -> query($sql);
while($f = $rc -> fetch_array()) {
	//"<hr> 1:<br>".var_dump($f)."";
	$misdptos = $misdptos.$f['Edpto'].", ";

	//recuperacion del nivel 2	
	$sql = "
	SELECT
		cat_gerarquia.id as Edpto,
		cat_gerarquia.titular as Etitular,
		cat_gerarquia.nombre,
		(SELECT nombre from empleados where nitavu=Etitular) as nombre
	FROM	cat_gerarquia
	where dependencia = '".$f['Edpto']."'


	";	
	//echo "<hr>2:<br>".$sql;
	$rc2= $conexion -> query($sql);
	while($f2 = $rc2 -> fetch_array()) {
		//"<hr> 2:<br>".var_dump($f2)."";
		$misdptos = $misdptos.$f2['Edpto'].", ";

		//recuperacion del nivel 3
		//recuperacion del nivel 2	
		$sql = "
		SELECT
			cat_gerarquia.id as Edpto,
			cat_gerarquia.titular as Etitular,
			cat_gerarquia.nombre,
			(SELECT nombre from empleados where nitavu=Etitular) as nombre
		FROM	cat_gerarquia
		where dependencia = '".$f2['Edpto']."'


		";	
		//	echo "<hr>3:<br>".$sql;
		$rc3= $conexion -> query($sql);
		while($f3 = $rc3-> fetch_array()) {
			//"<hr> 3:<br>".var_dump($f3)."";
			$misdptos = $misdptos.$f3['Edpto'].", ";

			//recuperacion del nivel 4
			$sql = "
			SELECT
				cat_gerarquia.id as Edpto,
				cat_gerarquia.titular as Etitular,
				cat_gerarquia.nombre,
				(SELECT nombre from empleados where nitavu=Etitular) as nombre
			FROM	cat_gerarquia
			where dependencia = '".$f3['Edpto']."'


			";	
			//	echo "<hr>4:<br>".$sql;
			$rc4= $conexion -> query($sql);
			while($f4 = $rc4 -> fetch_array()) {
				//"<hr> 4:<br>".var_dump($f4)."";
				$misdptos = $misdptos.$f4['Edpto'].", ";


		}//4

	}//3

}//2

}//1
return substr($misdptos, 0, -2);
}

function comida_salio($id, $autorizo, $quien){
require("config.php");
$sql = "UPDATE empleados_salidas_temporal SET registro_salida='".$hora."' wHERE id='".$id."'";
//echo $sql;
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
	historia($autorizo, "Dio salida del pase de comida con ID".$id.", de ".nitavu_nombre($quien)." (".$quien.")");
	//header('location:../vigilancia3.php');
	return TRUE;
}
else {
	return FALSE;
}
}

function ActCurpSexoEstadoCivil($Nitavu_real, $Curp, $Sexo, $EdoCivil, $autorizo){
require("config.php");
    $sql = "UPDATE empleados SET curp='".$Curp."', sexo='".$Sexo."', estadocivil='".$EdoCivil."' WHERE nitavu='".$Nitavu_real."'";
    //echo $sql;
    
    //$resultado = $conexion -> query($sql);
   if ($conexion->query($sql) == TRUE){
        historia($autorizo, "Actualizo el Curp (".$Curp."), Sexo(".$Sexo.") y Estado Civil(".$EdoCivil.") de ".nitavu_nombre($Nitavu_real));        
        
    }
    else {echo "error".$sql;}
}

function correo_limite(){
require("config.php");
$sql = "SELECT count(*) as n from correos where fecha=CURDATE()";
//echo $sql;
$limite=0;
$nuc='';
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
	if($f = $resultado -> fetch_array())
	{	$alerta = 50;
		$limite = $correo_limite - $f['n'];
		if ($limite<0){$limite=0;}		
		
		if ($limite<=0)
			{
			notificacion_add ('2809', 'chat', $fecha, '2809', '<b class=alerta>ALERTA</b> de correos: Se ha llegado a su limite, quedan '.$limite.' de '.$correo_limite.'. Se han intentado enviar correos, se han marcado como no enviados.'); //alerta juanjonitavu
			
			notificacion_add ('1533', 'chat', $fecha, '2809', '<b class=alerta>ALERTA</b> de correos: Se ha llegado a su limite, quedan '.$limite.' de '.$correo_limite.'. Se han intentado enviar correos, se han marcado como no enviados.'); //alerta javier
			return 0;
		}else {
				return $limite;
			}

		
	} 

}
else {return 0;}

}


function comida_entro($id, $autorizo, $quien){
require("config.php");
$sql = "UPDATE empleados_salidas_temporal SET registro_entrada='".$hora."' wHERE id='".$id."'";
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
	historia($autorizo, "Dio entrada al pase de comida con ID".$id.", de ".nitavu_nombre($quien)." (".$quien.")");
	//header('location:../vigilancia3.php');
	return TRUE;
}
else {
	return FALSE;
}
}




function nocomida_salio($id, $autorizo, $quien){
require("config.php");
$sql = "UPDATE empleados_salidas_temporal SET registro_salida='".$hora."' wHERE id='".$id."'";
//echo $sql;
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
	historia($autorizo, "Dio salida del pase de salida Oficial con ID".$id.", de ".nitavu_nombre($quien)." (".$quien.")");
	//header('location:../vigilancia3.php');
	return TRUE;
}
else {
	return FALSE;
}
}

function nocomida_entro($id, $autorizo, $quien){
require("config.php");
$sql = "UPDATE empleados_salidas_temporal SET registro_entrada='".$hora."' wHERE id='".$id."'";
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
	historia($autorizo, "Dio entrada al pase de salida Oficial con ID".$id.", de ".nitavu_nombre($quien)." (".$quien.")");
	//header('location:../vigilancia3.php');
	return TRUE;
}
else {
	return FALSE;
}
}
















function ingresos_totales($IdDelegacion, $fecha_){ //NOTIFICACIONES, TOTALES
require("config.php");		
$sql = "select sum(ingresos) as ingreso from ingresos_vivienda where IdDelegacion=".$IdDelegacion." and fecha='".$fecha_."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{if ($f['ingreso']==''){return 0; }else {return $f['ingreso'];}
	
	} else {return 0;}
}


function notifi_total($nitavu){ //NOTIFICACIONES, TOTALES
require("config.php");		
$sql = "SELECT count(*) as n FROM notificaciones WHERE (nitavu='".$nitavu."')";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['n'];} else {return 0;}
}

function notifi_sinleer($nitavu){ //NOTIFICACIONES, TOTALES
require("config.php");		
$sql = "SELECT count(*) as n FROM notificaciones WHERE (nitavu='".$nitavu."' AND lectura_hora='')";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['n'];} else {return 0;}
}



function notifi_leidas($nitavu){ //NOTIFICACIONES, TOTALES
require("config.php");		
$sql = "SELECT count(*) as n FROM notificaciones WHERE (nitavu='".$nitavu."' AND lectura_hora<>'')";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['n'];} else {return 0;}
}


function notifi_enviadassinleer($nitavu){ //NOTIFICACIONES, TOTALES
require("config.php");		
$sql = "SELECT count(*) as n FROM notificaciones WHERE (nitavu_manda='".$nitavu."'AND lectura_hora='') ORDER by entregar_fecha ASC";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['n'];} else {return 0;}
}

function comida_aut($nitavu){ //consulta experiencia del usuario
require("config.php");		
$sql = "select comida from empleados where nitavu='".$nitavu."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{
	return $f['comida'];

	} else {
		return 0;
	}
}


function comida_salida($nitavu){ //consulta experiencia del usuario
require("config.php");		
$sql = "select * from empleados_salidas_temporal where nitavu='".$nitavu."' and fecha='".$fecha."' and asunto='COMIDA'";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{
	return $f['registro_salida'];

	} else {
		return FALSE;
	}
}


function comida_salida2($nitavu, $fecha2){ //consulta experiencia del usuario
require("config.php");		
$sql = "select * from empleados_salidas_temporal where nitavu='".$nitavu."' and fecha='".$fecha2."' and asunto='COMIDA'";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{
	return $f['registro_salida'];

	} else {
		return FALSE;
	}
}


function comida_estado($nitavu){ //consulta experiencia del usuario
require("config.php");		
$sql = "select * from empleados_salidas_temporal where nitavu='".$nitavu."' and fecha='".$fecha."' and asunto='COMIDA'";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	if ($f['autorizo_nitavu']==''){
			return "Esperando autorizacion del pase para comida de las ".$f['hora_desde'];
		} else
		{
			return "Pase Autorizado por ".nitavu_nombre($f['autorizo_nitavu'])." y disponible en Caseta";

		}
		

} else {
	return FALSE;
}
}


function comida_trestante($nitavu){ //consulta experiencia del usuario
require("config.php");		
$sql = "select * from empleados_salidas_temporal where nitavu='".$nitavu."' and fecha='".$fecha."' and asunto='COMIDA'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{
		$thasta = tiempo_sumar_hr(comida_aut($f['nitavu']), $f['registro_salida']);
		$trestante = tiempo_restar_hr($hora, $thasta);
	if (($f['registro_entrada']=='00:00:00') AND ($f['registro_salida']<>'00:00:00')  ){//aun esta afuera
		if ($trestante > comida_salida($f['nitavu']) ){
			if ($f['registro_entrada']=='' or $f['registro_entrada']=='00:00:00')
			{
				$trestante = tiempo_restar_hr($thasta, $hora);//se hace con la hora el retraso ya que no entro
			}
			else {
				$trestante = tiempo_restar_hr($thasta, $f['registro_entrada']);//se hace con la hora de registro de entrada, para dar cuanto se retraso
			}
			return "-".$trestante;	
		} else {return $trestante."";}

	} else {
		// ya se realizo el pase
		if (($f['registro_entrada']=='00:00:00') AND ($f['registro_salida']=='00:00:00')  ){
			//Pase sin realizar
			return '*'.'Salida: '.hora12($f['registro_salida']).", Entrada: ".hora12($f['registro_entrada']);	
		} else {
			return '+'.'Salida: '.hora12($f['registro_salida']).", Entrada: ".hora12($f['registro_entrada']);
		}
		
	}
	
		

	} else {
		return 0;
	}
}

function paselibre($npase){ //consulta experiencia del usuario
require("config.php");		
$sql = "SELECT id from empleados_salidas_temporal where id='".$npase."'";
	$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
	{return FALSE;	}
	else {return  TRUE;}

}

function xd($idap, $nitavu){ //consulta experiencia del usuario
require("config.php");		
$sql = "SELECT	* from xd where idap='".$idap."' and iduser='".$nitavu."'";
	$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
	{return $f['c'];	}
	else {return  0;}

}

function xd_update($idap, $nitavu){//actualizar sd
require("config.php");		
$sql = "SELECT	* from xd where idap='".$idap."' and iduser='".$nitavu."'";
	$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
	{//update
		$c = xd($idap, $nitavu) + 1;
		$sql="UPDATE xd SET c='".$c."' WHERE idap='".$idap."' and iduser='".$nitavu."'";
		if ($conexion->query($sql) == TRUE)
			{return 'Actualizada entrada para'.$idap.' con '.$nitavu;}
		else {return 'No se actualizo';}



	
	} else { // insert
		$sql = "INSERT INTO xd (idap, iduser, c, fecha) VALUES ('".$idap."', '".$nitavu."', 1, '".$fecha."')";
		if ($conexion->query($sql) == TRUE)
			{return 'Agregada entrada para '.$idap.' con '.$nitavu;}
		else {return 'No se agrego entrada';}

	}




}
















//REPORTEADOR

function pendientes_($nitavu){
require("config.php");
$id_aplicacion ="ap54"; //ap06=Permisos de Aplicacion
if (sanpedro($id_aplicacion, $nitavu)==TRUE){

$sql2="select * from pendientes_direccion where pendiente_estado = 0 ";
$r2 = $conexion -> query($sql2);
//$msg = nombre_corto($nitavu,0)." ".nombre_corto($nitavu,1)." Tienes ";
$msg = "";
$pendientes =  "";
$c = 0;
while($f = $r2 -> fetch_array())
	{//$df recorre la lista de las delegaciones
	$pendientes = $pendientes.$f['pendiente_nombre'].", ";
	$c= $c +1;
	}
//$msg = $msg.$c." pendientes, en la Mesa de Temas. "	.$pendientes.".";
if ($c>0) {//habla($msg);
}
return $msg;
}

}






function habla($quedigo){
	echo "<script>responsiveVoice.speak('".$quedigo."', 'Spanish Latin American Female', {volume: 100}); </script>";
	//echo "<script>responsiveVoice.speak('".$quedigo."', 'Spanish Female', {volume: 100}); </script>";
      //onclick='responsiveVoice.speak("Hola Mundo", "Spanish Latin American Female");' type='button' value='🔊 Play'  class='btn btn-default'/>
   //responsiveVoice.speak('Probando Sintetizador de audio', 'Spanish Female', {volume: 100});
   //responsiveVoice.speak('Probando Sintetizador de audio', 'Spanish Latin American Female', {volume: 100});
   //onstart: StartCallback, onend: EndCallback}
}



	function pendiente_direccion_participantes_faltan($tema){
	require("config.php");		
		$sql = "
		SELECT
			*,
			nitavu as nitavu_,
			(select count(*) from pendientes_direccion_votos where votador=nitavu_ and pendiente_nombre='".$tema."') as participacion

		FROM
			aplicaciones_permisos
		WHERE
			idapp = 'ap54'
			
		";
		$cuantos=0;
		$r2 = $conexion -> query($sql); while($lista = $r2 -> fetch_array())
		{
			if ($lista['participacion']==0){
				$cuantos = $cuantos +1;
			}

		}
		return $cuantos;

	}


	function pendiente_direccion_total(){
	require("config.php");		
		$sql = "SELECT	count(*) as n FROM	pendientes_direccion WHERE
		MONTH (pendiente_fecha) = MONTH (NOW())	AND 	YEAR (pendiente_fecha) = YEAR (NOW())";
		$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
		{return $f['n'];	}
		else {return  $f['n'];}

	}


	function pendiente_direccion_sinaprobar(){
	require("config.php");		
		$sql = "SELECT	count(*) as n FROM	pendientes_direccion WHERE
		MONTH (pendiente_fecha) = MONTH (NOW())	AND 	YEAR (pendiente_fecha) = YEAR (NOW()) AND pendiente_estado=0";
		$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
		{return $f['n'];	}
		else {return  $f['n'];}

	}


	function pendiente_direccion_ok(){
	require("config.php");		
		$sql = "SELECT	count(*) as n FROM	pendientes_direccion WHERE
		MONTH (pendiente_fecha) = MONTH (NOW())	AND 	YEAR (pendiente_fecha) = YEAR (NOW()) AND pendiente_estado=1";
		$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
		{return $f['n'];	}
		else {return  $f['n'];}

	}

	function pendiente_direccion_x(){
	require("config.php");		
		$sql = "SELECT	count(*) as n FROM	pendientes_direccion WHERE
		MONTH (pendiente_fecha) = MONTH (NOW())	AND 	YEAR (pendiente_fecha) = YEAR (NOW()) AND pendiente_estado=2";
		$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
		{return $f['n'];	}
		else {return  $f['n'];}

	}

	function pendiente_direccion_voto($nombre, $nitavu_){
	require("config.php");		
		$sql = "SELECT  * FROM pendientes_direccion_votos WHERE pendiente_nombre='".$nombre."' AND votador='".$nitavu_."'";
		$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
		{return $f['voto'];	}
		else {return 'FALSE';}

	}


	function pendiente_direccion_votos($nombre, $voto){
	require("config.php");		
		$sql = "SELECT  count(*) as n FROM pendientes_direccion_votos WHERE pendiente_nombre='".$nombre."' AND voto='".$voto."'";
		$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
		{return $f['n'] ;	}
		else {return 0;}
	}





function embarque_permiso($id){
require("config.php");
$sql = "SELECT * FROM embarques_proveedores WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ return $f['nombre'];} else {return "";}
}



function embarque_proveedor($id){
require("config.php");
$sql = "SELECT * FROM embarques_proveedores WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ return $f['nombre'];} else {return "";}
}

function embarque_rastreo_url($id){
require("config.php");
$sql = "SELECT * FROM embarques_guias WHERE guia='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ 
		return embarque_proveedor_url($f['paqueteria_id']);
	}
}


function embarque_asignado($id){
require("config.php");
$sql = "SELECT * FROM embarques_guias WHERE guia='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ 
		return $f['asignacion'];
	}
}

function embarque_origen($id){
require("config.php");
$sql = "SELECT * FROM embarques_guias WHERE guia='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ 
		return $f['origen'];
	}
}


function embarque_destino($id){
require("config.php");
$sql = "SELECT * FROM embarques_guias WHERE guia='".$id."'";
//echo "<h5>".$sql."</h5>";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ 
		
		return $f['destino'];
	}
}



function embarque_recibido($id){
require("config.php");
$sql = "SELECT * FROM embarques_guias WHERE guia='".$id."'";
//echo "<h5>".$sql."</h5>";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ 
		return $f['recibido'];
	}
}



function embarque_codigo($id){
require("config.php");
$sql = "SELECT * FROM embarques_guias WHERE guia='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ 
		return $f['token'];
	}
}

function embarque_descripcion($id){
require("config.php");
$sql = "SELECT * FROM embarques_guias WHERE guia='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ 
		return $f['descripcion'];
	}
}

function embarque_proveedor_url($id){
require("config.php");
$sql = "SELECT * FROM embarques_proveedores WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{ return $f['url_rastreo'];} else {return "";}
}


function reporte_tabla($id){
require("config.php");
$sql = reporte_sql($id);
$cuantas_columnas=0;
$tabla_titulos = "";
$r2 = $conexion -> query($sql); while($finfo = $r2->fetch_field())
{//OBTENER LAS COLUMNAS

        /* obtener posición del puntero de campo */
        $currentfield = $r2->current_field;       
       	$tabla_titulos=$tabla_titulos."<th>".$finfo->name."</th>";
       	$cuantas_columnas = $cuantas_columnas + 1;        
}

$tabla_contenido=""; $cuantas_filas=0;
$r = $conexion -> query($sql); while($f = $r-> fetch_row())
{//LISTAR COLUMNAS

    $tabla_contenido = $tabla_contenido."<tr>";        
    for ($i = 1; $i <= $cuantas_columnas; $i++) {      
        $tabla_contenido = $tabla_contenido."<td>".$f[$i-1]."</td>";       
        }

    $tabla_contenido = $tabla_contenido."</tr>";
    $cuantas_filas = $cuantas_filas + 1;        
}


$t = "<h3>".reporte_titulo($id)."</h3>";
$t = $t."<label class='reporte_descripcion'
>".reporte_descripcion($id)."</label>";

$t = $t."<table class='tabla'>".$tabla_titulos.$tabla_contenido."</table>";
return $t;

}





function reporte_tabla2($id){
require("../unica/config.php");
$sql = reporte_sql($id);
$cuantas_columnas=0;
$tabla_titulos = "<tr>";
$r2 = $conexion -> query($sql); while($finfo = $r2->fetch_field())
{//OBTENER LAS COLUMNAS

        /* obtener posición del puntero de campo */
        $currentfield = $r2->current_field;       
       	$tabla_titulos=$tabla_titulos.'<td style="background-color:black; color:white;">'.$finfo->name."</td>";
       	$cuantas_columnas = $cuantas_columnas + 1;        
}
$tabla_titulos = $tabla_titulos."</tr>";
$tabla_contenido=""; $cuantas_filas=0;
$r = $conexion -> query($sql); while($f = $r-> fetch_row())
{//LISTAR COLUMNAS

    $tabla_contenido = $tabla_contenido."<tr>";        
    for ($i = 1; $i <= $cuantas_columnas; $i++) {      
        $tabla_contenido = $tabla_contenido."<td>".$f[$i-1]."</td>";       
        }

    $tabla_contenido = $tabla_contenido."</tr>";
    $cuantas_filas = $cuantas_filas + 1;        
}


$t = '<h5 style="text-align:center">'.reporte_titulo($id)."</h5>";
$t = $t.'<div style="font-size: xx-small;">'.reporte_descripcion($id)."</div>";

$t = $t.'<table border="1" style="font-size: xx-small;">'.$tabla_titulos.$tabla_contenido."</table>";
return $t;

}






?>

<?php





function nfoto($consulta){
require("config.php");
$sql = "SELECT * FROM contadores WHERE id='0'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	if ($consulta==TRUE) {
	return $f['nfoto'];
}
else
{ // sino es consulta entonces aumentarle y aumentar el contador de ceropapel
// la diferencia entre ceropapel y este, es que cero papel se multiplica
// por las copias que se entregan o con copia, para estadistica de cuanto se ha ahorrado
	$n2 = $f['nfoto'] + 1;
	$sql="UPDATE contadores SET nfoto='".$n2."' WHERE id='0'";
	$resultado = $conexion -> query($sql);
	if ($conexion->query($sql) == TRUE) {
	return $f['nfoto'];
	}
	else {return  FALSE;}
	}
	}
	else
	{ return FALSE;}
}





function npase($consulta){
require("config.php");
$sql = "SELECT * FROM contadores WHERE id='0'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	if ($consulta==TRUE) {
	return $f['npase_idenficador'].$f['npase'];
}
else
{ // sino es consulta entonces aumentarle y aumentar el contador de ceropapel
// la diferencia entre ceropapel y este, es que cero papel se multiplica
// por las copias que se entregan o con copia, para estadistica de cuanto se ha ahorrado
	$n2 = $f['npase'] + 1;
	//$n2 = $f['npase_idenficador'].$n2;
	$sql="UPDATE contadores SET npase='".$n2."' WHERE id='0'";
	$resultado = $conexion -> query($sql);
	if ($conexion->query($sql) == TRUE) {
	return $f['npase_identificador'].$n2;
	}
	else {return  FALSE;}
	}
	}
	else
	{ return FALSE;}
}




function token_correo($consulta){
require("config.php");
$sql = "SELECT * FROM contadores WHERE id='0'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	if ($consulta==TRUE) {
	return $f['npase_idenficador'].$f['correo_token'];
}
else
{ // sino es consulta entonces aumentarle y aumentar el contador de ceropapel
// la diferencia entre ceropapel y este, es que cero papel se multiplica
// por las copias que se entregan o con copia, para estadistica de cuanto se ha ahorrado
	$n2 = $f['correo_token'] + 1;
	//$n2 = $f['npase_idenficador'].$n2;
	$sql="UPDATE contadores SET correo_token='".$n2."' WHERE id='0'";
	$resultado = $conexion -> query($sql);
	if ($conexion->query($sql) == TRUE) {
	return $f['npase_identificador'].$n2;
	}
	else {return  FALSE;}
	}
	}
	else
	{ return FALSE;}
}





function tarjeta_dpto($id){
require("config.php");
$sql = "SELECT * FROM cat_gerarquia WHERE id='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{

	echo "<div id='dpto_box'>";
	echo "<h1>".$f['nombre']."</h1>";
	echo "<table><tr>";
	$foto= "<img src='fotos/".$f['titular'].".jpg' class='foto_org1'>";
	//echo "<td width='20%'>".$foto."</td>";
	echo "<td>";
	echo "<h3>".nitavu_nombre($f['titular'])."</h3>";
	echo "<nav>";
	echo "<a href='' class=''><img src='icon/tel.png' style='width: 18px;'><span class='pc'>".nitavu_tel($f['titular'])."</span></a>";
	echo "<a href='' class=''><img src='icon/mail.png' style='width: 18px;'></a>";
	echo "<a href='' class=''><img src='icon/msg.png' style='width: 18px;'></a>";
	echo "</nav>";
	echo "</td>";
	echo "</tr></table>";
	echo "</div>";


}
else
{ return "";}



}

function sentimental($msg){
  	  echo "<div id='sentimental'>";
      echo "<table border='0'>";
      echo "<tr>";
      echo "<td width='50px' align='left' valign='middle'><img src='icon/404.png'></td>";
      echo "<td align='center'  valign='middle'>".$msg."</td>";
      echo "</tr>";
      echo "</table>";
      echo "</div>";

}


function req_alertas($nitavu_, $sugerencia){
require("config.php");
//funcion que otorga acceso a las aplicaciones
$sql = "INSERT INTO req_conceptos_alertas
(nitavu, fecha, id_concepto)
VALUES
('$nitavu_', '$fecha', '$sugerencia')";
if ($conexion->query($sql) == TRUE)
{	//echo "ok";
	return 'TRUE';
}
	else
{	//echo $sql;
	return 'FALSE';
}
}

function req_sugerencia($nitavu_, $sugerencia){
require("config.php");
//funcion que otorga acceso a las aplicaciones
$sql = "INSERT INTO req_conceptos_sugerencias
(nitavu, fecha, concepto)
VALUES
('$nitavu_', '$fecha', '$sugerencia')";
if ($conexion->query($sql) == TRUE)
{	//echo "ok";
	return 'TRUE';
}
	else
{	//echo $sql;
	return 'FALSE';
}
}





function nivel_detalle($n, $clase){
require("config.php");
$sql = "SELECT * FROM aplicaciones_nivelusuario WHERE id='".$n."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{

	echo "<div id='nivel_detalle'>";
	echo "<table border='0'><tr>";
	echo "<td align='right' width='50%'><img src='icon/nivel_".$n.".png' class='".$clase."'></td><td align='left' width='50%'>".$f['modo']."</td>";
	echo "</tr></table>";
	echo "</div>";
}
else
{
	
}

}








function doc_historia($id, $programa, $folio, $delegacion){
require("config.php");
$sql = "SELECT * FROM digital_itavu WHERE id_documento='".$id."' and programa='".$programa."' and folio='".$folio."' and delegacion='".$delegacion."'";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['historia'];
}
else
{
	return '';
}

}






function manzana_pendientes($id_colonia, $id_municipio, $manzana){
require("config.php");
$sql = "
SELECT
	count(*) as n
FROM
	notificadores_visitas
WHERE
	id_colonia = '".$id_colonia."' and manzana='".$manzana."' and visitada='' and id_municipio='".$id_municipio."'
";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['n'];
}
else
{
	return '';
}

}


function cat_edo_vivienda($id){
require("config.php");
$sql = "
select cat_estado_lotes_vivienda.EstatusLote
from cat_estado_lotes_vivienda
WHERE cat_estado_lotes_vivienda.IdEstatus = ".$id."

";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['EstatusLote'];
}
else
{
	return '';
}

}



function escritura_lista($contrato){
require("config.php");
$sql = "SELECT * FROM tmp_escrituraslistas WHERE contrato='".$contrato."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return 'TRUE';
}
else
{
	return 'FALSE';
}

}





function notificadores_colonia_faltan($colonia){
require("config.php");
$sql = "SELECT
	count(*) AS total,
	(
		SELECT
			count(*)
		FROM
			notificadores_visitas
		WHERE
			id_colonia = '".$colonia."'
		AND visitada = 'TRUE'
	) AS visitadas,
	(
		100 / count(*) * (
			SELECT
				count(*)
			FROM
				notificadores_visitas
			WHERE
				id_colonia = '".$colonia."'
			AND visitada = 'TRUE'
		)
	) AS porcentaje
FROM
	notificadores_visitas
WHERE
	id_colonia = '".$colonia."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	$faltan = $f['total'] - $f['visitadas'];
	return $faltan;
}
else
{
	return 'FALSE';
}

}




function notificadores_colonia_manzana_avance($colonia, $m){
require("config.php");
$sql = "SELECT
	count(*) AS total,
	(
		SELECT
			count(*)
		FROM
			notificadores_visitas
		WHERE
			id_colonia = '".$colonia."'
		AND visitada = 'TRUE'
		AND manzana = '".$m."'
	) AS visitadas,
	(
		100 / count(*) * (
			SELECT
				count(*)
			FROM
				notificadores_visitas
			WHERE
				id_colonia = '".$colonia."'
			AND visitada = 'TRUE'
		)
	) AS porcentaje
FROM
	notificadores_visitas
WHERE
	id_colonia = '".$colonia."'
	AND manzana='".$m."'
	";

//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	if ($f['porcentaje']<=100 AND $f['porcentaje']>1)
	{ return $f['porcentaje'];}
	else {
		return "0";
	}
}
else
{
	return 'FALSE';
}

}




function notificadores_colonia_avance($colonia){
require("config.php");
$sql = "SELECT
	count(*) AS total,
	(
		SELECT
			count(*)
		FROM
			notificadores_visitas
		WHERE
			id_colonia = '".$colonia."'
		AND visitada = 'TRUE'
	) AS visitadas,
	(
		100 / count(*) * (
			SELECT
				count(*)
			FROM
				notificadores_visitas
			WHERE
				id_colonia = '".$colonia."'
			AND visitada = 'TRUE'
		)
	) AS porcentaje
FROM
	notificadores_visitas
WHERE
	id_colonia = '".$colonia."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['porcentaje'];
}
else
{
	return 'FALSE';
}

}




function id_estadoubv_nombre($id){
require("config.php");
$sql = "SELECT * FROM cat_estado_ubv WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['nombre'];
}
else
{
	return '';
}

} 






function id_transpaso($id){
require("config.php");
$sql = "SELECT * FROM cat_transpasos  WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['nombre'];
}
else
{
	return '';
}

} 







function id_estado_lote_nombre($id){
require("config.php");
$sql = "SELECT * FROM cat_estado_lotes WHERE id='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['nombre'];
}
else
{
	return '';
}

} 

function programa_nombre($id){
require("config.php");
$sql = "SELECT * FROM cat_programa WHERE IdPrograma='".$id."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['Programa'];
}
else
{
	return '';
}

}

function colonia_nombre($id, $id_municipio){
require("config.php");
$sql = "SELECT * FROM cat_colonias WHERE (IdColonia='".$id."' AND IdMunicipio='".$id_municipio."')";
//echo $sql;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	return $f['Colonia'];
}
else
{
	return '';
}

}


function lotes_colonia($IdMunicipio, $IdColonia){
require("config.php");
$sql = "SELECT * FROM lotes WHERE (IdMunicipio='".$IdMunicipio."' AND IdColonia='".$IdColonia."')";
$r = $conexion -> query($sql);
$r_count = $r -> num_rows;
return $r_count;
}


function lotes_($IdMunicipio, $IdColonia){
require("config.php");
$sql = "SELECT * FROM lotes WHERE (IdMunicipio='".$IdMunicipio."' AND IdColonia='".$IdColonia."')";
$r = $conexion -> query($sql);
$r_count = $r -> num_rows;
return $r_count;
}






function app_descripcion($idapp){
require("config.php");
$sql = "SELECT * FROM aplicaciones WHERE idapp='".$idapp."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
	return $f['nombre']."(".$f['descripcion'].")";
	

}
else
{ return FALSE;}
}


function notifica_mejora($idapp, $m, $quien){
require("config.php");
$sqlx = "SELECT * FROM aplicaciones_permisos WHERE (idapp='".$idapp."' )";
$c=0;
$r= $conexion -> query($sqlx);	
while($f2 = $r -> fetch_array())
				{
				//echo nitavu_nombre($f2['nitavu'])."<br>";
				notifica ($f2['nitavu'], "Mejora de la Aplicacion ".app_nombre($idapp), date('Y-m-d'), $quien,$m); //SU PERSONAL
				//echo notifica ($f2['nitavu'], "Mejora de la Aplicacion ", $fecha, $quien,$m); //SU PERSONAL
				
				$c= $c +1;
				}
return $c;				

}



function dpto_id($id){
require("config.php");
if ($id>0){
$sql = "SELECT * FROM cat_gerarquia WHERE id='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['nombre'];
}
else
{ return FALSE;}
}else {return '';}
}



function soytitular($id){
require("config.php");
$sql = "SELECT * FROM cat_gerarquia WHERE titular='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['id'];
}
else
{ return 'FALSE';}
}


function titular($id){
require("config.php");
$sql = "SELECT * FROM cat_gerarquia WHERE id='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['titular'];
}
else
{ return 'FALSE';}
}

function notifica ($usuario, $asunto, $entregar_fecha, $itavu_manda, $contenido){

	return notificacion_add ($usuario, $asunto, $entregar_fecha, $itavu_manda, $contenido);
}



function dir_list_count($ruta){
$directorio = opendir("
$ruta"); //ruta actual
$tmp="";
$path=".".$ruta;
$directorio=dir($path);
//echo "Directorio ".$path.":<br><br>";
$c=0;
while ($archivo = $directorio->read())
{
	if ($archivo<>"." and $archivo<>".."){
    //$tmp=$tmp.$archivo.", ";
	$c= $c +1;

	}


}

return $c;
$directorio->close();

}



function dir_list($ruta){
$ruta=$ruta.".";
$directorio = opendir("$ruta"); //ruta actual
$tmp="";
$path=".".$ruta;
$directorio=dir($path);
echo "Directorio ".$path."<br><br>";
while ($archivo = $directorio->read())
{
	if ($archivo<>"." and $archivo<>".."){
    $tmp=$tmp.$archivo.", ";
	}


}

return $tmp;
$directorio->close();

}



function FTP_existe_archivo($archivo){
	$id_ftp=FTP_conectar(); //Obtiene un manejador y se conecta al Servidor FTP
	$fileSize = ftp_size($id_ftp, FTP_ruta().$archivo);
	if ($fileSize != -1) {return "TRUE";} else {return "FALSE";}
}

function FTP_descargar($archivo){
	$lista="";
	$id_ftp=FTP_conectar(); //Obtiene un manejador y se conecta al Servidor FTP
	ftp_pasv($id_ftp, true);

	// intenta descargar $server_file y guardarlo en $local_file
	if (ftp_get($id_ftp, "tmp/".$archivo, "".$archivo, FTP_BINARY)) {
    		//return "Se ha guardado satisfactoriamente en $archivo\n";
			return "TRUE";
	} else {
		    return "FALSE";
	}

	 
}


function FTP_lista(){
	$lista="";
	$id_ftp=FTP_conectar(); //Obtiene un manejador y se conecta al Servidor FTP
	$files = ftp_nlist($id_ftp, '.');
	foreach ($files as $file) {

	$lista = $lista.FTP_ruta().$file . "<br>";
	}
	return $lista;
}
function FTP_leer($archivo_nombre){
$ftp_url="ftp://".FTP_USER.":".FTP_PASSWORD."@".FTP_SERVER.FTP_DIR.$archivo_nombre;
//ftp://desarrollo2:jpedraza@ftp.172.16.90.3/home/desarrollo2/public_html/tam.png

echo $ftp_url;
$archivo = fopen ($ftp_url, "r");
if (!$archivo) {
		return "ERROR";
		
}else {return $archivo;}

}

function FTP_conectar(){
//Permite conectarse al Servidor FTP
	
	$id_ftp=ftp_connect(FTP_SERVER,FTP_PORT); //Obtiene un manejador del Servidor FTP
	//$id_ftp=ftp_ssl_connect(FTP_SERVER,FTP_PORT); //Obtiene un manejador del Servidor FTP
	ftp_login($id_ftp,FTP_USER,FTP_PASSWORD); //Se loguea al Servidor FTP
	ftp_pasv($id_ftp, TRUE); //Establece el modo de conexión
return $id_ftp; //Devuelve el manejador a la función
}


function FTP_subir_post($archivo_local,$archivo_remoto){
//if (isset($_FILES[$archivo_local])){	
	//Sube archivo de la maquina Cliente al Servidor (Comando PUT)
	$id_ftp=FTP_conectar(); //Obtiene un manejador y se conecta al Servidor FTP
    

	if (ftp_put($id_ftp,FTP_ruta().$archivo_remoto,$_FILES[$archivo_local]['tmp_name'],FTP_BINARY)){
		return "TRUE";} else {return "FALSE";}
	//Sube un archivo al Servidor FTP en modo Binario
	ftp_quit($id_ftp); //Cierra la conexion FTP
//} else {return "FALSE";}
}



function FTP_subir($archivo_local,$archivo_remoto){
	//Sube archivo de la maquina Cliente al Servidor (Comando PUT)
	$id_ftp=FTP_conectar(); //Obtiene un manejador y se conecta al Servidor FTP
    
	if (ftp_put($id_ftp,FTP_ruta().$archivo_remoto,$archivo_local,FTP_BINARY)){
		return "TRUE";} else {return "FALSE";}
	//Sube un archivo al Servidor FTP en modo Binario
	ftp_quit($id_ftp); //Cierra la conexion FTP
}


function FTP_ruta(){
	//Obriene ruta del directorio del Servidor FTP (Comando PWD)
	$id_ftp=FTP_conectar(); //Obtiene un manejador y se conecta al Servidor FTP
	$Directorio=ftp_pwd($id_ftp); //Devuelve ruta actual p.e. "/home/willy"
	ftp_quit($id_ftp); //Cierra la conexion FTP
return $Directorio."/"; //Devuelve la ruta a la función
}




//select * from aplicaciones_historia where date_format (fecha_lanzamiento, '%m') = date_format (now(), '%m')
function app_new($id){
require("config.php");
$sql = "
SELECT	COUNT(*) AS n
FROM		aplicaciones_historia
WHERE		(date_format(fecha_lanzamiento, '%m') = date_format(now(), '%m'))
AND  idapp='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
	{
		if ($f['n']<=0)
		{
			
		}
		else 
		{
			return $f['n'];}
		}
else
	{return " ";}
}


function isURL($url){ 
  if(getimagesize($url))
  	{
    	return true; 
    }else
    {
    	return false;
 	}


}



function IMGweb_tam($clase, $img){
$url="https://www.flickr.com/photos/gobtam/";
$html = file_get_contents($url);
$doc = new DOMDocument();
@$doc->loadHTML($html);
$tags = $doc->getElementsByTagName('img');
$n= 1;
foreach ($tags as $tag) {
			$img = $tag->getAttribute('src'); //echo "<img src='$img'>"."<br>";	
			if (strlen($img)>4){
				$ext = substr($img,-3);
				//if (($ext <> 'gif') and ($ext <> 'png')){
					$srcs[$n]=$img;

					$n= $n+1;
				//	}
			}
	
}

$imgs_encontradas = $n;
$n_rnd =  rand(1, $imgs_encontradas);//seleccionar una en las que se encontro

if ($img=="TRUE"){
	return "<img title='$n_rnd' value='".$srcs[$n_rnd]."' class='$clase'>"; // la enviamos armada con la clase seleccionada
	//return "<img src='".$srcs[0]."' class='$clase'>"; // la enviamos armada con la clase seleccionada
}else{
	return "".$srcs[$n_rnd].""; 
}

}






function Google_images($palabra, $clase, $img){
$palabra= str_replace(" ", "+", $palabra);	
$url="https://www.google.com.mx/search?q=$palabra&source=lnms&tbm=isch&sa=X&ved=0ahUKEwiJs5L4hcPWAhXBLSYKHR9qDGAQ_AUICigB&biw=1680&bih=941";
$html = file_get_contents($url);
$doc = new DOMDocument();
@$doc->loadHTML($html);
$tags = $doc->getElementsByTagName('img');
$n= 1;
foreach ($tags as $tag) {
			$img = $tag->getAttribute('src'); //echo "<img src='$img'>"."<br>";	
			if (strlen($img)>4){
				$ext = substr($img,-3);
				//if (($ext <> 'gif') and ($ext <> 'png')){
					$srcs[$n]=$img;

					$n= $n+1;
				//	}
			}
	
}

$imgs_encontradas = $n;
$n_rnd =  rand(1, $imgs_encontradas);//seleccionar una en las que se encontro

if ($img=="TRUE"){
	return "<img title='$n_rnd' value='".$srcs[$n_rnd]."' class='$clase'>"; // la enviamos armada con la clase seleccionada
	//return "<img src='".$srcs[0]."' class='$clase'>"; // la enviamos armada con la clase seleccionada
}else{
	return "".$srcs[$n_rnd].""; 
}

}


function copiar_img($origen, $destino)
{$msgE='';
$imagen = file_get_contents($origen); // guardamos la imagen en la variable
//file_put_contents('images/imagen_copiada.jpg',$imagen); // guardamos la

	if(file_put_contents($destino,$imagen))
	{ $msgE= "TRUE";
	} else{
		//$msgE= "No se actualizo ".$nombredelcontrol.", ";
		$msgE= "FALSE";
	}
	

return $msgE;
}




function ping($host, $port, $timeout) 
{ 
  $tB = microtime(true); 
  $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
  if (!$fP) { return "down"; } 
  $tA = microtime(true); 
  //return round((($tA - $tB) * 1000), 0)." ms"; 
  return round((($tA - $tB) * 1000), 0).""; 

  //Echoing it will display the ping if the host is up, if not it'll say "down".
//echo ping("www.google.com", 80, 10);

}




function estado_laboral($id){
require("config.php");
$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
	{
	return $f['estado'];

	}
	else
	{
	return '';
	}

}



function mensaje($mensaje, $link){
if ($link=="") {$link = "../index2.php";}
$tipo = substr($mensaje, 0,5);    // devuelve "ef"

if ($tipo=='ERROR'){
	echo '<div id="modal_error"></div>';}
	else{
	echo '<div id="modal_oscuro"></div>';}
	

//echo '<div class="padre">';
//echo '<span class="hijo">';
		if ($tipo=='ERROR'){echo '<div id="msg_error">';}
		else{echo '<div id="mensaje">';}
		echo '<p>'.$mensaje.'</p>';
		echo '<a class="btn btn-default" href="'.$link.'">Aceptar</a>  ';
		//echo '<a class="btn btn-cancel" href="'.$link.'">Cerrar</a>';
		//habla($mensaje);
		echo '</div>';
		
//echo '</span>';
//echo '</div>';

}

	



function ltbox_foto($nitavu, $src, $origen){
$mensaje="
";	

echo '<div id="modal_box"></div>';

//echo '<div class="padre">';
//echo '<span class="hijo">';
		
		echo '<div id="ltbox_foto">';

		echo "<img src='".$src."'>";


		echo '<a class="btn btn-secundario" href="'.$origen.'">Regresar</a>  ';
		echo '</div>';
		
//echo '</span>';
//echo '</div>';

}

	



function mensaje_mantenimiento($nitavu, $link){
$mensaje="
<h3 class='alerta'>SECCION EN MANTENIMIENTO</H3>
Disculpe las molestias ".nombre_corto($nitavu,0).", estamos trabajando en mejoras para esta seccion. En breve estara disponible.<br>
Gracias por su comprensión.
";	
if ($link=="") {$link = "../index2.php";}

echo '<div id="modal_mantenimiento"></div>';

//echo '<div class="padre">';
//echo '<span class="hijo">';
		
		echo '<div id="mensaje">';
		echo '<p>'.$mensaje.'</p>';
		echo '<a class="btn btn-default" href="'.$link.'">Aceptar</a>  ';
		//echo '<a class="btn btn-cancel" href="'.$link.'">Cerrar</a>';
		echo '</div>';
		
//echo '</span>';
//echo '</div>';

}

	


function habla_notis($pases, $notis){
$tmp="";

$tmp = $tmp.'<script >';

$tmp = $tmp.'var sounds = new Array(';
if ($notis<9){
for ($x = 0; $x <= $notis; $x++) {
    $tmp = $tmp.'new Audio("audio/ring_.wav"), ';
	
} 
}
else
{
	$tmp = $tmp.'new Audio("audio/ring_.wav"), ';
}

	
//$tmp = $tmp.'new Audio("audio/ring_01.wav"), ';	
// $tmp = $tmp.'new Audio("audio/tiene.mp3"), ';

// if ($pases>0){
// 	if ($pases>9) {$tmp = $tmp.'new Audio("audio/algunos.mp3"), ';}
// 	if ($pases==1) {$tmp = $tmp.'new Audio("audio/1.mp3"), ';}
// 	if ($pases==2) {$tmp = $tmp.'new Audio("audio/2.mp3"), ';}
// 	if ($pases==3) {$tmp = $tmp.'new Audio("audio/3.mp3"), ';}
// 	if ($pases==4) {$tmp = $tmp.'new Audio("audio/4.mp3"), ';}
// 	if ($pases==5) {$tmp = $tmp.'new Audio("audio/5.mp3"), ';}
// 	if ($pases==6) {$tmp = $tmp.'new Audio("audio/6.mp3"), ';}
// 	if ($pases==7) {$tmp = $tmp.'new Audio("audio/7.mp3"), ';}
// 	if ($pases==8) {$tmp = $tmp.'new Audio("audio/8.mp3"), ';}
// 	if ($pases==9) {$tmp = $tmp.'new Audio("audio/9.mp3"), ';}	
	
// 	if ($pases==1) {$tmp = $tmp.'new Audio("audio/pase.mp3"), ';} else
// 	{$tmp = $tmp.'new Audio("audio/pases.mp3"), ';}

// 	if ($notis>0) {
// 		$tmp = $tmp.'new Audio("audio/y.mp3"), ';
// 	}
// } 


// if ($notis>0){
// 	if ($notis>9) {$tmp = $tmp.'new Audio("audio/algunas.mp3"), ';}
// 	if ($notis==1) {$tmp = $tmp.'new Audio("audio/1.mp3"), ';}
// 	if ($notis==2) {$tmp = $tmp.'new Audio("audio/2.mp3"), ';}
// 	if ($notis==3) {$tmp = $tmp.'new Audio("audio/3.mp3"), ';}
// 	if ($notis==4) {$tmp = $tmp.'new Audio("audio/4.mp3"), ';}
// 	if ($notis==5) {$tmp = $tmp.'new Audio("audio/5.mp3"), ';}
// 	if ($notis==6) {$tmp = $tmp.'new Audio("audio/6.mp3"), ';}
// 	if ($notis==7) {$tmp = $tmp.'new Audio("audio/7.mp3"), ';}
// 	if ($notis==8) {$tmp = $tmp.'new Audio("audio/8.mp3"), ';}
// 	if ($notis==9) {$tmp = $tmp.'new Audio("audio/9.mp3"), ';}	
	
// 	if ($notis==1) {$tmp = $tmp.'new Audio("audio/notificacion.mp3"), ';} else
// 	{$tmp = $tmp.'new Audio("audio/notificaciones.mp3"), ';}
// } 
// $tmp = $tmp.'new Audio("audio/pendientes.mp3"), ';


$tmp = $tmp.'
	new Audio("audio/silencio.wav"));
var i = -1;
playSnd();

function playSnd() {
    i++;
    if (i == sounds.length) return;
    sounds[1].currentTime = -5;
    sounds[i].addEventListener("ended", playSnd);
    sounds[i].play();
}
</script>

';

if (($notis>0) or ($pases>0)){
	echo $tmp;
}

}





function grafica_bar_histo($campo, $tabla,$especial,$titulo, $w, $h){
	require("config.php");
	$tmp="";
				$c=0;
				$sql2="SELECT DISTINCT ".$campo."  as campo FROM ".$tabla;
				$r2 = $conexion -> query($sql2);
					$tmp= $tmp."['Fecha','Notificaciones'],";
				while($a = $r2 -> fetch_array())
					{
					$sqlx = "SELECT COUNT(*) as n FROM ".$tabla." where ".$campo."='".$a['campo']."'";
					//echo $sqlx;
					$rc= $conexion -> query($sqlx);
					if($f = $rc -> fetch_array())
						{
							$c= $f['n'];
						}
						//$c= solicitudes_jornada_apoyo($a['apoyo']);
						if ($especial=="nitavu") {
									$tmp= $tmp."['".nitavu_nombre($a['campo'])."',".$c."],";
						}
						
						if ($especial=="apoyo") {
									$tmp= $tmp."['".$a['campo']."',".$c."],";
						}
						
						
				
					//echo $a['apoyo']."=".$c."<br>";
					}
				$data =   trim($tmp, ',');
				$grafica = "
<script type='text/javascript'>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);
function drawChart() {
var data = google.visualization.arrayToDataTable([
				".$data."
				
				
				]);
var options = {
title: 'Top de Notificaciones Diarias',
hAxis: {title: 'Fechas',  titleTextStyle: {color: '#333'}},
vAxis: {minValue: 0}
};
var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
chart.draw(data, options);
}
</script>
<div id='chart_div' style='width: 80%; height: 500px; padding:0px; margin:0px; display:inline-block;'></div>
";
return $grafica;
}
function grafica_pastel($campo, $tabla,$especial,$titulo, $w, $h){
require("config.php");
$tmp="";
$c=0;
$sql2="SELECT DISTINCT ".$campo."  as campo FROM ".$tabla;
$r2 = $conexion -> query($sql2);
while($a = $r2 -> fetch_array())
{
$sqlx = "SELECT COUNT(*) as n FROM ".$tabla." where ".$campo."='".$a['campo']."'";
//echo $sqlx;
$rc= $conexion -> query($sqlx);
if($f = $rc -> fetch_array())
{
$c= $f['n'];
}
//$c= solicitudes_jornada_apoyo($a['apoyo']);
if ($especial=="nitavu") {
$tmp= $tmp."['".nitavu_nombre($a['campo'])."',".$c."],";
}

if ($especial=="apoyo") {
$tmp= $tmp."['".$a['campo']."',".$c."],";
}



//echo $a['apoyo']."=".$c."<br>";
}
$data =   trim($tmp, ',');
$grafica = "
<script type='text/javascript'>
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(drawChart);
function drawChart() {
// Create the data table.
var data = new google.visualization.DataTable();
data.addColumn('string', 'Topping');
data.addColumn('number', 'Slices');
data.addRows([
				".$data."
				
				
				]);
// Set chart options
var options = {'title':'".$titulo."',
'width':".$w.",
'height':".$h."};
// Instantiate and draw our chart, passing in some options.
var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
chart.draw(data, options);
}
</script>
<div id='grafica'><div id='chart_div'></div></div>
";
return $grafica;
}

function presolicitud_no($consulta, $cuantas){
require("config.php");
$sql = "SELECT * FROM contadores WHERE id='0'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
if ($consulta==TRUE) {
return $f['presolicitud'];
}
else
{ // sino es consulta entonces aumentarle y aumentar el contador de ceropapel
// la diferencia entre ceropapel y este, es que cero papel se multiplica
// por las copias que se entregan o con copia, para estadistica de cuanto se ha ahorrado
$docdigital = $f['presolicitud'];
$docdigitalnew = $docdigital + 1;
$ceropapel = $f['ceropapel'] + $cuantas;
$sql="UPDATE contadores SET presolicitud='".$docdigitalnew."', ceropapel='".$ceropapel."' WHERE id='0'";
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
return $f['presolicitud'];
}
else {return  FALSE;}
}
}
else
{ return FALSE;}
}


function notificaciones_faltantes($delegacion_id,$brigada_id){
require("config.php");
$sql = "SELECT * FROM notificaciones_old WHERE (id_delegacion='".$delegacion_id."' AND brigada_id='".$brigada_id."' AND folio<>'X')";
$r = $conexionmigra -> query($sql);
$r_count = $r -> num_rows;
return $r_count;

}
function tantos($edad){
$msg="";
if ($edad<=0) {$msg=$edad;}
if (($edad>=20) AND ($edad<30) ){$msg="Veintitantos";}
if (($edad>=30) AND ($edad<40) ){$msg="Treintitantos";}
if (($edad>=40) AND ($edad<50) ){$msg="Cuarentaytantos";}
if (($edad>=50) AND ($edad<60) ){$msg="Cincuentaitantos";}
if (($edad>=60) AND ($edad<70) ){$msg="Sesentaytantos";}
if (($edad>=70) AND ($edad<80) ){$msg="Ochentaytantos";}
if (($edad>=80) AND ($edad<90) ){$msg="Noventaytantos";}
if ($edad>=90) {$msg=$edad;}
return $msg;
}
function cumples_estemes(){
require("config.php");
$sql = "select * from empleados where date_format (fecha_nacimiento, '%m') = date_format (now(), '%m')";
$r = $conexion -> query($sql);
$msg ="";
$c=0;
while($f = $r -> fetch_array())
{
$c= $c+1;
$msg= $msg."".nombre_corto($f['nitavu'],0).", ";
}
if ($c>0){
return "Este mes hay ".$c." cumpleañeros, "." Haz <a class='alerta' href='cumples_lista.php'>clic aqui para saber mas.</a>";
}
else{
return "";
}

}



function cumples_estemes_quienes(){
require("config.php");
$sql = "select * from empleados where date_format (fecha_nacimiento, '%m') = date_format (now(), '%m')";
$r = $conexion -> query($sql);
$msg ="";
$c=0;
while($f = $r -> fetch_array())
{
$c= $c+1;
$msg= $msg."".nombre_corto($f['nitavu'],0).", ";
}
if ($c>0){
$habla = 	"Este mes tenemos ".$c." cumpleañeros, ".$msg;
//habla();
echo "<script>responsiveVoice.speak('".$habla."', 'Spanish Latin American Female', {volume: 100}); </script>";

return "Este mes tenemos ".$c." cumpleañeros, ";
}
else{
return "";
}

}




function edad($fechanacimiento){
list($ano,$mes,$dia) = explode("-",$fechanacimiento);
$ano_diferencia  = date("Y") - $ano;
$mes_diferencia = date("m") - $mes;
$dia_diferencia   = date("d") - $dia;
if ($dia_diferencia < 0 || $mes_diferencia < 0)
$ano_diferencia--;
return $ano_diferencia;
}
function completar1($id){
require("config.php");
$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
$rc= $conexion -> query($sql);
$completar=0;
$msg="";
if($f = $rc -> fetch_array())
{
if ($f['domicilio_calle']=='') {
$completar= $completar + 1;
$msg = $msg. "Calle, ";
}
if ($f['domicilio_num_ext']=='') {
$completar= $completar + 1;
$msg = $msg. "Num Ext, ";
}
// if ($f['domicilio_num_int']=='') {
// 	$completar= $completar + 1;
// 	$msg = $msg. "Num Int, ";
// }
// if ($f['domicilio_entrecalles']=='') {
// 	$completar= $completar + 1;
// 	$msg = $msg. "Entre Calles, ";
// }
if ($f['domicilio_ciudad']=='') {
$completar= $completar + 1;
$msg = $msg. "Ciudad, ";
}
if ($f['domicilio_colonia']=='') {
$completar= $completar + 1;
$msg = $msg. "colonia, ";
}
if ($f['domicilio_cp']=='') {
$completar= $completar + 1;
$msg = $msg. "CP, ";
}
if ($f['estadocivil']=='0' or $f['estadocivil']=='') {
$completar= $completar + 1;
$msg = $msg. "Estado Civil, ";
}
// if ($f['telefono2']=='') {
// 	$completar= $completar + 1;
// 	$msg = $msg. " * Telefono de Casa, ";
// }
if ($f['telefono_movil']=='') {
$completar= $completar + 1;
$msg = $msg. "Celular, ";
}
// if ($f['correoelectronico']=='') {
// 	$completar= $completar + 1;
// 	$msg = $msg. "correo ";
// }
if ($completar>0){
return "Tiene ".$completar." faltantes de llenar. (".$msg.")";
}
}
else
{ return '';}
}
function municipio_nombre($id){
require("config.php");
$sql = "SELECT * FROM cat_municipios WHERE IdMunicipio='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['nombre'];

}
else
{
return '';
}


}
function pase_id_nombre($id){
require("config.php");
$sql = "SELECT * FROM empleados_salidas_temporal WHERE id='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return nitavu_nombre($f['nitavu']);

}
else
{
return '';
}


}
function brigada($id){
require("config.php");
$sql = "SELECT * FROM brigadas WHERE id='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['nombre'];

}
else
{
return '';
}


}
function beneficiario_nombre_curp($id){
require("config.php");
$sql = "SELECT * FROM beneficiarios WHERE curp='".$id."'";
$rc= $conexion -> query($sql);
//echo $sql;
if($f = $rc -> fetch_array())
{
return $f['nombre']." ".$f['paterno']." ".$f['materno'];

}
else
{
return '';
}


}
function beneficiario_old_curp($id){
require("config.php");
$sql = "SELECT * FROM beneficiarios_old WHERE id_solicitante='".$id."'";
$rc= $conexionmigra -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['curp'];

}
else
{
return '';
}


}
function beneficiario_idsol($id){
require("config.php");
$sql = "SELECT * FROM beneficiarios WHERE id_solicitante='".$id."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['nombre']." ".$f['paterno']." ".$f['materno'];

}
else
{
return '';
}


}
function notificaciones_entregadas($delegacion_id,$brigada_id){
require("config.php");
$sql = "SELECT * FROM notificaciones_old WHERE (id_delegacion='".$delegacion_id."' AND brigada_id='".$brigada_id."' AND folio='X')";
$r = $conexionmigra -> query($sql);
$r_count = $r -> num_rows;
return $r_count;

}
function notificaciones_disponibles($delegacion_id, $brigada_id){
require("config.php");
$sql = "SELECT * FROM notificaciones_old WHERE (id_delegacion='".$delegacion_id."' AND brigada_id='".$brigada_id."')";
$r = $conexionmigra -> query($sql);
$r_count = $r -> num_rows;
return $r_count;

}
function beneficiario_updatetmp($consulta){
require("config.php");
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {

return 'OK';
}
else {
return 'X';
}
}
function beneficiario_historia($curp, $string){
require("config.php");
$sql = "SELECT * FROM beneficiarios WHERE curp='".$curp."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{//
$string2 = $f['historia']."<br>".$fecha." : ".$hora." ".$string;

$sql="UPDATE beneficiarios SET historia='".$string2."' WHERE curp='".$curp."'";
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
return TRUE;
}
else {return  FALSE;}
}
else
{ return FALSE;}
}
function beneficiario_notirepetidas($curp, $string){
require("config.php");
$sql = "SELECT * FROM beneficiarios WHERE curp='".$curp."'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{//
$string2 = $f['noti_repetidas']."<br>".$fecha." : ".$hora." ".$string;

$sql="UPDATE beneficiarios SET noti_repetidas='".$string2."' WHERE curp='".$curp."'";
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE) {
return TRUE;
}
else {return  FALSE;}
}
else
{ return FALSE;}
}



function buscar($action, $placeholder, $brig){
echo '                <div id="beta_buscar">';
	echo '<form action="'.$action.'" method="get">';
		if (isset($brig)){
		echo '<input type="hidden" name="'.$brig.'" id="brig" value="">';
		}
		echo '<table broder="1" width="100%"><tr>';
			echo '<td>                    <input required="required" type="text" id="beta_buscar_input" name="busqueda" placeholder="'.$placeholder.'" /></td>';
			echo '<td align="right" width="15px">                    
			<button id="beta_buscar_boton">
			<img  src="icon/buscar.png"></button>
			</td>';
		echo '</tr></table>';
	echo '</form>';
echo '                </div>';
//onclick="searchToggle(this, event)
}




function titulo($string){
echo "<span id='titulares'>";
	echo "<b>".$string."</b>";
echo "</span>";
}
function limpiar_tel($s){
$s = str_replace("-","",$s);
$s = str_replace("(","",$s);
$s = str_replace(")","",$s);
$s = str_replace(" ","",$s);
//para ampliar los caracteres a reemplazar agregar lineas de este tipo:
//$s = str_replace(“caracter-que-queremos-cambiar”,”caracter-por-el-cual-lo-vamos-a-cambiar”,$s);
return $s;
}
function  es_https(){
if (isset($_SERVER['HTTPS'])) {
// Codigo a ejecutar si se navega bajo entorno seguro.
return TRUE;
} else {
// Codigo a ejecutar si NO se navega bajo entorno seguro.
return FALSE;
}
}
function hora12($hora_){
return date("g:ia",strtotime($hora_));
}
function nacimiento($nitavu_){
require("config.php");
$sql = "SELECT * FROM empleados WHERE nitavu='".$nitavu_."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['fecha_nacimiento'];

}


}

function notificadores_pendientes($nitavu){
require("config.php");
$id_delegacion = midelegacion_id($nitavu);
if ($id_delegacion=='') {
	$sql = "SELECT	count(*) as n FROM	notificadores_visitas WHERE	visitada = ''";
} else {$sql = "SELECT	count(*) as n FROM	notificadores_visitas WHERE	visitada = ''AND delegacion = '".$id_delegacion."'";}
//echo $sql;
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
	return $f['n'];
}



}



function notificadores_pendientes_vobo($nitavu, $nivel){
require("config.php");
if ($nivel=='2') {
	$id_delegacion = midelegacion_id($nitavu);
	$sql = "
	SELECT
	count(*) as n
	FROM
		notificadores_visitas
	WHERE
		
		vobo='' and visitada<>''
		AND delegacion='".$id_delegacion."'
	";

	}

if ($nivel=='1') {
	$sql="SELECT
	count(*) as n
	FROM
		notificadores_visitas
	WHERE
		visitada<>'' AND vobo=''
	";
}
//echo $sql;
$rc= $conexion -> query($sql);
$r_count = $rc -> num_rows;
if ($r_count>0){
$msg="";
if($f = $rc -> fetch_array())
{
	return $f['n'];
}
}


}



function misdelegaciones_conid($id){
require("config.php");
$sql2="SELECT * FROM notificaciones_config WHERE id='".$id."' ";
$r2 = $conexion -> query($sql2);
$tmp ="";
while($df = $r2 -> fetch_array())
{//$df recorre la lista de las delegaciones
$tmp = $tmp.$df['delegacion_id'].", ";
}
$midelegacion = midelegacion($id);
$p2 = explode(" ",$midelegacion);
$midelegacion_lugar =  $p2[1]; // esto muestra la primera palabra
if ($midelegacion_lugar=='Coordinacion'){}
else{$midelegacion_id = busca_id_delegacion($midelegacion_lugar);}
$tmp = $tmp." ".strtoupper($midelegacion_id).".";
return $tmp;
//$delegaciones_aut = substr($delegaciones_aut, 0, -2); //quita la ultima coma.
}



function misdelegaciones($id){
require("config.php");
$sql2="SELECT * FROM notificaciones_config WHERE id='".$id."' ";
$r2 = $conexion -> query($sql2);
$tmp ="";
while($df = $r2 -> fetch_array())
{//$df recorre la lista de las delegaciones
	$tmp = $tmp.delegacion_id($df['delegacion_id']).", ";
}

$midelegacion = midelegacion($id);
$p2 = explode(" ",$midelegacion);
$midelegacion_lugar =  $p2[1]; // esto muestra la primera palabra

if ($midelegacion_lugar=='Coordinacion'){}
	else{$midelegacion_id = busca_id_delegacion($midelegacion_lugar);}


$tmp = $tmp." ".strtoupper($midelegacion_lugar).".";

return $tmp;
//$delegaciones_aut = substr($delegaciones_aut, 0, -2); //quita la ultima coma.
}



function solicitudes_jornada_apoyo($a){
require("config.php");
$sqlx = "SELECT COUNT(*) as n FROM solicitudes_jornada where apoyo='".$a."'";
$rc= $conexion -> query($sqlx);
if($f = $rc -> fetch_array())
{

return $f['n'];
}
else
{
return '';
}


}



function soydelegacion($id){
require("config.php");
$dpto = nitavu_dpto($id);
$sql = "SELECT * FROM cat_gerarquia WHERE (id='".$dpto."')";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
	if($f['nivel']=='Del'){
		return $f['id'];
	}else{
	return FALSE;
	}
}
else
{
return '';
}
}



function delegacion_id($id){
require("config.php");
$sql = "SELECT * FROM cat_delegaciones WHERE (id='".$id."')";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{

return $f['nombre'];
}
else
{
return '';
}


}
function beneficiario_old_nombre($id){
require("config.php");
$sql = "SELECT * FROM empleados WHERE (nitavu='".$nitavu_."' AND departamento like '%legacion%')";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{

return $f['departamento'];
}
else
{
return 'OFICINAS CENTRALES';
}


}


function midelegacion($nitavu_){
require("config.php");
$sql = "SELECT
	cat_gerarquia.id,
	cat_gerarquia.nombre,
	cat_gerarquia.nivel,
	empleados.nitavu,
	empleados.dpto
FROM
	cat_gerarquia,
	empleados
WHERE
	empleados.dpto = cat_gerarquia.id
AND	
	empleados.nitavu = '".$nitavu_."'
and 
	cat_gerarquia.nivel = 'Del'";
	//echo $sql;
$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
{
	return $f['nombre'];
}
else
{
	return 'OFICINAS CENTRALES';
}

}




function midelegacionconid($nitavu_){
require("config.php");
$sql = "SELECT
	cat_gerarquia.id,
	cat_gerarquia.nombre,
	cat_gerarquia.nivel,
	empleados.nitavu,
	empleados.dpto
FROM
	cat_gerarquia,
	empleados
WHERE
	empleados.dpto = cat_gerarquia.id
AND	
	empleados.nitavu = '".$nitavu_."'
and 
	cat_gerarquia.nivel = 'Del'";
	//echo $sql;
$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
{
	return $f['id'];
}
else
{
	return 'OFICINAS CENTRALES';
}

}

function midelegacion_id($nitavu_){
require("config.php");
$sql = 
"
SELECT
	cat_gerarquia.id,
	cat_gerarquia.nombre AS 'delegacion_nombre',
	cat_gerarquia.nivel,
	empleados.nitavu,
	empleados.dpto,
	cat_delegaciones.nombre AS 'delegacion',
	empleados.nombre,
	cat_delegaciones.id as 'iddel'
FROM
	cat_gerarquia,
	empleados,
	cat_delegaciones
WHERE
	empleados.dpto = cat_gerarquia.id
AND cat_gerarquia.nivel = 'Del'
AND cat_gerarquia.nombre LIKE CONCAT('%', cat_delegaciones.nombre, '%')
AND nitavu = '".$nitavu_."'

";
//echo $sql;
$rc= $conexion -> query($sql); if($f = $rc -> fetch_array())
{
	return $f['iddel'];
}
else
{
	return '';
}
}


function acceso($nitavu_,$nip){
require("config.php");
$sql = "SELECT * FROM empleados WHERE (nitavu='".$nitavu_."' AND nip='".$nip."')";
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE)
{
return TRUE;
}
else
{
return FALSE;
}
}


function fecha_nacimiento($nitavu_,$fechanac, $guarda){
require("config.php");
if ($guarda == TRUE){
$sql="UPDATE empleados SET fecha_nacimiento='".$fechanac."' WHERE (nitavu='".$nitavu_."')";
$resultado = $conexion -> query($sql);
if ($conexion->query($sql) == TRUE)
{
return TRUE;
}
else {return FALSE;}
}
else
{
$sql = "SELECT * FROM empleados WHERE nitavu='".$nitavu_."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['fecha_nacimiento']=='';

}

}

}
function asistencia_entrada($nitavu_){
require("config.php");
$sql = "SELECT * FROM asistencia WHERE nitavu='".$nitavu_."' AND fecha='".$fecha."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['entrada'];
}
else
{ return '';}
}
function busca_id_delegacion($string){
require("config.php");

$sql = "SELECT * FROM cat_delegaciones WHERE (nombre like'%".$string."%')";
//echo $sql;
$rc= $conexion -> query($sql);
//echo $sql;
if($f = $rc -> fetch_array())
{
return $f['id'];
}
else
{
return 'X';
}


}
function asistencia_salida($nitavu_){
require("config.php");
$sql = "SELECT * FROM asistencia WHERE nitavu='".$nitavu_."' AND fecha='".$fecha."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
return $f['salida'];
}
else
{ return "";}
}
function nombre_corto($nitavu_,$x){
require("config.php");
$sql = "SELECT * FROM empleados WHERE nitavu='".$nitavu_."'";
$rc= $conexion -> query($sql);
$msg="";
if($f = $rc -> fetch_array())
{
$cadena = $f['nombre'];
$parte = explode(" ",$cadena);
return $parte[$x]; // esto muestra la primera palabra
}
else
{ return FALSE;}
}
function pase_estado($empleado, $desde, $hasta, $todos){
require("config.php");
$tmp = "";
$tmp = "<div class='normal bold grande'>Mostrando pases de ".fecha_larga($desde)." a ".fecha_larga($hasta)." | ".nitavu_nombre($empleado).":</div>";
$tmp = $tmp. "<table border='0' class='tabla'>";
	$tmp = $tmp. "<tr class='tabla_titulo'>";
		//echo "<td>ID</td>";
		$tmp = $tmp. "<td>ID de Pase</td>";
		$tmp = $tmp. "<td>SOLICITANTE</td>";
		//$tmp = $tmp. "<td>Solicitado</td>";
		$tmp = $tmp. "<td>DESCRIPCION</td>";
		$tmp = $tmp. "<td width='30%'>ESTADO</td>";
	$tmp = $tmp. "</tr>";
	if ($todos=="TRUE"){
	$sql = "SELECT * FROM empleados_salidas_temporal WHERE (fecha>='".$desde."' AND fecha<='".$hasta."') ORDER by solicito_fecha, solicito_hora";
	}
	else
	{
	$sql = "SELECT * FROM empleados_salidas_temporal WHERE (nitavu='".$empleado."'AND fecha>='".$desde."' AND fecha<='".$hasta."')";
	}
	$r = $conexion -> query($sql);
	//echo $sql;
	$aut="";
	$m="";
	while($f = $r -> fetch_array())
	{ // resultado de la busqueda.................
	$tmp=$tmp. "<tr class='tabla_tr tabla'>";
		//echo "<td>".$f['id']."</td>";
		$tmp=$tmp. "<td>".$f['id']."</td><td> ".nitavu_nombre($f['nitavu'])." (".$f['nitavu'].")</td>";
		//$tmp=$tmp. "<td>".$f['solicito_hora']."</td>";
		$tmp=$tmp. "<td> <b>Para: ".fecha_larga($f['fecha'])." para las ".$f['hora_desde']."</b><br>";
		$tmp=$tmp. "Solicitado: ".fecha_larga($f['fecha']).""."</td>";
		
		if ($f['autorizo_nitavu']==''){
			$aut="PENDIENTE AUTORIZACION";
		} else {$aut=$m."<br>Autorizado por ".nitavu_nombre($f['autorizo_nitavu'])." a las ".$f['autorizo_hora']." a ".fecha_larga($f['autorizo_fecha']);}


		if ($f['rechazada']=='TRUE'){
			$aut="<b class='alerta bold'>RECHAZADO </b>por ".nitavu_nombre($f['autorizo_nitavu'])." a las ".$f['autorizo_hora']." de ".fecha_larga($f['autorizo_fecha']);
			} 
		else {
			$m = "<b class='normal bold'>Registro:</b>, Salida ".$f['registro_salida']." y regreso ".$f['registro_entrada'];
			
		}
		
		$tmp = $tmp. "<td>".$f['asunto'].": ".$f['justificacion']."<br>".$aut."</td>";
	$tmp = $tmp. "</tr>";
	}//while
$tmp = $tmp. "</table>";
return $tmp;
}
function pases_desfase($nitavu_, $desde, $hasta, $detalles){
require("config.php");
$sql = "SELECT * FROM empleados_salidas_temporal WHERE (registro_entrada>hora_hasta) AND
(solicito_fecha>='".$desde."') AND (solicito_fecha<='".$hasta."') AND
(nitavu='".$nitavu_."') ORDER by dpto ASC";
$rc= $conexion -> query($sql);
$resumen="";
$r2="";
$total_retraso="00:00:00";

while($f = $rc -> fetch_array()) {
$retraso =  tiempo_restar_hr($f['registro_salida'],$f['registro_entrada']);
$lapso =  tiempo_restar_hr($f['hora_desde'],$f['hora_hasta']);
$lapsoytole =tiempo_sumar_hr($lapso,$tolerancia);
//$resumen=$resumen."b=".$lapsoytole;
if ($retraso>$lapsoytole){
$total_retraso = tiempo_sumar_hr($total_retraso,$retraso);
if ($f['registro_salida']>$f['hora_desde']){
$desfase_permiso = tiempo_restar_hr($f['hora_desde'],$f['registro_salida']);
if ($desfase_permiso>$tolerancia){
//$r2="Salio despues de la hora solicitada ".$desfase_permiso."min";
}
}
else
{
$desfase_permiso = tiempo_restar_hr($f['registro_salida'],$f['hora_desde']);
if ($desfase_permiso>$tolerancia){
//$r2="Salio ".$desfase_permiso." minutos antes de la hora que solicito";
}

}
$resumen = $resumen. "<cite>".fecha_larga($f['solicito_fecha'])." [".$lapso."min] para las ".$f['hora_desde']."<span class='tenue'>(Salida: ".$f['registro_salida'].", Regreso: ".$f['registro_entrada'].")</span> </cite>";
$resumen = $resumen. "<a target='_blank' href='auscencia_pase_estado.php?empleado=".$f['nitavu']."&desde=".$f['solicito_fecha']."&hasta=".$f['solicito_fecha']."'> Ver detalles del pase ".$f['id']."</a><br>";
if ($r2<>""){$resumen=$resumen.$r2;}
$resumen= $resumen."";
}
}
if ($detalles=='TRUE'){
if ($resumen<>""){return "<strong class='alerta grande'>".$total_retraso." min.</strong><br><lu>".$resumen."</lu>";}
}
else
{
return $total_retraso;
}

}
function dia_semana2($fecha_){
$dias = array('Lun','Mar','Mie','Jue','Vie','Sab','Dom');
$n= date('N', strtotime($fecha_));
$fecha = $dias[$n-1];
return $fecha;
//return $fecha_;
//return date('N', strtotime($fecha_));
}
function dia_semana($fecha_){
$dias = array('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
$n= date('N', strtotime($fecha_));
$fecha = $dias[$n-1];
return $fecha;
//return $fecha_;
//return date('N', strtotime($fecha_));
}


function fecha_lite($fecha_){
//return  dia_semana($fecha_)." ".date('d/m/Y', strtotime($fecha_));
$mes = date('m', strtotime($fecha_));
$mes = (int)$mes -1;
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$mes_largo = $meses[$mes];
//$fecha_salida = dia_semana($fecha_)." ".date('d', strtotime($fecha_))." de ".$mes_largo." de ".date('Y', strtotime($fecha_));;

$fecha_salida = "<div id='fecha_lite'>";
$fecha_salida = $fecha_salida."<span id='fecha_lite_dia'>".dia_semana($fecha_)."</span>";
$fecha_salida = $fecha_salida."<span id='fecha_lite_dia2'>".date('d', strtotime($fecha_))."</span><br>";
$fecha_salida = $fecha_salida."<span id='fecha_lite_resto'>".$mes_largo." de ".date('Y', strtotime($fecha_))."</span>";


$fecha_salida = $fecha_salida."</div>";


return $fecha_salida;
}


function fecha_larga($fecha_){
//return  dia_semana($fecha_)." ".date('d/m/Y', strtotime($fecha_));
$mes = date('m', strtotime($fecha_));
$mes = (int)$mes -1;
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$mes_largo = $meses[$mes];
$fecha_salida = dia_semana($fecha_)." ".date('d', strtotime($fecha_))." de ".$mes_largo." de ".date('Y', strtotime($fecha_));;

return $fecha_salida;
}

function fecha_larga_cumple($fecha_){
//return  dia_semana($fecha_)." ".date('d/m/Y', strtotime($fecha_));
$mes = date('m', strtotime($fecha_));
$mes = (int)$mes -1;
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$mes_largo = $meses[$mes];
$fecha_salida = dia_semana($fecha_)." ".date('d', strtotime($fecha_))." de ".$mes_largo;

return $fecha_salida;
}





function itop($ip){
require("config.php");
$sql = "SELECT * FROM ipinterface WHERE (ipaddress='".$ip."')";
$r2 = $conexionitop -> query($sql);
$tmp="";
while($f = $r2 -> fetch_array())
{//Categorias de Aplicaciones


echo $f['comment']. " [ mac:".$f['macaddress'].", Gateway: ".$f['ipgateway']."]";


}
}
function pases_detalles($id){
require("config.php");
$sql = "SELECT * FROM empleados_salidas_temporal WHERE (id='$id')";

//$pases = $r -> num_rows;
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
return "".$f['id']." de las ".$f['hora_desde']."hr a las ".$f['hora_hasta']." para el ".$f['fecha']." de asunto ".$f['asunto'];
}
else
{
return FALSE;
}


}


function pases_quien($id){
require("config.php");
$sql = "SELECT * FROM empleados_salidas_temporal WHERE (id='$id')";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
{
return $f['nitavu'];
}
else
{
return FALSE;
}
}


function cuanto_empleados(){
require("config.php");
$sql = "select count(*) as n from empleados where estado=''";
$rc= $conexion -> query($sql);
	if($f = $rc -> fetch_array())
		{return $f['n'];}
	else {return 0;}

}




function cuanto_empleados_correo(){
require("config.php");
$sql = "select count(*) as n from empleados where estado='' and correoelectronico<>''";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['n'];}
	else {return 0;}

}


function cuanto_empleados_correo_ok(){
require("config.php");
$sql = "select count(*) as n from empleados where estado='' and correoelectronico<>'' and correo_vobo='1'";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{return $f['n'];}
	else {return 0;}

}



function carga_apps_free_info(){
require("config.php");
$sql = "SELECT * FROM aplicaciones WHERE (idapcat='8') AND version >'0'";
$r2 = $conexion -> query($sql);
$tmp="";
while($f = $r2 -> fetch_array())
{//Categorias de Aplicaciones


$tmp = $tmp."<article>";
	$tmp = $tmp. "<a href='".$f['vinculo']."'>";
		$tmp = $tmp. "<table border='0'><tr><td>";
			
			if ($f['icono']<>"") {
			$tmp = $tmp. "<img src='./icon/".$f['icono']."' class='icono_menu'>";
			}
		$tmp = $tmp. "</td><td><b class='normal menu_font_n'>".$f['nombre'].":</b> ";
		$tmp = $tmp. "<cite class='tenue menu_font_d pc'>".$f['descripcion']."</cite>";
	$tmp = $tmp. "</td></tr></table></a></article>";
	
	
	}
	return $tmp;
	}
	function carga_apps_free(){
	require("config.php");
	$sql = "SELECT * FROM aplicaciones WHERE (idapcat='2') AND version >'0'";
	$r2 = $conexion -> query($sql);
	$tmp="";
	while($f = $r2 -> fetch_array())
	{//Categorias de Aplicaciones
	
	
	$tmp = $tmp."<article>";
		$tmp = $tmp. "<a href='".$f['vinculo']."'>";
			$tmp = $tmp. "<table border='0'><tr><td>";
				
				if ($f['icono']<>"") {
				$tmp = $tmp. "<img src='./icon/".$f['icono']."' class='icono_menu'>";
				}
			$tmp = $tmp. "</td><td><b class='normal menu_font_n'>".$f['nombre'].":</b> ";
			$tmp = $tmp. "<cite class='tenue menu_font_d pc'>".$f['descripcion']."</cite>";
		$tmp = $tmp. "</td></tr></table></a></article>";
		
		
		}
		return $tmp;
		}

function carga_apps($idapcat, $nitavu, $todas){
		require("config.php");
		$sql = "SELECT * FROM aplicaciones WHERE (idapcat='".$idapcat."') AND version >'0'";
		$r2 = $conexion -> query($sql);
		$tmp="";
		while($f = $r2 -> fetch_array())
		{//Categorias de Aplicaciones
		if ($todas==FALSE) {
			if (sanpedro($f['idapp'],$nitavu)==TRUE){
			$tmp = $tmp."<article>";
			$tmp = $tmp. "<a href='".$f['vinculo']."'>";
				$tmp = $tmp. "<table border='0'><tr><td>";
					
					if ($f['icono']<>"") {
					$tmp = $tmp. "<img src='./icon/".$f['icono']."' class='icono_menu'>";
					}
				$tmp = $tmp. "</td><td><b class='normal menu_font_n'>".$f['nombre'].":</b> ";
				$tmp = $tmp. "<cite class='tenue menu_font_d pc'>".$f['descripcion']."</cite>";
			$tmp = $tmp. "</td><td width='10px'>";
			
			// if (app_new($f['idapp'])>=1){
			// 	$tmp= $tmp."<b class='pc new'><a class='tchico tenue' href='info_acercade.php#".$f['idapp']."'>".app_new($f['idapp'])."</a></b>";}
				
			$tmp = $tmp."</td></tr></table></a></article>";
			
			}
		}
		else{

			$tmp = $tmp."<article>";
			$tmp = $tmp. "<a href='".$f['vinculo']."'>";
				$tmp = $tmp. "<table border='0'><tr><td>";
					
					if ($f['icono']<>"") {
					$tmp = $tmp. "<img src='./icon/".$f['icono']."' class='icono_menu'>";
					}
				$tmp = $tmp. "</td><td><b class='normal menu_font_n'>".$f['nombre'].":</b> ";
				$tmp = $tmp. "<cite class='tenue menu_font_d pc'>".$f['descripcion']."</cite>";
			$tmp = $tmp. "</td><td width='10px'>";
			
			// if (app_new($f['idapp'])>=1){
			// 	$tmp= $tmp."<b class='pc new'><a class='tchico tenue' href='info_acercade.php#".$f['idapp']."'>".app_new($f['idapp'])."</a></b>";}
				
			$tmp = $tmp."</td></tr></table></a></article>";
			
			
		}
		} 

		return $tmp;
}



			function visitas($nitavu){
			require("config.php");
			$nivel = aplicacion_nivel('ap15', $nitavu);
			$dpto = nitavu_dpto($nitavu);
			if ($nivel=='1') {
			$sql = "SELECT * FROM visitas WHERE (autorizo_nitavu='')";
			$r= $conexion -> query($sql);
			$visitas = $r -> num_rows;
			return $visitas;
			}
			
			if ($nivel=='2') {
			$sql = "SELECT * FROM visitas WHERE (autorizo_nitavu='' AND dpto='".$dpto."')";
			$r= $conexion -> query($sql);
			$visitas = $r -> num_rows;
			return $visitas;
			}
			
			}
			function pases($nitavu){
			require("config.php");
			$nivel = aplicacion_nivel('ap12', $nitavu);
			$dpto = nitavu_dpto($nitavu);
			$pases = 0;
			if ($nivel==1) {
			$sql = "SELECT * FROM empleados_salidas_temporal WHERE (autorizo_nitavu='' AND solicito_fecha>='".$fecha."')";
			$r= $conexion -> query($sql);
			$pases = $r -> num_rows;
			}
			
			if ($nivel==2) {
			$sql = "SELECT * FROM empleados_salidas_temporal WHERE (autorizo_nitavu='' AND dpto='".$dpto."' AND solicito_fecha>='".$fecha."')";
			$r= $conexion -> query($sql);
			$pases = $r -> num_rows;
			}
			
			return $pases;
			}

function archivo_pases($nitavu, $fecha_, $hr_salida){
	$nombrearchivo = "salidas/".$nitavu."_".str_replace("-", "", $fecha_)."_".str_replace(":", "", $hr_salida)."";
	return $nombrearchivo;
}


			function tiempo_restar_fecha($fecha_i, $fecha_f){
			$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
			$dias 	= abs($dias); $dias = floor($dias);
			return $dias;
			}
			function tiempo_sumar_hr($horaini,$horafin)
			{
			$horai=substr($horaini,0,2);
			$mini=substr($horaini,3,2);
			$segi=substr($horaini,6,2);
			
			$horaf=substr($horafin,0,2);
			$minf=substr($horafin,3,2);
			$segf=substr($horafin,6,2);
			
			$ini=((($horai*60)*60)+($mini*60)+$segi);
			$fin=((($horaf*60)*60)+($minf*60)+$segf);
			
			$dif=$fin+$ini;
			
			$difh=floor($dif/3600);
			$difm=floor(($dif-($difh*3600))/60);
			$difs=$dif-($difm*60)-($difh*3600);
			return date("H:i:s",mktime($difh,$difm,$difs));
			}


function tiempo_restar_hr($horaini,$horafin)
{
	$horai=substr($horaini,0,2);
	$mini=substr($horaini,3,2);
	$segi=substr($horaini,6,2);

	$horaf=substr($horafin,0,2);
	$minf=substr($horafin,3,2);
	$segf=substr($horafin,6,2);

$ini=((($horai*60)*60)+($mini*60)+$segi);
$fin=((($horaf*60)*60)+($minf*60)+$segf);

$dif=$fin-$ini;

	$difh=floor($dif/3600);
	$difm=floor(($dif-($difh*3600))/60);
	$difs=$dif-($difm*60)-($difh*3600);
	return date("H:i:s",mktime($difh,$difm,$difs));


}
			function geo_guarda($nitavu_, $lat, $lon, $descripcion){
			require("config.php");
			$sql = "INSERT INTO empleados_geo
			(nitavu, lat, lon, fecha, hora, descripcion)
			VALUES
			('$nitavu_', '$lat', '$lon', '$fecha', '$hora','$descripcion')";
			if ($conexion->query($sql) == TRUE)
			{
			return TRUE;
			//header('location:../index.php');a
			}
			else
			{
			return FALSE;
			//echo $sql;
			}
			}
			function chat_guardamsj($nitavu_, $mensaje){
			require("config.php");
			$sql = "INSERT INTO chat
			(nitavu, mensaje, fecha, hora)
			VALUES
			('$nitavu_', '$mensaje', '$fecha', '$hora')";
			if ($conexion->query($sql) == TRUE)
			{
			return TRUE;
			//header('location:../index.php');
			}
			else
			{
			return FALSE;
			//echo $sql;
			}
			}


function historia($nitavu_, $descripcion){
require("config.php");
//funcion que otorga acceso a las aplicaciones
$sql = "INSERT INTO historia
(nitavu, fecha, hora, descripcion)
VALUES
('$nitavu_', '$fecha', '$hora','$descripcion')";
if ($conexion->query($sql) == TRUE)
{	//echo "ok";
	return 'TRUE';
}
	else
{	//echo $sql;
	return 'FALSE';
}
}
			

			
			function aplicacion_historia($nitavu_, $descripcion, $version){
			require("config.php");
			//funcion que otorga acceso a las aplicaciones
			$sql = "INSERT INTO aplicacion_historia
			(nitavu, fecha, descripcion, version)
			VALUES
			('$nitavu_', '$fecha', '$descripcion', '$version')";
			if ($conexion->query($sql) == TRUE)
			{
			return TRUE;
			}
			else
			{
			return FALSE;
			}
			}
			function valida_fecha($fecha_){
			if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $fecha_)){
			//it's ok
			return TRUE;
			}else{
			//it's bad
			return FALSE;
			}
			}
			function sugerencia($msg){
			$msg = '
			<div id="sugerencias">
				<table border="0"><tr>
					<td><img src="./icon/sugerencia.png" class="icono"></td>
					<td class="normal">
						'.$msg.'
					</td></tr></table>
				</div>
				';
				return $msg;
				}
				function ponerpdf($archivo,$clase)
				{
				if (file_exists($archivo)){
				return '<iframe src="'.$archivo.'" class="'.$clase.'"></iframe><a href="'.$archivo.'" target class="btn"><br>Ver completo</a>';
				}
				else
				{
				return 'Aun no hay archivo';
				}
				}
				
				// function ponerfoto($archivo,$clase)
				// {
					
				// if (file_exists($archivo)){
				
				// //return '<a href="'.$archivo.'" target="_blank"><img src="'.$archivo.'" class="'.$clase.'"></a>';
				// return '<img src="'.$archivo.'" class="'.$clase.'">';

				// }
				// else
				// {
				// return '<img src="img/sinfoto.png" class="'.$clase.'">';
				// }
				// }
				function ponerfoto_org($archivo,$n)
				{
				if (file_exists($archivo)){
				return '<a href="empleados_edit.php?pes=gral&n='.$n.'"><img src="'.$archivo.'" width=100px; height=130px;></a>';
				}
				else
				{
				return '';
				}
				
			}
				function ponerfoto_app($archivo,$clase)
				{
				if (file_exists($archivo)){
				return '<a href="'.$archivo.'"><img src="'.$archivo.'" class="'.$clase.'"></a>';
				}
				else
				{
				//return '<img src="img/sinfoto.png" class="'.$clase.'">';
				return "";
				}
				}
				function ponericono($archivo,$clase)
				{
				if (file_exists($archivo)){
				return '<img src="'.$archivo.'" class="'.$clase.'">';
				}
				else
				{
				return '<img src="icon/sinfoto.png" class="'.$clase.'">';
				}
				}

				function subir2($nombredelcontrol, $archivo, $ext)
				//set_time_limit(5000); // aumenta el tiempo ejecucion del script 5min
				{
				$msgE='';
				if (substr($_FILES[$nombredelcontrol]['type'], 0, 11)=="application"){
					//$msgE ="(".$_FILES[$nombredelcontrol]."no es una foto)";
				}
				else
					{
						if ($_FILES[$nombredelcontrol]['size']<20000000) {
						//$target_path = "".$donde."/";
						$target_path = $archivo.'.'.$ext;
							if(move_uploaded_file($_FILES[$nombredelcontrol]['tmp_name'], $target_path))
							{ $msgE= "";} else{
							//$msgE= "No se actualizo ".$nombredelcontrol.", ";
							$msgE ="no subio";
							}
						} else {//$msgE ="(tamaño superior 10mb)";
						}
				
				}
				
				return $msgE;
				}

//  function renombrarimagenes($carpeta)
//  {
	
// 	if(is_dir($carpeta)){//comprueba que $carpeta sea un directorio					
// 		if($dir = opendir($carpeta)){//abre el directrio					
// 			//recorre el directorio mientras haya archivos
			
// 			while(($archivo = readdir($dir)) !== false){
				
// 				//el if compara que no sea elementos . .. o htaccess
// 				if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess')
// 				{								
// 					$pos = strpos('-', $archivo);

// 					if ($pos === false) {
						
// 						$nombre= explode('.',substr($archivo, 0, strlen($archivo)));
// //echo "<br>".$nombre[0]."==>".$nombre[1];
// 						rename($carpeta."/".$archivo,$carpeta."/".$nombre[0].'-'.ndocumento(false).".".$nombre[1]);
// 					}
// 					else
// 					{
						
// 					}
// 				}
// 			}
			
// 			closedir($dir);
// 		}
// 	}

//  }



function ponerfoto($archivo,$clase)
				{
				//obtengo el directorio
				$carpeta= substr($archivo, 0,  stripos($archivo, '/'));
				//obtengo el Nombre del archivo sin el directorio

				//Excelente Yesi! :D, 				
				//Marca un error, si recibe en $archivo  una extension, Ajustarla para (quitar la extension si la detecta)
				//Esto es para que acepte donde se aplico anteriormente esta funcion
				
				//$nombre= explode('-',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
						
				
				$pos = strpos('-', $archivo);
				if ($pos === false) {
					$nombre= explode('.',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
					//$nombre= explode('.',substr($nombre[0], stripos($nombre[0], '/')+1, strlen($nombre[0])));
					//echo "<br>".$archivo;
				//	$nombre[0]=$archivo;
				
				}
				else
				{
					$nombre= explode('-',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
					$nombre= explode('.',substr($nombre[0], stripos($nombre[0], '/')+1, strlen($nombre[0])));	
				}
				
			
				//echo $nombre[0];
				$valores=null;
				$i=0;
				$ext=null;;


								if(is_dir($carpeta)){//comprueba que $carpeta sea un directorio					
									if($dir = opendir($carpeta)){//abre el directrio					
										//recorre el directorio mientras haya archivos
										while(($archivo = readdir($dir)) !== false){
											
											//el if compara que no sea elementos . .. o htaccess
											if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess'){								
												//creamos nuestro elemento comparativo
												//por medio de una funcion de cadena
												
												//obtenemos el nombre de cada archivo en nuestro directorio
												//echo "<br>".$archivo;
																						 
												$comparacion = substr($archivo, 0,  stripos($archivo, '-'));
												
												//comparamos el elemento con nuestro patron
												//y si se cumple lo guardamos en un vector para posteriormente obtener el mayor

											   
												if ($nombre[0] == $comparacion){

													$numero= explode('.',substr($archivo, stripos($archivo, '-')+1, strlen($archivo)));											
													$valores[$i]=$numero[0];
													$ext[$i]=$numero[1];													
													$i++;
												
												}
											}
										}
										
										closedir($dir);
									}
								}

								if ($valores<>''){
								if(sizeof($valores)>1)
								{
									$clave = array_search(max($valores), $valores);
									$ext1=$ext[$clave];
									$archivo= $carpeta."/".$nombre[0]."-".max($valores).".".$ext1;
								}
								else
								{
									$ext1=$ext[0];
									$archivo= $carpeta."/".$nombre[0]."-".$valores[0].".".$ext1;
								 }
								
							    
								 if (file_exists($archivo))
								 {

									if($ext1=='pdf')
									{
										return '<iframe src="'.$archivo.'" class="'.$clase.'"></iframe><a href="'.$archivo.'" target class="btn"><br>Ver completo</a>';
									}
									else
									{
										
										return '<img id="foto" src="'.$archivo.'" class="'.$clase.'">';
									}
								 
								}
								else
								{
								 	return '<img src="icon/sinfoto.png" class="'.$clase.'">';
								}
								}
}	







function ponerfoto_src($archivo,$clase)
				{
					
				//obtengo el directorio
				$carpeta= substr($archivo, 0,  stripos($archivo, '/'));
				//obtengo el Nombre del archivo sin el directorio

				//Excelente Yesi! :D, 				
				//Marca un error, si recibe en $archivo  una extension, Ajustarla para (quitar la extension si la detecta)
				//Esto es para que acepte donde se aplico anteriormente esta funcion
				
				//$nombre= explode('-',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
						
				
				$pos = strpos('-', $archivo);
				if ($pos === false) {
					$nombre= explode('.',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
					//$nombre= explode('.',substr($nombre[0], stripos($nombre[0], '/')+1, strlen($nombre[0])));
					//echo "<br>".$archivo;
				//	$nombre[0]=$archivo;
				
				}
				else
				{
					$nombre= explode('-',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
					$nombre= explode('.',substr($nombre[0], stripos($nombre[0], '/')+1, strlen($nombre[0])));	
				}
				
			
				//echo $nombre[0];
				$valores=null;
				$i=0;
				$ext=null;;


								if(is_dir($carpeta)){//comprueba que $carpeta sea un directorio					
									if($dir = opendir($carpeta)){//abre el directrio					
										//recorre el directorio mientras haya archivos
										while(($archivo = readdir($dir)) !== false){
											
											//el if compara que no sea elementos . .. o htaccess
											if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess'){								
												//creamos nuestro elemento comparativo
												//por medio de una funcion de cadena
												
												//obtenemos el nombre de cada archivo en nuestro directorio
												//echo "<br>".$archivo;
																						 
												$comparacion = substr($archivo, 0,  stripos($archivo, '-'));
												
												//comparamos el elemento con nuestro patron
												//y si se cumple lo guardamos en un vector para posteriormente obtener el mayor

											   
												if ($nombre[0] == $comparacion){

													$numero= explode('.',substr($archivo, stripos($archivo, '-')+1, strlen($archivo)));											
													$valores[$i]=$numero[0];
													$ext[$i]=$numero[1];													
													$i++;
												
												}
											}
										}
										
										closedir($dir);
									}
								}

								if ($valores<>''){
								if(sizeof($valores)>1)
								{
									$clave = array_search(max($valores), $valores);
									$ext1=$ext[$clave];
									$archivo= $carpeta."/".$nombre[0]."-".max($valores).".".$ext1;
								}
								else
								{
									$ext1=$ext[0];
									$archivo= $carpeta."/".$nombre[0]."-".$valores[0].".".$ext1;
								 }
								
							    
								 if (file_exists($archivo))
								 {

									
										
									return ''.$archivo.'';
									
								 
								}
								else
								{
								 	return 'icon/sinfoto.png';
								}
								}
}	






function ponerfoto_correo($archivo,$clase)
{
	require('config.php');
	
	$style='border-radius:5px;width:80%;border-width:1px;border-style:solid;border-color:#C8C8C8;padding:10px;background-color:#E5E5E5;';
				//obtengo el directorio
				$carpeta= substr($archivo, 0,  stripos($archivo, '/'));
				//obtengo el Nombre del archivo sin el directorio

				//Excelente Yesi! :D, 				
				//Marca un error, si recibe en $archivo  una extension, Ajustarla para (quitar la extension si la detecta)
				//Esto es para que acepte donde se aplico anteriormente esta funcion
				
				//$nombre= explode('-',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
						
				
				$pos = strpos('-', $archivo);
				if ($pos === false) {
					$nombre= explode('.',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
					//$nombre= explode('.',substr($nombre[0], stripos($nombre[0], '/')+1, strlen($nombre[0])));
					//echo "<br>".$archivo;
				//	$nombre[0]=$archivo;
				
				}
				else
				{
					$nombre= explode('-',substr($archivo, stripos($archivo, '/')+1, strlen($archivo)));
					$nombre= explode('.',substr($nombre[0], stripos($nombre[0], '/')+1, strlen($nombre[0])));	
				}
				
			
				//echo $nombre[0];
				$valores=null;
				$i=0;
				$ext=null;;


								if(is_dir($carpeta)){//comprueba que $carpeta sea un directorio					
									if($dir = opendir($carpeta)){//abre el directrio					
										//recorre el directorio mientras haya archivos
										while(($archivo = readdir($dir)) !== false){
											
											//el if compara que no sea elementos . .. o htaccess
											if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess'){								
												//creamos nuestro elemento comparativo
												//por medio de una funcion de cadena
												
												//obtenemos el nombre de cada archivo en nuestro directorio
												//echo "<br>".$archivo;
																						 
												$comparacion = substr($archivo, 0,  stripos($archivo, '-'));
												
												//comparamos el elemento con nuestro patron
												//y si se cumple lo guardamos en un vector para posteriormente obtener el mayor

											   
												if ($nombre[0] == $comparacion){

													$numero= explode('.',substr($archivo, stripos($archivo, '-')+1, strlen($archivo)));											
													$valores[$i]=$numero[0];
													$ext[$i]=$numero[1];													
													$i++;
												
												}
											}
										}
										
										closedir($dir);
									}
								}

								if ($valores<>''){
								if(sizeof($valores)>1)
								{
									$clave = array_search(max($valores), $valores);
									$ext1=$ext[$clave];
									$archivo= $carpeta."/".$nombre[0]."-".max($valores).".".$ext1;
								}
								else
								{
									$ext1=$ext[0];
									$archivo= $carpeta."/".$nombre[0]."-".$valores[0].".".$ext1;
								 }
								
							    
								 if (file_exists($archivo))
								 {

									if($ext1=='pdf')
									{
										return '<iframe src="'.$archivo.'" class="'.$clase.'"></iframe><a href="'.$archivo.'" target class="btn"><br>Ver completo</a>';
									}
									else
									{
										//se quitan las ' y agrega style sin clase, y se agrega la url
										
										return '<img src='.$urlsite."/".$archivo.' style='.$style.'>';
									}
								 
								}
								else
								{
								 	return '<img src='.$urlsite.'/icon/sinfoto.png  style='.$style.'>';
								}
								}
}	



function subir($nombredelcontrol, $archivo,$ext) //--------------------------------------------------------------------------
{ $ext =''; $msgE='';
//OBTENTGO LA EXTENSIÓN 
//$ext= substr($_FILES[$nombredelcontrol]['name'],strlen($_FILES[$nombredelcontrol]['name'])-3,3);	
$ext = pathinfo( $_FILES[$nombredelcontrol]['name'], PATHINFO_EXTENSION );

if ( isset( $_FILES ) && isset( $_FILES[$nombredelcontrol] ) && !empty( $_FILES[$nombredelcontrol]['name'] && !empty($_FILES[$nombredelcontrol]['tmp_name']) ) ) 
{
	//Hemos recibido el fichero
	//Comprobamos que es un fichero subido por PHP, y no hay inyección por otros medios
	if ( ! is_uploaded_file( $_FILES[$nombredelcontrol]['tmp_name'] ) ) 
	{$msgE= "ERROR: El fichero encontrado no fue procesado por la subida correctamente";} 

	// si es un formato de imagen o pdf		
	if($_FILES[$nombredelcontrol]["type"]=="image/jpg"||$_FILES[$nombredelcontrol]["type"]=="image/jpeg" || $_FILES[$nombredelcontrol]["type"]=="image/pjpeg" || $_FILES[$nombredelcontrol]["type"]=="image/gif" || $_FILES[$nombredelcontrol]["type"]=="image/png" ||mime_content_type($_FILES[$nombredelcontrol]['tmp_name']) == 'application/pdf'  )
	{
		$destino=$archivo.'-'.ndocumento(False).'.'.$ext;			
		if ( is_file($destino ) )
		{
			$msgE= "ERROR: Ya existe almacenado un fichero con ese nombre";
			@unlink(ini_get('upload_tmp_dir').$_FILES[$nombredelcontrol]['tmp_name']);			
		}
			
		if ( ! @move_uploaded_file($_FILES[$nombredelcontrol]['tmp_name'], $destino) ) 
		{
			$msgE= "ERROR: No se ha podido mover el fichero enviado a la carpeta de destino";
			@unlink(ini_get('upload_tmp_dir').$_FILES[$nombredelcontrol]['tmp_name']);
			
		}
		else
		{
			//$msgE= $destino. "";
			$msgE="Archivo subido con exito.!!";
		}
	}
	else
	{
		$msgE= "ERROR: El archivo que intenta subir no tiene un formato correcto.";
		
	}
				
	
	}
	return $msgE;

}//----------------------------------------------------------------------------------------------------------------


				// function subir($nombredelcontrol, $archivo, $ext)
				// {
				// $msgE='';
				
				// if (substr($_FILES[$nombredelcontrol]['type'], 0, 11)=="application"){
				// $msgE= "ERROR: Es una aplicacion";
				// }
				// else
				// {

				// if ($_FILES[$nombredelcontrol]['size']<2000000) 
				// {
					
				// 	if(!empty($_FILES[$nombredelcontrol]['name']))
				// 	{
				// 	//$target_path = "".$donde."/";
				// 	$target_path = $archivo.'.'.$ext;
				// 	if(move_uploaded_file($_FILES[$nombredelcontrol]['tmp_name'], $target_path))
				// 	{ $msgE= "La foto se  ". $archivo.'.'.$ext. " ha guardado exito<br>";
				// 	} 
				// 	else
				// 	{
				// 	//$msgE= "No se actualizo ".$nombredelcontrol.", ";
				
				// 	$msgE= "No se actualizo o cargo foto ";
				// 	}
				// 	}
				// }
				//  else {
				// $msgE ="ERROR: El archivo que intenta subir es mayor de 2mb";
				// }
				
				// }
				
				// return $msgE;
				// }
				
				function subirpdf($nombredelcontrol, $archivo, $ext)
				{
				$msgE='';
				
				if (substr($_FILES[$nombredelcontrol]['type'], 0, 11)=="application"){
				$msgE= "ERROR: Es una aplicacion".substr($_FILES[$nombredelcontrol]['type'], 0, 11);
				}
				else
				{
				if ($_FILES[$nombredelcontrol]['size']<20000000) {
				//$target_path = "".$donde."/";
				$target_path = $archivo.'.'.$ext;
				if(move_uploaded_file($_FILES[$nombredelcontrol]['tmp_name'], $target_path))
				{ $msgE= "El documento se  ". $archivo.'.'.$ext. " ha guardado exito<br>";
				} else{
				//$msgE= "No se actualizo ".$nombredelcontrol.", ";
				//$msgE= "No se actualizo o cargo foto ";
				}
				} else {
				$msgE ="ERROR: El archivo que intenta subir es mayor de 2mb";
				}
				
				}
				
				return $msgE;
				}

				function subirpdf2 ($nombredelcontrol, $archivo)
				{
				$ebook = $_FILES[$nombredelcontrol]['tmp_name'];
				if ($_FILES[$nombredelcontrol]['error'] !== 0) {
				//return 'Error al subir el archivo (¿demasiado grande?)';
				} else {
					if ( mime_content_type($_FILES[$nombredelcontrol]['tmp_name']) == 'application/pdf')
					{
						$ruta_ebook = 'docs/' . $archivo . '.pdf';
						if (move_uploaded_file($ebook, $ruta_ebook)) {
						return  "Doc ".$archivo." guardado.";
						} else {
							return  "No se guardo ".$archivo;
						}
					}
				}
				}


				function subirpdf3 ($nombredelcontrol, $archivo)
				{
				$ebook = $_FILES[$nombredelcontrol]['tmp_name'];
				if ($_FILES[$nombredelcontrol]['error'] !== 0) {
				//return 'Error al subir el archivo (¿demasiado grande?)';
				} else {
					if ( mime_content_type($_FILES[$nombredelcontrol]['tmp_name']) == 'application/pdf')
					{
						//$ruta_ebook = 'docs/' . $archivo . '.pdf';
						if (move_uploaded_file($ebook, $archivo)) {
						return  "TRUE";
						} else {
							return  "FALSE ";
						}
					}
				}
				}


				function notificaciones_ver($no_oficio,$nitavu_){
				require("config.php");
				$sql = "SELECT * FROM notificaciones WHERE (nitavu='".$nitavu_."' AND id='".$no_oficio."')";
				$rc= $conexion -> query($sql);
				if($f = $rc -> fetch_array())
				{
				$sql="UPDATE notificaciones SET lectura_fecha='".$fecha."', lectura_hora='".$hora."' WHERE (nitavu='".$nitavu_."' AND id='".$no_oficio."')";
				//echo $sql;
				$resultado = $conexion -> query($sql);
				if ($conexion->query($sql) == TRUE)
				{
				return TRUE;
				}
				else
				{
				return FALSE;
				}
				
				}
				else
				{
				return FALSE;
				}
				
				}
				function ceropapel(){
				require("config.php");
				$sql = "SELECT * FROM contadores WHERE id='0'";
				$rc= $conexion -> query($sql);
				if($f = $rc -> fetch_array())
				{
				return $f['ceropapel'];
				}
				
				}
				function docdigital_no($consulta, $cuantas){
				require("config.php");
				$sql = "SELECT * FROM contadores WHERE id='0'";
				$rc= $conexion -> query($sql);
				if($f = $rc -> fetch_array())
				{
				if ($consulta==TRUE) {
				return $f['docdigital'];
				}
				else
				{ // sino es consulta entonces aumentarle y aumentar el contador de ceropapel
				// la diferencia entre ceropapel y este, es que cero papel se multiplica
				// por las copias que se entregan o con copia, para estadistica de cuanto se ha ahorrado
				$docdigital = $f['docdigital'];
				$docdigitalnew = $docdigital + 1;
				$ceropapel = $f['ceropapel'] + $cuantas;
				$sql="UPDATE contadores SET docdigital='".$docdigitalnew."', ceropapel='".$ceropapel."' WHERE id='0'";
				$resultado = $conexion -> query($sql);
				if ($conexion->query($sql) == TRUE) {
				return $f['docdigital'];
				}
				else {return  FALSE;}
				}
				}
				else
				{ return FALSE;}
				}
				
					function ver($no_oficio){
					require("config.php");
					//funcion que otorga acceso a las aplicaciones
					$sql = "INSERT INTO notificaciones
					(nitavu, asunto, entregar_fecha, nitavu_manda, contenido)
					VALUES
					('$usuario', '$asunto', '$entregar_fecha','$itavu_manda', '$contenido')";
					if ($conexion->query($sql) == TRUE)
					{
					return TRUE;
					}
					else
					{
					return FALSE;
					}
					}

function notificacion_add ($usuario, $asunto, $entregar_fecha, $itavu_manda, $contenido){
//echo $usuario;
//echo $asunto;
//echo $entregar_fecha;
//echo $itavu_manda;
//echo $contenido;

sleep(1);//retraso programado	
require("config.php");
if ($usuario <> ''){
$npase = npase(FALSE);
$sql = "INSERT INTO notificaciones
	(nitavu, asunto, entregar_fecha, nitavu_manda, contenido, id)
VALUES
	('$usuario', '$asunto', '$entregar_fecha','$itavu_manda', '$contenido', '$npase')";
//echo $sql;
if ($conexion->query($sql) == TRUE)
{
	if (nitavu_correo_valido($usuario)==TRUE){//si tiene correo valido
		if ($asunto <>'chat'){//que no sea chat
			$quien = nitavu_correo($itavu_manda);
			$quien_nombre = nitavu_nombre($itavu_manda);

			//echo "correo: ".$quien;

			correo(nitavu_correo($usuario), nitavu_nombre($usuario), $quien, $quien_nombre, $asunto, $contenido, $usuario);
		}
	}
	return TRUE;
}
else
	{return FALSE;}
}
}

					function notificaciones_detalle($oficio){
					require("config.php");
					$sql = "SELECT * FROM notificaciones WHERE no_oficio='".$oficio."'";
					$rc= $conexion -> query($sql);
					$hay = 0;
					$msg="";
					while($m = $rc -> fetch_array()) {
					$msg= $msg."<li>".$m['no_oficio']." entregada ".$m['entregar_fecha']." a ".nitavu_nombre($m['nitavu']).". Asunto: ".$m['asunto']."";
						if ($m['lectura_hora']=="") {
						$msg = $msg.". Aun sin leer"	;
						}
						else {
						$msg = $msg. ", leida el ".$m['lectura_fecha']." a las ".$m['lectura_hora']."hrs.";
						}
					echo "</li>";
					$hay = $hay +1;
					}
					//$msg = $msg."</lu>";
					$msg=$msg."";
					if ($hay>0) {
					return $msg."";
					}
					else{
					return "";
					}
					
					}
					function aplicaciones_nivel($n){
					require("config.php");
					$sql = "SELECT * FROM aplicaciones_permisos	 WHERE nitavu='".$n."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					
					return $f['nivel'];
					}
					else
					{ return FALSE;}
					}
					function aplicacion_categoria($idapp){
					require("config.php");
					$sql = "SELECT * FROM aplicaciones	WHERE idapp='".$idapp."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					return $f['idapcat'];
					}
					else
					{ return FALSE;}
					}
					function nivel_que($n){
					require("config.php");
					$sql = "SELECT * FROM aplicaciones_nivelusuario	 WHERE id='".$n."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					
					return $f['modo'];
					}
					else
					{ return FALSE;}
					}
					function idapp_categoria($idapp){
					require("config.php");
					$sql = "SELECT * FROM aplicaciones WHERE idapp='".$idapp."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					
					
					return $msg['idapcat'];
					}
					else
					{ return FALSE;}
					}
					function app_detalle($idapp){
					require("config.php");
					$sql = "SELECT * FROM aplicaciones WHERE idapp='".$idapp."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					$msg="<a href='".app_vinculo($idapp)."' style='cursor:pointer; margin-top:-100px;  z-index:5000;' title='Haga clic para regresar a la principal de esta aplicacion'><table><tr>";
						$msg= $msg."<td>";
							$archivo = "icon/".$f['icono'];
							
							$foto = "<img src='icon/".$f['icono']."' class='mini_icono2'>";
							$msg = $msg.$foto;
							
						$msg=  $msg. "</td>";
						$msg = $msg."<td><span class='app_titulo'>".$f['nombre']."</span><span class='app_version'></span></td>";
						$msg = $msg."<td class='pc'><span class='app_des'>".$f['descripcion']."</span></td>";
						$msg = $msg."<td class='pc'><a title='Ir a la ayuda de esta aplicacion ' href='ayuda.php?idapp=".$f['idapp']."'><img src='icon/ayuda2.png' 
						style=' width:24px; height:24px; margin-left:20px;
						';
						></a></span></td>";
						
					$msg= $msg."</tr></table></a>";
					return $msg;
					}
					else
					{ return FALSE;}
					}


					function app_nombre($idapp){
					require("config.php");
					$sql = "SELECT * FROM aplicaciones WHERE idapp='".$idapp."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					return $f['nombre'];
					}
					else
					{ return FALSE;}
					}

					function app_vinculo($idapp){
					require("config.php");
					$sql = "SELECT * FROM aplicaciones WHERE idapp='".$idapp."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					return $f['vinculo'];
					}
					else
					{ return FALSE;}
					}

					function app_version($idapp){
					require("config.php");
					$sql = "SELECT * FROM aplicaciones WHERE idapp='".$idapp."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					return $f['version'];
					}
					else
					{ return FALSE;}
					}
					function notificaciones_count($nitavu){
					require("config.php");
					$sql = "SELECT * FROM notificaciones WHERE (nitavu='".$nitavu."' AND lectura_hora='')";
					$r = $conexion -> query($sql);
					$r_count = $r -> num_rows;
					return $r_count;
					
					}
					function aplicacion_nivel($idapp,$usuario){
					require("config.php");
					//funcion que otorga acceso a las aplicaciones
					$sql = "SELECT * FROM aplicaciones_permisos WHERE (nitavu='".$usuario."' AND idapp='".$idapp."')";
					$rc= $conexion -> query($sql);
					if($f = $rc -> fetch_array())
					{

						//historia($usuario,"Usando la aplicacion [".$idapp."] ".app_nombre($idapp)." (".$f['nivel']).")";

						return $f['nivel'];

					}
					else
					{ return 0;}
					}

					function aplicacion_nivel_quien($idapp,$nivel){
					require("config.php");
					//funcion que otorga acceso a las aplicaciones
					$sql = "SELECT * FROM aplicaciones_permisos WHERE (nivel='".$nivel."' AND idapp='".$idapp."')";
					//echo $sql;
					$rc= $conexion -> query($sql);
					if($f = $rc -> fetch_array())
					{

						//historia($usuario,"Usando la aplicacion [".$idapp."] ".app_nombre($idapp)." (".$f['nivel']).")";
						return $f['nitavu'];

					}
					else
					{ return '';}
					}

function sanpedro ($idapp,$usuario){
require("config.php");
//funcion que otorga acceso a las aplicaciones
//pero a san pedro no le importa tu nivel, si estas en la lista te deja pasar
$sql = "SELECT * FROM aplicaciones_permisos WHERE (nitavu='".$usuario."' AND idapp='".$idapp."' )";
$rc= $conexion -> query($sql);
if($f = $rc -> fetch_array())
	{
	xd_update($idapp,$usuario);//guarda la experiencia del usuario
	return TRUE;

	}
else
	{ //historia($usuario, "Se le nego el acceso a la aplicacion con ID ".$idapp); 
		return FALSE;}
}
					function dedondeeres($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					return $f['ciudad'].", Tamaulipas.";
					}
					else
					{ return FALSE;}
					}
					function pase_quien_autoriza_dpto($dpto){
					//para saber los autorizados en un departamento para aprobar el pase
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE departamento='".$dpto."'";
					$rc= $conexion -> query($sql);
					$msg="";
					while($f = $rc -> fetch_array())
					{
					$nivel = aplicacion_nivel('ap12', $f['nitavu']);
					if (($nivel == '3') OR ($nivel == '2') OR ($nivel == '1'))   {
					$msg = $msg.nitavu_nombre($f['nitavu']).", ";
					
					}
					//$msg = $msg.$f['nitavu']." nivel: ".$nivel."<br>";
					}
					return $msg;
					}
					
					function nitavu_tel_ext($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					return $f['telefono_extension'];
					}
					else
					{ return "";}
					}

					function nitavu_celular($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					return $f['telefono_movil'];
					}
					else
					{ return "";}
					}


					function nitavu_correo_valido($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."' and correo_vobo=1";
					$rc= $conexion -> query($sql);					
					if($f = $rc -> fetch_array())
						{return TRUE;}
					else
						{return FALSE;}
					}

					function nitavu_tel($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					return $f['telefono'];
					}
					else
					{ return FALSE;}
					}


function nitavu_profesion($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
						return $f['profesion_abr'];
					}


}












					function nitavu_nombre($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					if ($f['profesion_abr']==""){
					return $f['nombre'];}
					else
					{return $f['profesion_abr'].". ".$f['nombre'];}
					}
					else
					{ return FALSE;}
					}
					function nitavu_nombre2($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					if ($f['profesion_abr']==""){
					return $f['nombre'];}
					else
					{return $f['profesion_abr'].". ".$f['nombre'];}
					}
					else
					{ return '<span style=color:red>sin asignacion</span>';}
					}
					function nitavu_dir($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{return $f['direccion'];}
					else
					{ return FALSE;}
					}
					function dpto_au($id){
					require("config.php");
					$sql = "SELECT * FROM empleados_salidas_temporal WHERE id='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{return nitavu_dpto($f['nitavu']);}
					else
					{ return FALSE;}
					}

					function nitavu_dpto($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
							{return $f['dpto'];}
					else
							{ return FALSE;}
					}

					function nitavu_dpto_nombre($id){
					require("config.php");
					$sql = "SELECT
							dpto as depa,
							(select nombre from cat_gerarquia where id=depa) as departamento
							FROM
							empleados
							WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
							{return $f['departamento'];}
					else
							{ return FALSE;}
					}


					function nitavu_puesto($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{return $f['puesto'];}
					else
					{ return FALSE;}
					}

					function nitavu_correo($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{return $f['correoelectronico'];}
					else
					{ return "";}
					}


					function user_quien($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					
					$msg ="<b>".$f['nombre']."</b>, ".$f['puesto']." de ".$f['departamento'];
					
					return $msg;
					}
					else
					{ return FALSE;}
					}
					function user_historia($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					
					$msg ="".$f['historia']."<br>";
					
					return $msg;
					}
					else
					{ return FALSE;}
					}
					function user_legend($id){
					require("config.php");
					$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
					$rc= $conexion -> query($sql);
					$msg="";
					if($f = $rc -> fetch_array())
					{
					
					$msg = $msg."<b>".$f['nombre']."</b><br>";
					$msg = $msg.$f['puesto']." de ".$f['departamento'];
					
					return $msg;
					}
					else
					{ return FALSE;}
					}





function user_alertas($id){
require("config.php");
$sql = "SELECT * FROM empleados WHERE nitavu='".$id."'";
$rc= $conexion -> query($sql);$msg="";
if($f = $rc -> fetch_array())
{
	$msg="";
		if ($f['nitavu']==$f['nip']) // una alerta; PONERLAS EN ARTICLEca
					{
					$msg = $msg."<article><a href='nip_update.php'>".
						"<b>Debe cambiar su NIP por seguridad.</b> <cite> Debido a que de manera predeterminada es el mismo que su Numero de ITAVU</cite>"
					."</a></article>";
					}
					
					$pases = pases($f['nitavu']);
	if ($pases>0) // una alerta; PONERLAS EN ARTICLE
					{
					$msg = $msg."<article><a href='auscencia_temporal_autoriza.php'>".
						"<b>Hay ".$pases." pases por aprobar</b> </cite>"
					."</a></article>";
					}
					
					$visitas = visitas($f['nitavu']);
					if ($visitas>0) // una alerta; PONERLAS EN ARTICLE
					{
					//$msg = $msg."<article><a href='visitas.php'>".
					//	"<b>Tienes ".$visitas." Visitas, Verifica las aprobaciones</b> </cite>"
					//."</a></article>";
					}
					$desface = pases_desfase($f['nitavu'], $fecha, $fecha, 'FALSE')	;
					if ($desface>0) // una alerta; PONERLAS EN ARTICLE
					{
					$msg = $msg."<article>".
						"<b>Tienes ".$desface."min. de retraso en tu pase de salida</b> "
					."</article>";
					}
					
					$naci = $f['fecha_nacimiento'];
					if ($naci=='0000-00-00') // una alerta; PONERLAS EN ARTICLE
					{
					$msg = $msg."<article><form action='' method='GET'>
						<b>Apoyanos para completar tus datos: </b><label>¿Cual es tu fecha de nacimiento? </label>
						<input type='date' name='nac' value='".$naci."'> <input type='submit' value='Guardar' class='btn btn-secundario'>
					</form>";
					$msg = $msg."
					";
					$msg = $msg."</article>";
					}
					
					if ($f['correoelectronico']==''){//si no tiene
						$msg = $msg."<article>Si deseas recibir notificaciones en tu correo, <a href='perfil.php?pes=personales'>Registralo</a>.<label>Mediante este, se hara saber de tus actividades y pendientes en la plataforma</label>";
						if ($f['correo_vobo']<=0){
					
						}
						$msg = $msg."</article>";
					}else{//si ya tiene correo
						if ($f['correo_vobo']<=0){
						$msg = $msg."<article style='background-color:red; color:white'><a href='perfil.php?pes=personales'>Tu correo aun no ha sido activado, si no ha recibido el correo de activacion vaya a sus preferencias y de clic en activar. </a>";
						$msg = $msg."</article>";					
						}
	
					}
					
						if (sanpedro('ap54', $id)==TRUE){//solo los que tengan permiso
						if (pendientes_($id)<>''){
							$msg = $msg."<article><a href='pendientes_direccion.php'>".pendientes_($id)."</a></article>";}
						}

				// $aviso="
				// <b class='tgrande normal'>ATENTO AVISO<br> </b><b class='normal'> Apreciados compañeros:</b> 

				// <p>Con la intentencion de apoyar a nuestro hermanos que están viviendo una situación dificil, a causa del sismo que se registro el día de ayer en diversos estados de la República, se les hace una atenta invitación para que los puedan ayudar donando agua embotellada, alimentos no perecederos, material de limpieza o de curación, en el área de recursos humanos se estarán recibiendo los apoyos. </p>

				// <p>Posteriormente, los viveres serán llevados a los lugares de acopio autorizados para su traslado a los lugares afectados. </p>
				// <b>AGRADECEMOS TU VALIOSA COOPERACIÓN.</b> 

				// ";
				// $msg = $msg."<article>".
				// 		" ".$aviso.""
				// 	."</article>";
				
				// $cumples = cumples_estemes();
				// //habla(cumples_estemes_quienes());
				// if ($cumples<>''){
				// $msg = $msg."<article>".$cumples."</article>";
				

				// }
			
			return $msg;
			
			}
			else
			{ return FALSE;}
			}


















			function detectar()
			{
			$browser=array("IE","OPERA","MOZILLA","NETSCAPE","FIREFOX","SAFARI","CHROME");
			$os=array("WIN","MAC","LINUX");
			# definimos unos valores por defecto para el navegador y el sistema operativo
			$info['browser'] = "OTHER";
			$info['os'] = "OTHER";
			# buscamos el navegador con su sistema operativo
			foreach($browser as $parent)
			{
			$s = strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $parent);
			$f = $s + strlen($parent);
			$version = substr($_SERVER['HTTP_USER_AGENT'], $f, 15);
			$version = preg_replace('/[^0-9,.]/','',$version);
			if ($s)
			{
			$info['browser'] = $parent;
			$info['version'] = $version;
			
			}
			}
			# obtenemos el sistema operativo
			foreach($os as $val)
			{
			if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']),$val)!==false)
			$info['os'] = $val;
			}
			# devolvemos el array de valores
			
			//echo getenv('HTTP_CLIENT_IP');
			//echo getenv('HTTP_X_FORWADED_FOR');
			//echo getenv('REMOTE_ADDR');
			$infofull="<br>";
			//$infofull = $infofull. "Usuario: ".gethostname()."<br>";
			$infofull = $infofull. "SO: ".$info['os']."<br>";
			$infofull = $infofull. "Nav: ".$info['browser']."<br>";
			$infofull = $infofull. "Ver: ".$info['version']."<br>";
			$infofull = $infofull. "Agente ".$_SERVER['HTTP_USER_AGENT']."<br>";
			
			$infofull = $infofull. "ip: ".getenv('HTTP_CLIENT_IP')."<br>";
			$infofull = $infofull. "ip: ".getenv('HTTP_X_FORWADED_FOR')."<br>";
			$infofull = $infofull. "ip: ".getenv('REMOTE_ADDR')."<br>";
			
			
			return $infofull;
			}


function insertar_mapa(){//inserta el mapa interactivo, y entraga variable $_GET['m'] del municipio que seleccino
//para usar esta funcion se espera en la pagina presente la var ?m=	* CONSIDERARLO
require("config.php");
echo '<section id="municipios_seleccion"><div id=municipios> <h1>Municipios: </h1>';
$sql2="SELECT * FROM cat_municipios order by Municipio ASC";
$r2 = $conexion -> query($sql2);
$seleccionados="";
   if (isset($_GET['mm'])){ // si hay seleccionado un MULTIPLE municipio
         $municipios_select = explode(",", $_GET['mm']);
         $municipios_n = count($municipios_select);         
         $municipios_n2 = $municipios_n -1;
   }
   while($df = $r2 -> fetch_array())
   {//$df recorre la lista de las delegaciones
      echo "<div>";      
      if (isset($_GET['mm'])){ // si hay seleccionado un MULTIPLE municipio
      for ($i = 0; $i <= $municipios_n2; $i++) {         
         if ($municipios_select[$i]==$df['IdMunicipio']){   
               echo "<a href='?m=".$df['IdMunicipio']."' id='m".$df['IdMunicipio']."' class='municipio_resaltado'>".$df['nombre']."</a>"; 
               $seleccionados = $df['IdMunicipio'].",";
               //break;
         }
      }//for

      $seleccionados_ = explode(",", $seleccionados);$seleccionados_n = count($seleccionados_);       
      $seleccionados_n2 = $seleccionados_n -1;     
      for ($i = 0; $i <= $seleccionados_n2; $i++) {         
         {
            if ($seleccionados_[$i]==$df['IdMunicipio']){
               //echo "=";
               break;
            }
            else {
               echo "<a href='?m=".$df['IdMunicipio']."' id='m".$df['IdMunicipio']."' class='municipios'>".$df['nombre']."</a>"; 
               break;

            }
         }

         //echo $i;
         //echo $municipios_select[$i]."-".$df['IdMunicipio']."|";
         // $i = $i +1;             
         
      }//for
          

         


      //}
      echo "</div>";

   }



      if (isset($_GET['m'])){ // si hay seleccionado un municipio
         if ($_GET['m']==$df['IdMunicipio']){   
            echo "<a href='?m=".$df['IdMunicipio']."' id='m".$df['IdMunicipio']."' class='municipio_resaltado'>".$df['nombre']."</a></div>"; 
         }
         else {
            echo "<a href='?m=".$df['IdMunicipio']."' id='m".$df['IdMunicipio']."' class='municipios'>".$df['nombre']."</a></div>"; 
         }

      }


   }
echo '</div>';


echo "<div id='mapa_tamaulipas'>";

echo '
<svg version="1.1" id="Layer_1" data-municipio="Layer_1"  x="0px" y="0px" viewBox="0 0 325.656 665.291" enable-background="new 0 0 325.656 665.291" xml:space="preserve">';


$sql2="SELECT * FROM cat_municipios order by Municipio ASC";
$r2 = $conexion -> query($sql2);
   while($df = $r2 -> fetch_array())
   {//$df recorre la lista de las delegaciones
      echo "<a href='?m=".$df['IdMunicipio']."'>";
      echo "<path ";
      $id= "m".$df['IdMunicipio']."";

      echo  "onmouseover=".chr(34)."javascript:document.getElementById('$id').className='municipio_resaltado'".chr(34)."; "; 
      echo  "onmouseout=".chr(34)."javascript:document.getElementById('$id').className='municipios'".chr(34).";";    

      echo "id='map".$df['IdMunicipio']."' ";


   
      if (isset($_GET['mm'])){ // si hay seleccionado un MULTIPLE municipio
      for ($i = 0; $i <= $municipios_n2; $i++) {         
         if ($municipios_select[$i]==$df['IdMunicipio']){   
            echo 'class="municipios_resalta"';

            // echo "<a href='?m=".$df['IdMunicipio']."' id='m".$df['IdMunicipio']."' class='municipio_resaltado'>".$df['nombre']."</a>"; 
               $seleccionados = $df['IdMunicipio'].",";
               //break;
         }
      }//for

      $seleccionados_ = explode(",", $seleccionados);$seleccionados_n = count($seleccionados_);       
      $seleccionados_n2 = $seleccionados_n -1;     
      for ($i = 0; $i <= $seleccionados_n2; $i++) {// si ya esta seleccionado poner sin seleccion     
         
            if ($seleccionados_[$i]==$df['IdMunicipio']){
               //echo "=";
               break;
            }
            else {
               echo 'class="municipios_mapa"';
               //echo "<a href='?m=".$df['IdMunicipio']."' id='m".$df['IdMunicipio']."' class='municipios'>".$df['nombre']."</a>";  
               break;

            }
         }//for
      }//getmm





      if (isset($_GET['m'])){ // si hay un municipio seleccionado

      if ("m".$_GET['m']=="m".$df['IdMunicipio']) {echo 'class="municipios_resalta"';} else {echo 'class="municipios_mapa"';}
      } else {echo 'class="municipios_mapa"';}{echo 'class="municipios_mapa"';}

      echo " d='".$df['data']."'>";
      echo $df['nombre'];
      echo "</path>";
      echo "</a>";
      

   }
   echo "</div>";
}




function CrearPase($npase, $empleado, $hr_salida, $justificacion, $asunto, $gen){	
require("config.php");
if ($npase == FALSE){
	$npase = npase(FALSE); //Solicitamos el Numero de Pase
}
$midpto = nitavu_dpto($empleado);		
$sql = "INSERT INTO empleados_salidas_temporal
		(id, nitavu, hora_desde, justificacion,  asunto, fecha, dpto)
		VALUES
		('$npase','$empleado', '$hr_salida',  '$justificacion', '$asunto', '$fecha','$midpto');";
		$h="";
echo $sql;		
if ($conexion->query($sql) == TRUE)
	{		$m="<p>Solicito pase de Salida para <b>".$asunto."</b></p><p>".$justificacion.", para el dia ".fecha_larga($fecha)." a las ".$hr_salida."</p><p>*Solicitado por ".nitavu_nombre($gen)."</p>";
			notificacion_add (titular(nitavu_dpto($empleado)), 'Solicito Salida para el '.fecha_larga($fecha), $fecha, $empleado, $m);
			//notificacion_add ($empleado, 'chat', $fecha, $gen, 'Te he activado una solicitud de pase'.$m);
			$h="<p>".nitavu_nombre($empleado)." (".$empleado.") ha solicitado un pase de salida para <span class='tenue'><b>".$asunto."</b>".$justificacion.". ";
			$h = $h."para el dia ".$fecha."</p>.";
			historia($empleado, $h);
			return TRUE;
	}
else
	{
			historia($empleado, "ERROR | (".$sql.") al intentar guardar pase de salida");
			return FALSE;
			mensaje ("Error :".$sql,'');
			
	}



}




//------    CONVERTIR NUMEROS A LETRAS         ---------------
//------    Máxima cifra soportada: 18 dígitos con 2 decimales
//------    999,999,999,999,999,999.99
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE BILLONES
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE MILLONES
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE PESOS 99/100 M.N.

function numtoletras($xcifra)
{
    $xarray = array(0 => "Cero",
        1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
        "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
        "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
        100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
    );
//
    $xcifra = trim($xcifra);
    $xlength = strlen($xcifra);
    $xpos_punto = strpos($xcifra, ".");
    $xaux_int = $xcifra;
    $xdecimales = "00";
    if (!($xpos_punto === false)) {
        if ($xpos_punto == 0) {
            $xcifra = "0" . $xcifra;
            $xpos_punto = strpos($xcifra, ".");
        }
        $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
        $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
    }

    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
    $xcadena = "";
    for ($xz = 0; $xz < 3; $xz++) {
        $xaux = substr($XAUX, $xz * 6, 6);
        $xi = 0;
        $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
        $xexit = true; // bandera para controlar el ciclo del While
        while ($xexit) {
            if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                break; // termina el ciclo
            }

            $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
            $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
            for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                switch ($xy) {
                    case 1: // checa las centenas
                        if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                            
                        } else {
                            $key = (int) substr($xaux, 0, 3);
                            if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                if (substr($xaux, 0, 3) == 100)
                                    $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                            }
                            else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                $key = (int) substr($xaux, 0, 1) * 100;
                                $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                $xcadena = " " . $xcadena . " " . $xseek;
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 0, 3) < 100)
                        break;
                    case 2: // checa las decenas (con la misma lógica que las centenas)
                        if (substr($xaux, 1, 2) < 10) {
                            
                        } else {
                            $key = (int) substr($xaux, 1, 2);
                            if (TRUE === array_key_exists($key, $xarray)) {
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux);
                                if (substr($xaux, 1, 2) == 20)
                                    $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3;
                            }
                            else {
                                $key = (int) substr($xaux, 1, 1) * 10;
                                $xseek = $xarray[$key];
                                if (20 == substr($xaux, 1, 1) * 10)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 1, 2) < 10)
                        break;
                    case 3: // checa las unidades
                        if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
                            
                        } else {
                            $key = (int) substr($xaux, 2, 1);
                            $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                            $xsub = subfijo($xaux);
                            $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                        } // ENDIF (substr($xaux, 2, 1) < 1)
                        break;
                } // END SWITCH
            } // END FOR
            $xi = $xi + 3;
        } // ENDDO

        if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
            $xcadena.= " DE";

        if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
            $xcadena.= " DE";

        // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
        if (trim($xaux) != "") {
            switch ($xz) {
                case 0:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena.= "UN BILLON ";
                    else
                        $xcadena.= " BILLONES ";
                    break;
                case 1:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena.= "UN MILLON ";
                    else
                        $xcadena.= " MILLONES ";
                    break;
                case 2:
                    if ($xcifra < 1) {
                        $xcadena = "CERO PESOS $xdecimales/100 M.N.";
                    }
                    if ($xcifra >= 1 && $xcifra < 2) {
                        $xcadena = "UN PESO $xdecimales/100 M.N. ";
                    }
                    if ($xcifra >= 2) {
                        $xcadena.= " PESOS $xdecimales/100 M.N. "; //
                    }
                    break;
            } // endswitch ($xz)
        } // ENDIF (trim($xaux) != "")
        // ------------------      en este caso, para México se usa esta leyenda     ----------------
        $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
    } // ENDFOR ($xz)
    return trim($xcadena);
}

// END FUNCTION

function subfijo($xx)
{ // esta función regresa un subfijo para la cifra
    $xx = trim($xx);
    $xstrlen = strlen($xx);
    if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
        $xsub = "";
    //
    if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
        $xsub = "MIL";
    //
    return $xsub;
}

// END FUNCTION



			
?>