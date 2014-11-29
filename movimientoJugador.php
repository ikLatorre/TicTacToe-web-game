<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function getPartidasXML() {
    $fich_partida = "partidas.xml";
    return simplexml_load_file ($fich_partida);
}

function getTablero() {
    $partidasXML = getPartidasXML ();
    //Obtener tablero de la partida del usuario (segun id de la sesion)
    foreach($partidasXML->partida as $tablero){
        if($tablero['id'] == "partida" . $_REQUEST['id']){
            $partida = array (
                        array (
                                $tablero->casilla [0]->ficha [0],
                                $tablero->casilla [1]->ficha [0],
                                $tablero->casilla [2]->ficha [0] 
                        ),
                        array (
                                $tablero->casilla [3]->ficha [0],
                                $tablero->casilla [4]->ficha [0],
                                $tablero->casilla [5]->ficha [0] 
                        ),
                        array (
                                $tablero->casilla [6]->ficha [0],
                                $tablero->casilla [7]->ficha [0],
                                $tablero->casilla [8]->ficha [0] 
                        ) 
                    );
            return $partida;
        }
    }
    return null;
}


//Obtiene el estado de la partida: 'ganaO', 'ganaX', 'empate' o 'continuar'
function getEstado(){
    $partida = getTablero();
    
    if(is_null($partida)){
        echo ("Error al obtener el estado del tablero.");
        exit();
    }
    
    $op1 = $partida[0][0].$partida[0][1].$partida[0][2];
    if($op1 == "XXX" || $op1 == "OOO") return "gana".substr($op1, 0, 1);
    
    $op2 = $partida[1][0].$partida[1][1].$partida[1][2];
    if($op2 == "XXX" || $op2 == "OOO") return "gana".substr($op2, 0, 1);
    
    $op3 = $partida[2][0].$partida[2][1].$partida[2][2];
    if($op3 == "XXX" || $op3 == "OOO") return "gana".substr($op3, 0, 1);
    
    $op4 = $partida[0][0].$partida[1][0].$partida[2][0];
    if($op4 == "XXX" || $op4 == "OOO") return "gana".substr($op4, 0, 1);
    
    $op5 = $partida[0][1].$partida[1][1].$partida[2][1];
    if($op5 == "XXX" || $op5 == "OOO") return "gana".substr($op5, 0, 1);
    
    $op6 = $partida[0][2].$partida[1][2].$partida[2][2];
    if($op6 == "XXX" || $op6 == "OOO") return "gana".substr($op6, 0, 1);
    
    $op7 = $partida[0][0].$partida[1][1].$partida[2][2];
    if($op7 == "XXX" || $op7 == "OOO") return "gana".substr($op7, 0, 1);
    
    $op8 = $partida[0][2].$partida[1][1].$partida[2][0];
    if($op8 == "XXX" || $op8 == "OOO") return "gana".substr($op8, 0, 1);
    
    if(strlen($op1.$op2.$op3) == 9) return "empate";
    else return "continuar";
}

function setJugada($jugada, $ficha) {
    if($ficha == "O") $fichaRival = "X";
    else $fichaRival = "O";
    
    $partidasXML = getPartidasXML ();
    foreach($partidasXML->partida as $partida){
        if($partida['id'] == "partida".$_REQUEST['id']){
            $partida['siguiente'] = $fichaRival; 
            $partida['ultimoMov'] = $jugada;
            foreach ( $partida->casilla as $casilla ) {
                foreach ($casilla->attributes() as $a => $b){
                    if ($b == $jugada)
                        $casilla->addChild ( "ficha", $ficha );
                }
            }
        }
    }
    $partidasXML->saveXML("partidas.xml");
    return true;
}

function setPartidaTerminada(){
    $partidasXML = getPartidasXML ();
    foreach($partidasXML->partida as $partida){
        if($partida['id'] == "partida".$_REQUEST['id']){
            $partida['terminada'] = "si";
        }
    }
    $partidasXML->saveXML("partidas.xml");
    return true;
}

//Devuelve el ultimo movimiento del rival, una vez lo haya hecho 
//y le toque al jugador que realiza esta peticion.
function turnoRivalFinalizado($ficha){
    $partidasXML = getPartidasXML ();
    foreach($partidasXML->partida as $partida){
        if($partida['id'] == "partida".$_REQUEST['id']){
            if($partida['siguiente'] == $ficha) return $partida['ultimoMov'];
            else return "";
        }
    }
}

function esperarMovimientoRival($ficha){
    $movimientoRival = turnoRivalFinalizado($ficha);
    while($movimientoRival == ""){
        sleep(3);
        $movimientoRival = turnoRivalFinalizado($ficha);
    }
    return $movimientoRival;
}

$jugada = $_REQUEST ['pos'];
$ficha = $_REQUEST['ficha'];
if($ficha == "O") $fichaRival = "X";
else $fichaRival = "O";

setJugada($jugada, $ficha);
$estado = getEstado();

if($estado == "continuar"){
    $jugada = esperarMovimientoRival($ficha);
    //setJugada($jugada, $fichaRival); --> El rival ya lo habra hecho
    $estado = getEstado();
    if($estado == "continuar")
        echo $jugada;
    else{
        //setPartidaTerminada(); --> No hace falta, lo habra realizado el rival
        // Termina la partida por el movimiento del rival.
        // Mostrar su ultimo movimiento en la partida.
        // 'R' representa 'rival'
        echo "R".$estado.$jugada;
    }
}else{
    setPartidaTerminada();
    // Termina la partida por el movimiento del jugador.
    echo "J".$estado;
}