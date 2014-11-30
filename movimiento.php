<?php

function getTableroXML() {
    $fich_partida = "partida.xml";
    if (! file_exists ( $fich_partida )) {
        $tableroXML = new SimpleXMLElement ('<?xml version="1.0" encoding="UTF-8"?>
                <tablero>
    <casilla id="00"></casilla>
    <casilla id="01"></casilla>
    <casilla id="02"></casilla>
    <casilla id="10"></casilla>
    <casilla id="11"></casilla>
    <casilla id="12"></casilla>
    <casilla id="20"></casilla>
    <casilla id="21"></casilla>
    <casilla id="22"></casilla>
</tablero>' );
    } else {
        $tableroXML = simplexml_load_file ( $fich_partida );
    }
    return $tableroXML;
}
function getTablero() {
    $tableroXML = getTableroXML ();
    $tablero = array (
                    array (
                            $tableroXML->casilla [0]->ficha [0],
                            $tableroXML->casilla [1]->ficha [0],
                            $tableroXML->casilla [2]->ficha [0] 
                    ),
                    array (
                            $tableroXML->casilla [3]->ficha [0],
                            $tableroXML->casilla [4]->ficha [0],
                            $tableroXML->casilla [5]->ficha [0] 
                    ),
                    array (
                            $tableroXML->casilla [6]->ficha [0],
                            $tableroXML->casilla [7]->ficha [0],
                            $tableroXML->casilla [8]->ficha [0] 
                    ) 
    );
    return $tablero;
}

function getMovimiento() {
    $tablero = getTablero ();
    $preferidos = array (
                    array (1,1),
                    array (0,0),
                    array (0,2),
                    array (2,0),
                    array (2,2),
                    array (0,1),
                    array (1,0),
                    array (1,2),
                    array (2,1)
                );
    foreach ( $preferidos as $mov ) {
            if ($tablero [$mov [0]] [$mov [1]] == "")
                    return "{$mov[0]}" . "{$mov[1]}";
    }
    return null;
}    

function setJugada($jugada, $jugador) {
    $tableroXML = getTableroXML ();
    foreach ( $tableroXML->casilla as $casilla ) {
        foreach ( $casilla->attributes() as $a => $b ){
            if ($b == $jugada)
                $casilla->addChild ( "ficha", $jugador );
        }
    }
    $tableroXML->saveXML("partida.xml");
    return true;
}

//Obtiene el estado de la partida: 'ganaO', 'ganaX', 'empate' o 'continuar'
function getEstado(){
    $tablero = getTablero();
    
    $op1 = $tablero[0][0].$tablero[0][1].$tablero[0][2];
    if($op1 == "XXX" || $op1 == "OOO") return "gana".substr($op1, 0, 1);
    
    $op2 = $tablero[1][0].$tablero[1][1].$tablero[1][2];
    if($op2 == "XXX" || $op2 == "OOO") return "gana".substr($op2, 0, 1);
    
    $op3 = $tablero[2][0].$tablero[2][1].$tablero[2][2];
    if($op3 == "XXX" || $op3 == "OOO") return "gana".substr($op3, 0, 1);
    
    $op4 = $tablero[0][0].$tablero[1][0].$tablero[2][0];
    if($op4 == "XXX" || $op4 == "OOO") return "gana".substr($op4, 0, 1);
    
    $op5 = $tablero[0][1].$tablero[1][1].$tablero[2][1];
    if($op5 == "XXX" || $op5 == "OOO") return "gana".substr($op5, 0, 1);
    
    $op6 = $tablero[0][2].$tablero[1][2].$tablero[2][2];
    if($op6 == "XXX" || $op6 == "OOO") return "gana".substr($op6, 0, 1);
    
    $op7 = $tablero[0][0].$tablero[1][1].$tablero[2][2];
    if($op7 == "XXX" || $op7 == "OOO") return "gana".substr($op7, 0, 1);
    
    $op8 = $tablero[0][2].$tablero[1][1].$tablero[2][0];
    if($op8 == "XXX" || $op8 == "OOO") return "gana".substr($op8, 0, 1);
    
    if(strlen($op1.$op2.$op3) == 9) return "empate";
    else return "continuar";
}

$jugada = $_REQUEST ['pos'];
setJugada($jugada, "O");
$estado = getEstado();
if($estado == "continuar"){
    $jugada = getMovimiento();
    setJugada($jugada, "X");
    $estado = getEstado();
    if($estado == "continuar")
        echo $jugada;
    else
        // Termina la partida por el movimiento de la maquina.
        // Mostrar su ultimo movimiento en el tablero.
        echo "M".$estado.$jugada;
}else
    // Termina la partida por el movimiento del jugador.
    echo "J".$estado;
?>