<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function calcularIdPartida(){
    $fich_partida = "partidas.xml";
    if (! file_exists ( $fich_partida ))
        $partidasXML = new SimpleXMLElement ('<?xml version="1.0" encoding="UTF-8"?>
            <partidas ult_id="partida0">
            </partidas>');
    else 
        $partidasXML = simplexml_load_file ( $fich_partida );
    
    $tipo = $_REQUEST['vs'];
    if($tipo == "maquina")
        $tipo = $_REQUEST['dificultad'];
    //En este punto $tipo es 'facil', 'dificil' o 'jugador'.
    
    if($tipo == "jugador"){
        $respuesta = buscarPartidaJugador($partidasXML);
        if($respuesta == "")
            //Crear partida jugador vs jugador
            $respuesta = crearPartida($partidasXML, $tipo);
    }else
        //Crear partida jugador vs maquina de tipo 'facil' o 'dificil'
        $respuesta = crearPartida($partidasXML, $tipo);
    return $respuesta; //Devuelve el tipo (F, D, J), la ficha a usar (O, X) y el id de partida.
}

function crearPartida($partidasXML, $tipo){
    $id = (int)substr($partidasXML['ult_id'], 7) + 1;
    $nuevaPartida = $partidasXML->addChild('partida');
    $nuevaPartida['id'] = "partida" . $id;
    $nuevaPartida['tipo'] = $tipo; 
    $nuevaPartida['terminada'] = "no";
    if($tipo == "jugador"){
        $nuevaPartida['siguiente'] = "O";
        $nuevaPartida['ultimoMov'] = "";
    }
    $nuevaCasilla1 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla1['id'] = "00";
    $nuevaCasilla2 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla2['id'] = "01";
    $nuevaCasilla3 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla3['id'] = "02";
    $nuevaCasilla4 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla4['id'] = "10";
    $nuevaCasilla5 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla5['id'] = "11";
    $nuevaCasilla6 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla6['id'] = "12";
    $nuevaCasilla7 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla7['id'] = "20";
    $nuevaCasilla8 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla8['id'] = "21";
    $nuevaCasilla9 = $nuevaPartida->addChild('casilla');
    $nuevaCasilla9['id'] = "22";
    
    $partidasXML['ult_id'] = "partida" . $id;
    $partidasXML->asXML('partidas.xml');
    
    //Establecer tipo para facilitar funcion javascript de AJAX
    if($tipo == "facil") $tipo = "F";
    else if($tipo == "dificil") $tipo = "D";
    else $tipo = "J";
    
    return $tipo . "O" . $id; //Asignar la ficha O y devolver id de partida.
}

function buscarPartidaJugador($partidasXML){
    foreach($partidasXML->partida as $partida)
        if($partida['tipo'] == "jugador" && $partida['terminada'] == "no"){
            $partida['tipo'] = "completa";
            $partidasXML->asXML('partidas.xml');
            return "JX" . substr($partida['id'], 7); //Asignar la ficha X y devolver id de partida.  
        }
    return "";
}
?>