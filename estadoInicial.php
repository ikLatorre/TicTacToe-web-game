<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$partidasXML = simplexml_load_file ("partidas.xml");
foreach($partidasXML->partida as $partida){
    if($partida['id'] == "partida".$_REQUEST['id']){
        //Generar xml de la partida
        $casillasString = "";
        $casillasString = "<?xml version='1.0' encoding='UTF-8'?>"
        . "<partida siguiente='". $partida['siguiente'] ."' ultimoMov='". $partida['ultimoMov'] ."'>";
        foreach ( $partida->casilla as $casilla ) {
            $casillasString = $casillasString . "<casilla id='". $casilla['id'] ."'>";
            if(isset($casilla->ficha)){
                $casillasString = $casillasString . "<ficha>" . $casilla->ficha . "</ficha>";
            }
            $casillasString = $casillasString . "</casilla>";
        }
        $casillasString = $casillasString . "</partida>";
        echo $casillasString;
        exit();
    }
}
?>