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



	FUNCTION permutations($letters,$num){ 
		$last = STR_REPEAT($letters{0},$num); 
		$result = ARRAY(); 
		WHILE($last != STR_REPEAT(lastchar($letters),$num)){ 
			$result[] = $last; 
			$last = char_add($letters,$last,$num-1); 
		} 
		$result[] = $last; 
		RETURN $result; 
	} 
	FUNCTION char_add($digits,$string,$char){ 
		IF($string{$char} <> lastchar($digits)){ 
			$string{$char} = $digits{STRPOS($digits,$string{$char})+1}; 
			RETURN $string; 
		}ELSE{ 
			$string = changeall($string,$digits{0},$char); 
			RETURN char_add($digits,$string,$char-1); 
		} 
	} 
	FUNCTION lastchar($string){ 
		RETURN $string{STRLEN($string)-1}; 
	} 
	FUNCTION changeall($string,$char,$start = 0,$end = 0){ 
		IF($end == 0) $end = STRLEN($string)-1; 
		FOR($i=$start;$i<=$end;$i++){ 
			$string{$i} = $char; 
		} 
		RETURN $string; 
	} 
	
?>