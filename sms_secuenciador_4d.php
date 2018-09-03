
<?php
if (isset($_POST['Datos4d'])){
   // $string = file_get_contents("/home/michael/test.json");
    $json_a = json_decode($_POST['Datos4d'], true);
    var_dump($json_a);
    // foreach ($json_a as $person_name => $dat) {
    //     // echo $dat['']."<br>";
        
    // }



} else {echo "sin variable";}



?>