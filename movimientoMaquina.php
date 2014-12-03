<?php
require_once('movimientos.php');

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
        // Mostrar movimiento ganador del rival (R), la maquina.
        $response['jugador'] = "R";
        echo json_encode($response); //echo "R" . $estado . $jugada;
    }
} else {
    setPartidaTerminada();
    // Termina la partida por el movimiento del jugador.
    $response['jugador'] = "J";
    echo json_encode($response); //echo "J" . $estado;
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
        foreach ($preferidos as $mov) 
            if ($partida [$mov [0]] [$mov [1]] == "")
                return "{$mov[0]}" . "{$mov[1]}"; //Devolver primera casilla preferida libre
    }else 
        return $jugada; //Devolver la jugada bloquente (para evitar derrota de la maquina)
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

function setJugada($jugada, $jugador) {
    $partidasXML = getPartidasXML();
    foreach ($partidasXML->partida as $partida)
        if ($partida['id'] == "partida" . $_REQUEST['id']) {
            $partida = setFichaJugada($partida, $jugada, $jugador);
            break;
        }
    $partidasXML->saveXML("partidas.xml");
    return true;
}