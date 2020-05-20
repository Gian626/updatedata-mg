<?php

 class DataHandler{

    public function EstraiDati($result){

    $estrazione =( "https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-andamento-nazionale.json");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $estrazione);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $avvio = curl_exec($curl);
    curl_close($curl);
    var_dump(json_decode($avvio));
    print_r($estrazione);


    }

 }


?>