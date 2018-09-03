<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
    <script src="unica/jquery-3.2.1.min.js"></script>

    <style>
        #preloader {
            width: 100%;
            background-color: red: color:white;
            padding: 20px; margin: 0px;
            position: absolute; top: 0px;
            display: none;
        }    
    </style>
</head>
<body>
    
<?php

?>
<div id='resultados'>
<h1>Resultados:</h1>
</div>


<script>
function comb(alfabeto, n, resultados, resultado) {
    if(!resultado) {
        resultado = [];
    }
    for(var i=0; i<alfabeto.length; ++i) {
        var newResultado = resultado.slice();
        var newAlfabeto = alfabeto.slice();
        newResultado.push(alfabeto[i]);
        newAlfabeto.splice(i, 1);
        if(n>1) {
            comb(newAlfabeto, n-1, resultados, newResultado);
        } else {
            resultados.push(newResultado);
        }
        
    }
}

var cadena = [0,1,2,3,4,5,6,7,8,9];//Caracteres que va a combinar
var arrayCombinaciones = [];//Almacena las combinaciones
var grupo = 4;//le indico la cantidad de cuantos caracteres quiero que sean las combinaciones.

comb(cadena, grupo, arrayCombinaciones);    

var num4d = "0,2,0,4";
$.each(arrayCombinaciones, function (ind, elem) { 
//   console.log('¡Hola :'+elem+'!'); 
    // num4d = elem;
    num4d.replace(",","");

    
  $('#resultados').append(num4d + "<br>");
  
}); 

// $('#resultados').html(JSON.stringify(arrayCombinaciones));


// EnvioPHP(JSON.stringify(arrayCombinaciones));






function EnvioPHP(Datos){   
   
   $("#preloader").css({'display':'inline-block'});
//    $("#Pase_"+IdPase).css({'display':'none','color':'gray'});
   $.ajax({
       url: "sms_secuenciador_4d.php",
      type: "post",
   //    data: "id="+IdPase, "nitavu=" + Nitavu
      data: {Datos4d: Datos },
      success: function(data){
       $("#resultados").html(data+"\n");
       $("#preloader").css({'display':'none'});
      }
   });
   
}

// console.log(JSON.stringify(arrayCombinaciones));

</script>

<div id='preloader'>
    Cargando
</div>

</body>
</html>