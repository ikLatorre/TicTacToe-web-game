<?php
require_once("nuevaPartida.php");
$tipo_ficha_id = calcularIdPartida();
?>

<?xml version="1.0" encoding="UTF-8"?>
<!--  
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Juego 3 en raya</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/estilo.css" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Shadows+Into+Light' rel='stylesheet' type='text/css'/>
        <script type="text/javascript" src="javascript/tablero.js" ></script>
    </head>

    <body onload="prepararTablero()">
        <header id="header">        
            <h1>3 en raya</h1>
        </header>
        <div id="resultado" style="text-align: center;"></div> <br />
        <div id="resultado2" style="text-align: center;"></div> <br />
        <table class="tablero">
            <tr>
                <td id="div00" onclick="hacerMovimiento('00')">
                    <span id="pos00"></span>
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

        <div><form>
                <input type="hidden" id="ficha" name="ficha" value="<?php echo(substr($tipo_ficha_id, 1, 1)); ?>" /> <!-- [O|X] -->
                <input type="hidden" id="idPartida" name="idPartida" value="<?php echo(substr($tipo_ficha_id, 2)); ?>" /> <!-- id -->
                <input type="hidden" id="tipo" name="tipo" value="<?php echo(substr($tipo_ficha_id, 0, 1)); ?>" /> <!-- [F|D|J] -->
                <?php //echo($tipo_ficha_id); ?>
            </form></div>
    </body>
</html>
