<?php
if (!isset($_REQUEST['vs'])) {
    header("Location: index.xhtml");
}
require_once("nuevaPartida.php");
//Calcular datos de la nueva partida para almacenarlos en campos 'hidden' (al final de la pagina)
$datosInicializacion = calcularIdPartida();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Juego 3 en raya</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/estilo.css" type="text/css" />
        <link rel="stylesheet" href="css/tablero.css" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Shadows+Into+Light' rel='stylesheet' type='text/css'/>
        <link href='http://fonts.googleapis.com/css?family=Fredoka+One' rel='stylesheet' type='text/css'/>
        <script type="text/javascript" src="javascript/tablero.js" ></script>
        <script type="text/javascript">
            window.onbeforeunload = function () {  //Antes de cerrar la ventana. Aún se puede cancelar la operación.
                if ((document.getElementById("partidaFinalizada")).value == "no") {
                    return "Hay una partida en curso.";
                }
            }; 
            window.onunload = function () { //Al cerrar la ventana.
                if ((document.getElementById("partidaFinalizada")).value == "no") {
                    enviarPeticionFinPartida(true);
                }
            };
        </script>
    </head>

    <body onload="prepararTablero()" >
        <header id="header">        
            <h1><a href="index.xhtml">3 en raya</a></h1>
        </header>
        <table class="tablero">
            <tr>
                <td id="div00" onclick="hacerMovimiento('00')">
                    <span id="pos00" ></span>
                </td>
                <td id="div01" onclick="hacerMovimiento('01')">
                    <span id="pos01" ></span>
                </td>
                <td id="div02" onclick="hacerMovimiento('02')">
                    <span id="pos02"></span>
                </td>
            </tr>
            <tr>
                <td id="div10" onclick="hacerMovimiento('10')">
                    <span id="pos10"></span>
                </td>
                <td id="div11" onclick="hacerMovimiento('11')">
                    <span id="pos11"></span>
                </td>
                <td id="div12" onclick="hacerMovimiento('12')">
                    <span id="pos12"></span>
                </td>
            </tr>
            <tr>
                <td id="div20" onclick="hacerMovimiento('20')">
                    <span id="pos20"></span>
                </td>
                <td id="div21" onclick="hacerMovimiento('21')">
                    <span id="pos21"></span>
                </td>
                <td id="div22" onclick="hacerMovimiento('22')">
                    <span id="pos22"></span>
                </td>
            </tr>
        </table>

        <div id="resultado" class="menu2"></div>
        <div id="resultado2" class="menu2"></div>

        <input type="hidden" id="ficha" name="ficha" value="<?php echo($datosInicializacion['ficha']); ?>" /> <!-- [O|X] -->
        <input type="hidden" id="idPartida" name="idPartida" value="<?php echo($datosInicializacion['idPartida']); ?>" /> <!-- id -->
        <input type="hidden" id="tipo" name="tipo" value="<?php echo($datosInicializacion['tipo']); ?>" /> <!-- [F|D|J] -->
        <input type="hidden" id="partidaFinalizada" name="partidaFinalizada" value="no" /> <!-- [si|no] -->

        <form id="datos_partida" method="post">
            <input type="hidden" id="vs" name="vs" value="<?php echo $_REQUEST['vs']; ?>" /> <!-- [maquina|jugador] -->
            <input type="hidden" id="dificultad" name="dificultad" value="<?php echo $_REQUEST['dificultad']; ?>" /> <!-- [facil|dificil] -->
        </form>
    </body>
</html>
