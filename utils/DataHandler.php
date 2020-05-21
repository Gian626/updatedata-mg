<?php

 class DataHandler{

    public function estraiDati($dataname){

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/$dataname.json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
      ));
      
      $response = curl_exec($curl);
      
      curl_close($curl);
      return json_decode($response, true);
      

    }

    public function salvaDati($dati,$tablename){
         $queryes="";
         foreach($dati as $row){
            $index=0;

            $values="";
           
             foreach($row as $key=>$value){
                if($key=="long"){
                  $key="longitudine" ;
                }
                if($key=="lat"){
                   $key="latitudine";
                }
                if($key!="note_it" && $key!="note_en")
                {
                if(gettype($value)=="string"){
                   $value="'$value'";
                }
               
                 if(gettype($value)=="NULL"){
                    continue;
                 }

                  if($index==0){
                    $values="$value";
                  }
                  else
                  {
                     $values="$values,$value";
                  }
               }
                  $index++;
             }  
             
             $queryes="$queryes INSERT INTO `$tablename`($colums) VALUES ($values);"; 
            
         }
          echo "$queryes", "<br>";
          require_once('./MySQLDriver.php');
          $mysql=new MySQLDriver();
          print_r($mysql);
          $mysql->query($queryes);
        


    }
 }
 
   $qualcosa = new DataHandler();
   $dati= ($qualcosa->estraiDati("dpc-covid19-ita-andamento-nazionale"));
   // print_r($dati);
   $qualcosa->salvaDati($dati,"DatiNazionali")

?>