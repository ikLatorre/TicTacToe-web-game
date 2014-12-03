<?php
require_once('movimientos.php');

$response = array('jugada' => (string)consultarTurno(), 'estado' => "");
if($response['jugada'] == "esperar" || $response['jugada'] == "abandonoRival")
    echo json_encode($response); //Devuelve 'esperar' o 'abandonoRival'
else{
    $response['estado'] = getEstado();
    //Devuelve ultimo movimiento ('XX') y nuevo estado ('ganaO', 'ganaX', 'empate' o 'continuar')
    echo json_encode($response);  
}


/* 
 * Devuelve 'abandonoRival' si el rival ha abandonado la partida,
 * la posicion del ultimo movimiento del rival ('XX') si le toca al jugador
 * o 'esperar' e.c.c.
 */
function consultarTurno() {
    $partidasXML = getPartidasXML ();
    foreach($partidasXML->partida as $partida)
        if($partida['id'] == "partida".$_REQUEST['id']){
            if($partida['terminada'] == "si")
                return "abandonoRival";
            if($partida['siguiente'] == $_REQUEST['ficha'])
                return $partida['ultimoMov'];   
            else
                return "esperar"; 
        }     
}
