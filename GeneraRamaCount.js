

// function GeneraRama_count(){  
    
// Al recibir un mensaje se ejecuta
        
            
    
    

    
   


// setInterval(GeneraRama_count,1000);



onmessage = function (oEvent) {
    // postMessage("Valor recibido: " + oEvent.data);

    var valor = oEvent.data;

    // if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    // } else { // code for IE6, IE5
    //      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    // }

    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            // document.getElementById("div_cartero2").innerHTML=this.responseText;
            console.log("Count de " + valor + " => " + this.responseText);
            postMessage(this.responseText);
         }
    }
    
    xmlhttp.open("GET","secuenciador_count.php?valor="+valor+"",true);
    xmlhttp.send();




    // $.ajax({
    //     url: "secuenciador_count.php",
    //     type: "post",   
    //     data: {valor: valor},
    //     success: function(data){	   
    //         // self.postMessage( data );  // Contestamos con la suma
    //         console.log("Count de " + valor() + " => " + data);
    //         postMessage(data);
    //     }
    // });

    
};

// }
 