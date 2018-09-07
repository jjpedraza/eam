


onmessage = function (e) {
    // postMessage("Valor recibido: " + e.data);

    // var valor = oEvent.data;
     xmlhttp=new XMLHttpRequest();
     xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            console.log("Generando.. " + e.data[0] + e.data[1] + " => " + this.responseText);
            postMessage(this.responseText);
         }
    }
    
    xmlhttp.open("GET","secuenciador_genera.php?lada="+e.data[0]+"&rama="+e.data[1],true);

    xmlhttp.send();



    
};

// }
 