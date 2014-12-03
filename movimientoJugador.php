<?php
require_once('movimientos.inc');

setJugada($_REQUEST ['pos'], $_REQUEST['ficha']);
$estado = getEstado(); //Obtener nuevo estado de la partida
if ($estado != "continuar")
    setPartidaTerminada("noPorAbandono");
echo $estado;


/*
 * Almacenar la ficha del jugador [O|X] en la partida,
 * y actualizar turno y ultimo movimiento.
 */
function setJugada($jugada, $jugador) {
    if ($jugador == "O")
        $jugadorRival = "X";
    else
        $jugadorRival = "O";

    $partidasXML = getPartidasXML();
    foreach ($partidasXML->partida as $partida)
        if ($partida['id'] == "partida" . $_REQUEST['id']) {
            $partida['siguiente'] = $jugadorRival;
            $partida['ultimoMov'] = $jugada;
            $partida = setFichaJugada($partida, $jugada, $jugador);
            break;
        }
    $partidasXML->saveXML("partidas.xml");
    return true;
}