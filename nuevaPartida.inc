<?php

/*
 * Devuelve el 'id' de la nueva partida, el 'tipo' (facil, dificil o jugador) y la 'ficha'
 * asignada al jugador [O|X]. 
 * 
 * El id se calcula de este modo: crear uno en base al id de la ultima partida almacenada
 * si es contra la maquina, o contra jugador pero no hay ninguna disponible (las partidas
 * jugador vs jugador estan disponibles si solo hay un jugador en ellas, que queda a la espera
 * de que sa una un segundo jugador. Al unirse, no estará disponible para nadie más).
 * Si hay una partida jugador vs jugador disponible, el 'id' devuelvo es el de esa partida.
 * 
 * En las partidas contra la maquina el jugador siempre usa la ficha 'O'.
 * En las partidas contra un jugador el jugador que la crea siempre usa la ficha 'O', y el otro 'X'.
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
    
    //Devuelve el tipo (F, D, J), la ficha a usar (O, X) y el id de partida
    return array('tipo' => substr($respuesta, 0, 1), 'ficha' => substr($respuesta, 1, 1), 
                 'idPartida' => substr($respuesta, 2));
}

/*
 * Añadir la partida correspondiente en el XML dado.
 * Devuelve el 'tipo' de partida (facil, dificil o jugador), la ficha 'O' correspondiente 
 * a los que crean una partida del tipo que sea, y el 'id' de la misma.
 */
function crearPartida($partidasXML, $tipo){
    $id = (int)substr($partidasXML['ult_id'], 7) + 1;
    $nuevaPartida = $partidasXML->addChild('partida');
    $nuevaPartida['id'] = "partida" . $id;
    $nuevaPartida['tipo'] = $tipo; 
    $nuevaPartida['terminada'] = "no";
    if($tipo == "jugador"){
        $nuevaPartida['siguiente'] = "O";
        $nuevaPartida['ultimoMov'] = "";
        $nuevaPartida['abandonada'] = "no";
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

/*
 * Devuelve el 'tipo' jugador vs jugador, la ficha 'X' correspondiente a los que se
 * unen a una partida vs jugador, y el 'id' de la partida, si encuentra una partida
 * jugador vs jugador disponible (no terminada ni completa).
 */
function buscarPartidaJugador($partidasXML){
    foreach($partidasXML->partida as $partida)
        if($partida['tipo'] == "jugador" && $partida['terminada'] == "no"){
            $partida['tipo'] = "completa";
            $partidasXML->asXML('partidas.xml');
            return "JX" . substr($partida['id'], 7); //Asignar la ficha X y devolver id de partida.  
        }
    return "";
}