<?php

function getPartidasXML() {
    $fich_partida = "partidas.xml";
    return simplexml_load_file($fich_partida);
}

function getTablero() {
    $partidasXML = getPartidasXML();
    //Obtener tablero de la partida del usuario (segun id de la sesion)
    foreach ($partidasXML->partida as $tablero) {
        if ($tablero['id'] == "partida" . $_REQUEST['id']) {
            $partida = array(
                array(
                    $tablero->casilla [0]->ficha [0],
                    $tablero->casilla [1]->ficha [0],
                    $tablero->casilla [2]->ficha [0]
                ),
                array(
                    $tablero->casilla [3]->ficha [0],
                    $tablero->casilla [4]->ficha [0],
                    $tablero->casilla [5]->ficha [0]
                ),
                array(
                    $tablero->casilla [6]->ficha [0],
                    $tablero->casilla [7]->ficha [0],
                    $tablero->casilla [8]->ficha [0]
                )
            );
            return $partida;
        }
    }
}

//Obtiene el estado de la partida: 'ganaO', 'ganaX', 'empate' o 'continuar'
function getEstado() {
    $partida = getTablero();
    $rayas = array(
        array(0, 0, 0, 1, 0, 2),
        array(1, 0, 1, 1, 1, 2),
        array(2, 0, 2, 1, 2, 2),
        array(0, 0, 1, 0, 2, 0),
        array(0, 1, 1, 1, 2, 1),
        array(0, 2, 1, 2, 2, 2),
        array(0, 0, 1, 1, 2, 2),
        array(0, 2, 1, 1, 2, 0)
    );
    for ($i = 0; $i <= 7; $i++) {
        $ganador = getGanador($rayas[$i], $partida);
        if ($ganador != "")
            return "gana" . $ganador;
    }

    if (estaCompleto($partida))
        return "empate";
    else
        return "continuar";
}

//True si no quedan casillas vacias en el tablero. False e.c.c.
function estaCompleto($partida) {
    for ($i = 0; $i <= 2; $i++)
        for ($j = 0; $j <= 2; $j++)
            if ($partida[$i][$j] == "")
                return false;
    return true;
}

//Dada una raya del tablero, obtener el ganador si lo hubiera [O|X]
function getGanador($raya, $partida) {
    $estadoRaya = $partida[$raya[0]][$raya[1]] . $partida[$raya[2]][$raya[3]] . $partida[$raya[4]][$raya[5]];
    if ($estadoRaya == "XXX" || $estadoRaya == "OOO")
        return substr($estadoRaya, 0, 1);
    else
        return "";
}

function setFichaJugada($partida, $jugada, $jugador){
    foreach ($partida->casilla as $casilla)
        foreach ($casilla->attributes() as $a => $b)
            if ($b == $jugada){
                $casilla->addChild("ficha", $jugador);
                return $partida;
            }       
}

function setPartidaTerminada() {
    $partidasXML = getPartidasXML();
    foreach ($partidasXML->partida as $partida)
        if ($partida['id'] == "partida" . $_REQUEST['id']) {
            $partida['terminada'] = "si";
            break;
        }
    $partidasXML->saveXML("partidas.xml");
    return true;
}