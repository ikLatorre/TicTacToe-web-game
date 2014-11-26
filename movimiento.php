<?php
function getTableroXML() {
	$fich_partida = "partida.xml";
	if (! file_exists ( $fich_partida )) {
		$tableroXML = new SimpleXMLElement ( '<?xml version="1.0" encoding="UTF-8"?>
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
function movimiento() {
	$tablero = getTablero ();
	$preferidos = array (
			array (
					1,
					1 
			),
			array (
					0,
					0 
			),
			array (
					0,
					2 
			),
			array (
					2,
					0 
			),
			array (
					2,
					2 
			),
			array (
					0,
					1 
			),
			array (
					1,
					0 
			),
			array (
					1,
					2 
			),
			array (
					2,
					1 
			) 
	);
	foreach ( $preferidos as $mov ) {
		if ($tablero [$mov [0]] [$mov [1]] == "")
			return "{$mov[0]}" . "{$mov[1]}";
	}
	return null;
	
	// if ($tablero [1] [1] == "")
	// return "11";
	// else {
	// if ($tablero [0] [0] == "")
	// return "00";
	// else if ($tablero [0] [2] == "")
	// return "02";
	// else if ($tablero [2] [0] == "")
	// return "20";
	// else if ($tablero [2] [2] == "")
	// return "22";
	// else {
	// }
	// }
}
function jugada($jugada, $jugador) {
	$tableroXML = getTableroXML ();
	foreach ( $tableroXML->casilla as $casilla ) {
		foreach ( $casilla->attributes () as $a => $b ) {
			if ($casilla->count () != 0)
				return false;
			if ($b == $jugada)
				$casilla->addChild ( "ficha", $jugador );
		}
	}
	$tableroXML->saveXML ( "partida.xml" );
	return true;
}

$jugada = $_REQUEST ['pos'];
jugada ( $jugada, "O" );
$jugada = movimiento ();
jugada ( $jugada, "X" );
echo $jugada;
?>