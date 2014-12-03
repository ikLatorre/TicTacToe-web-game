<?php
require_once('movimientos.php');

$response = array('jugada' => (string)consultarTurno(), 'estado' => "");
if($response['jugada'] == "esperar" || $response['jugada'] == "abandonoRival")
    echo json_encode($response);
else{
    $response['estado'] = getEstado();
    echo json_encode($response); //Devuelve el estado y el ultimo movimiento realizado
}


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
