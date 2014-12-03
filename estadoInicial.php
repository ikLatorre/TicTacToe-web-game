<?php

$partidasXML = simplexml_load_file ("partidas.xml");
foreach($partidasXML->partida as $partida){
    if($partida['id'] == "partida".$_REQUEST['id']){
        //Generar xml de la partida
        $casillasString = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"
        . "<partida siguiente='". $partida['siguiente'] ."' ultimoMov='". $partida['ultimoMov'] ."'>";
        foreach ( $partida->casilla as $casilla ) {
            $casillasString = $casillasString . "<casilla id='". $casilla['id'] ."'>";
            if(isset($casilla->ficha)){
                $casillasString = $casillasString . "<ficha>" . $casilla->ficha . "</ficha>";
            }
            $casillasString = $casillasString . "</casilla>";
        }
        $casillasString = $casillasString . "</partida>";
       
        Header('Content-type: text/xml');
        $xmlElement = new SimpleXMLElement($casillasString);
        echo $xmlElement->asXML();
        exit();
    }
}