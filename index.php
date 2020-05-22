<?php
     require_once("./utils/DataHandler.php");
     require_once("./utils/MySQLDriver.php");
     $mysql= new MySQLDriver();
     $result=$mysql->query("select * from datinazionali");
     $datahandler=new DataHandler();
     if($result->num_rows==0){
        $alldataname=array(

               "datinazionali"=>"dpc-covid19-ita-andamento-nazionale",
               "datiregionali"=>"dpc-covid19-ita-regioni",
               "datiprovinciali"=>"dpc-covid19-ita-province"

        );
        foreach($alldataname as $tablename=>$filename){
            $dati=$datahandler->estraiDati($filename);
            $datahandler->salvaDati($dati,$tablename);
       }
     }
     else{
        $dataname=array(
            "datinazionali"=>"dpc-covid19-ita-andamento-nazionale-latest",
           "datiregionali"=>"dpc-covid19-ita-regioni-latest",
           "datiprovinciali"=>"dpc-covid19-ita-province-latest"
       );
         foreach($dataname as $tablename=>$filename){
              $dati=$datahandler->estraiDati($filename);
              $datahandler->salvaDati($dati,$tablename);
         }
     }

?>



