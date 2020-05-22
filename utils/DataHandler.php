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
      $columns = "";
      $values = "";
      $i = 0;
      foreach($dati as $row){
         $k = 0;

         // Deleting the useless values from the $row array
         if(array_key_exists("note_it", $row)){
            unset($row["note_it"]);
         }
         if(array_key_exists("note_en", $row)){
            unset($row["note_en"]);
         }

         foreach($row as $key=>$value){
            if($key == "long"){
               $key = "longitudine" ;
            }
            if($key == "lat"){
               $key = "latitudine";
            }
            if(gettype($value) == "string"){
              $value=str_replace("'"," ",$value);
               $value = "'$value'";

            }

            // If the values is null, then i set it as a string (compatible with mysql)
            if(gettype($value) == "NULL"){
               $value = "NULL";
            }
            
            // Preparing columns name array
            if($i == 0){
               if($k == 0){
                  $columns = "$key";
               }else{
                  $columns = "$columns, $key";
               }
            }

            /****
               Creating the (values 1), (values 2), (values 3), .. (values n); thing; 
               NOTE: the syntax is very important, that's why there are a lot of conditions
            ****/
            if($k == 0) {
               $values = "$values ($value,";
            } else if ($k == sizeof($row) - 1){
               if($i == sizeof($dati) - 1){
                  $values = "$values $value);";
               }else{
                  $values = "$values $value),";
               }
            } else {
               $values = "$values $value,";
            }
            $k++; 
         }
         $i++;
      }

      // This is the final query
      $query = "INSERT INTO $tablename ($columns) VALUES $values";
      echo "<br><br><br><br>";
      echo $query;
      

       require_once(__DIR__.'/MySQLDriver.php');
       $mysql=new MySQLDriver();
       $mysql->query($query);
   }

 }
 
  
?>