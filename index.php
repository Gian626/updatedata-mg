<?php
     require_once("./utils/DataHandler.php");
     $dataname=array(
         "datinazionali"=>"dpc-covid19-ita-andamento-nazionale",
         "datiregionali"=>"dpc-covid19-ita-regioni",
         "datiprovinciali"=>"dpc-covid19-ita-province"
        );
        $datahandler=new DataHandler();
       foreach($dataname as $tablename=>$filename){
            $dati=$datahandler->estraiDati($filename);
            $datahandler->salvaDati($dati,$tablename);
       }

?>



