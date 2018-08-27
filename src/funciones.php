<?php

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
?>