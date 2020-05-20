<?php
class MySQLDriver{
    private $connessione;
    public function __construct(){
        $this->connessione=new mysqli("localhost","root", "", "covid-19analytics", 3307);
        print_r($this->connessione);
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
            testo TEXT NOT NULL
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
$mysql=new MySQLDriver();
$mysql->creaTabelle();
?>
