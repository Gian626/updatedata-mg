  






<?php

  /*
    MYSQLDRIVER A CONNECTION TO THE MYSQL DB
  */
  class MySQLDriver{
    private $connessione;
    
    public function __construct(){
      $this->connessione=new mysqli("sql2.freemysqlhosting.net","sql2342765", " uY4%eC4!", "sql2342765", 3306);
    }

    public function creaTabelle(){
      $queries= array("
        create table IF NOT EXISTS Utenti (
          username VARCHAR(255)  PRIMARY KEY,
          password TEXT NOT NULL,
          email  TEXT NOT NULL,
          isAdmin BOOLEAN NOT NULL
        );
        ","
        create table IF NOT EXISTS Articoli (
          ID_Art INT(10) AUTO_INCREMENT  PRIMARY KEY,
          titolo TEXT NOT NULL,
          testo TEXT NOT NULL,
          created_at timestamp default current_timestamp,
          categoria TEXT NOT NULL,
          luogo  TEXT NOT NULL
        );
        ","
        create table IF NOT EXISTS ImagginiArticolo(
          ID_Img INT(10) AUTO_INCREMENT PRIMARY KEY,
          url TEXT NOT NULL, 
          ID_Art_fk INT(10) NOT NULL,
          FOREIGN KEY (ID_Art_fk)
          REFERENCES  Articoli(ID_Art)
          ON UPDATE  CASCADE  ON DELETE CASCADE
        );
        ","
        create table IF NOT EXISTS DatiNazionali(
          ID_n INT(10)  AUTO_INCREMENT PRIMARY KEY,
          data DATETIME,
          stato TEXT,
          ricoverati_con_sintomi INT(10),
          terapia_intensiva INT (10),
          totale_ospedalizzati INT (10),
          isolamento_domiciliare INT (10),
          totale_positivi INT (10),
          variazione_totale_positivi INT (10) ,
          nuovi_positivi INT (10) ,
          dimessi_guariti INT (10),
          deceduti INT (10),
          totale_casi INT (10),
          tamponi INT (10),
          casi_testati INT (10)
        );
        ","
        create table IF NOT EXISTS DatiRegionali(
          ID_r INT(10) AUTO_INCREMENT PRIMARY KEY ,
          data DATETIME,
          stato TEXT,
          codice_regione INT(10),
          denominazione_regione TEXT ,
          latitudine FLOAT ,
          longitudine FLOAT ,
          ricoverati_con_sintomi INT(10),
          terapia_intensiva INT(10),
          totale_ospedalizzati INT(10),
          isolamento_domiciliare INT(10),
          totale_positivi INT(10),
          variazione_totale_positivi INT(10),
          nuovi_positivi INT(10),
          dimessi_guariti INT(10),
          deceduti INT(10),
          totale_casi INT(10),
          tamponi INT(10),
          casi_testati INT(10)
        );
        ","   
        create table IF NOT EXISTS DatiProvinciali(
          ID_p INT(10)  AUTO_INCREMENT PRIMARY KEY ,
          data DATETIME ,
          stato TEXT ,
          codice_regione INT(10) ,
          denominazione_regione TEXT ,
          codice_provincia INT(10) ,
          denominazione_provincia TEXT ,
          sigla_provincia TEXT ,
          latitudine FLOAT ,
          longitudine FLOAT ,
          totale_casi INT(10)
        );
      ");
      foreach($queries as $query){
        $this->connessione->query($query);
      }
    }

    public function  query($query){
      return $this->connessione->query($query);
    }
  }

  /*
    DATAHANDLER CLASS, USED TO FETCH, PARSE AND SAVE THE COVID-19 DATA
  */
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
      $mysql = new MySQLDriver();
      $columns = "";
      $values = "";
      $i = 0;
      $actualRow = 0;
      $maxReached = false;
      foreach($dati as $row){
        $maxReached = false;
        $k = 0;
        $actualRow++;
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
            if($i == sizeof($dati) - 1 || $actualRow == 200){
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
        if($actualRow == 200){
          $actualRow = 0;
          $maxReached = true;
          $query = "INSERT INTO $tablename ($columns) VALUES $values";
          $mysql->query($query);
          $values = "";
        }
      }
      if(!$maxReached){
        $query = "INSERT INTO $tablename ($columns) VALUES $values";
        $mysql->query($query);
      }


    }
    

  }

  $mysql = new MySQLDriver();
  $mysql->creaTabelle();
  $result = $mysql->query("select * from datinazionali");
  $datahandler = new DataHandler();

  if($result->num_rows == 0){
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



