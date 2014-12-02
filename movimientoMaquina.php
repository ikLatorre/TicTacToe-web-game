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

function getPosicionBloqueante($raya, $partida) {
    $opcion = "";
    $posicionBloqueante = "";

    if ($partida[$raya[0]][$raya[1]] == "") {
        $opcion = $opcion . "-";
        $posicionBloqueante = $raya[0] . $raya[1];
    } else
        $opcion = $opcion . $partida[$raya[0]][$raya[1]];

    if ($partida[$raya[2]][$raya[3]] == "") {
        $opcion = $opcion . "-";
        $posicionBloqueante = $raya[2] . $raya[3];
    } else
        $opcion = $opcion . $partida[$raya[2]][$raya[3]];

    if ($partida[$raya[4]][$raya[5]] == "") {
        $opcion = $opcion . "-";
        $posicionBloqueante = $raya[4] . $raya[5];
    } else
        $opcion = $opcion . $partida[$raya[4]][$raya[5]];

    if (mb_substr_count($opcion, "O") == 2 && mb_substr_count($opcion, "-") == 1) {
        return $posicionBloqueante;
    }
    return "";
}

function getMovimientoBloqueante($partida) {
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
        $posicionBloqueante = getPosicionBloqueante($rayas[$i], $partida);
        if ($posicionBloqueante != "")
            return $posicionBloqueante;
    }
    return "";
}

function getMovimiento($dificultad) {
    $partida = getTablero();
    if ($dificultad == "D")
        $jugada = getMovimientoBloqueante($partida);
    else
        $jugada = "";
    if ($jugada == "") { //Es nivel 'facil', o 'dificil' pero no hay derrota que evitar
        $preferidos = array(
            array(1, 1),
            array(0, 0),
            array(0, 2),
            array(2, 0),
            array(2, 2),
            array(0, 1),
            array(1, 0),
            array(1, 2),
            array(2, 1)
        );
        foreach ($preferidos as $mov) {
            if ($partida [$mov [0]] [$mov [1]] == "")
                return "{$mov[0]}" . "{$mov[1]}"; //Devolver primera casilla preferida libre
        }
    }else {
        return $jugada; //Devolver la jugada bloquente (para evitar derrota de la maquina)
    }
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

function setJugada($jugada, $jugador) {
    $partidasXML = getPartidasXML();
    foreach ($partidasXML->partida as $partida)
        if ($partida['id'] == "partida" . $_REQUEST['id']) {
            foreach ($partida->casilla as $casilla)
                foreach ($casilla->attributes() as $a => $b)
                    if ($b == $jugada)
                        $casilla->addChild("ficha", $jugador);
            break;
        }
    $partidasXML->saveXML("partidas.xml");
    return true;
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

$response = array('jugador' => "", 'estado' => "", 'jugada' => "");
$response['jugada'] = $_REQUEST['pos'];
setJugada($response['jugada'], "O");
$response['estado'] = getEstado();
if ($response['estado'] == "continuar") {
    $response['jugada'] = getMovimiento($_REQUEST['dificultad']);
    setJugada($response['jugada'], "X");
    $response['estado'] = getEstado();
    if ($response['estado'] == "continuar")
        echo json_encode($response); //$jugada;
    else {
        setPartidaTerminada();
        // Termina la partida por el movimiento de la maquina.
        // Mostrar su ultimo movimiento en la partida.
        // 'R' representa 'rival', la maquina en este caso
        $response['jugador'] = "R";
        echo json_encode($response); //echo "R" . $estado . $jugada;
    }
} else {
    setPartidaTerminada();
    // Termina la partida por el movimiento del jugador.
    $response['jugador'] = "J";
    echo json_encode($response); //echo "J" . $estado;
}
?>