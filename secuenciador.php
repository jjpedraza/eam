
    
<?php

include('src/head.php');

// echo "<form action='secuenciador.php' method='post'>";
echo "<div class='ventana'>";
echo "<h3 id='titulo'>Generador de ramas telefonicas</h3>";
echo "<label id='informador'></label>";

echo "<div><label>Lada</label><input type='number' size=3 placeholder='000' id='lada' name='lada' min=1 max=999 maxlength='3' required></div>";
echo "<div><label>Rama</label><input type='number' size=3 placeholder='000' id='rama' name='rama' min=1 max=999 maxlength='3' required></div>";

echo "<div><button id='btnGenera' class='btn btn-default' name='genera'>Generar</button></div>";
// echo "</form>";
echo "</div>";

echo "
<div id = 'resultado_contenedor' class = 'ventana resultado' style='display:none;'>

    <div id='resultado' style='display:none' ></div>";
   


    echo "<span id='preloader_genera' style='display:none;'>
    <img src='img/preloader.gif' style='width:100px;'><br>
    <b id='esperando'>Generando</b>
    </span>";

echo "</div>";




?>



<script>
function GeneraRama(){  
    var Lada = $('#lada').val();
    var Rama = $('#rama').val();

       
    if (Lada.length <= 0 || Rama.length <= 0 ){        
        alert('Escriba una Lada y una Rama de 3 digitos');

    } else {
        console.log("OK");
        console.log("Lada = " + Lada + ", Rama = " + Rama);
        //validacion para extraer solo los primeros 3 digitos
        if (Lada.length >3){
            Lada = Lada.substr(0, 3);
            alert('LADA: Solo se tomaran los primeros 3 digitos: ' + Lada);
        }

        if (Rama.length >3){
            Rama = Rama.substr(0, 3);
            alert('RAMA: Solo se tomaran los primeros 3 digitos: ' + Rama);
        }
        
        //en este punto ya tenemos depurada la lada y la rama
        $("#resultado_contenedor").css({'display':'inline-block'});
        $("#preloader_genera").css({'display':'inline-block'});
        $("#resultado").css({'display':'none'});       
        $("#resultado").html("");
        $.ajax({
                url: "secuenciador_genera.php",
                type: "post",   
                data: {lada: Lada, rama: Rama},
            success: function(data){	   
            $('#resultado').html(data+"\n");	
            $("#resultado").css({'display':'inline-block'});       
            $("#preloader_genera").css({'display':'none'});
            // $("#resultado_contenedor").css({'display':'none'});
            }
        });
    }

    
   
}



    
// var myWorker = new Worker("GeneraRamaCount.js");
// function Wk(){
//     var Lada = $('#lada').val();
//     var Rama = $('#rama').val();
//     myWorker.postMessage(Lada+Rama);    
//     myWorker.onmessage = function (oEvent) {
//             console.log("Avance de "+Lada+Rama+": " + oEvent.data);
//             $('#informador').html(oEvent.data);
//     };
    
    
// }

// function Wk_stop(){
//     myWorker.terminate();
//     myWorker = undefined;
// }

// setInterval(Wk,5000);
    
// ----------------------------


 $("#btnGenera").click(function(){
    GeneraRama();
      
 });



</script>



<?php

include('src/footer.php');

?>
