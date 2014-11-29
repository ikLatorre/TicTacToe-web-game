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
        <script type="text/javascript" src="javascript/tablero.js" ></script>
    </head>
    
    <body onload="prepararTablero()">
        <h1 style="text-align: center;">3 en raya</h1>
        <div id="resultado" style="text-align: center;"></div> <br />
        <div id="resultado2" style="text-align: center;"></div> <br />
         <table border="1">
            <tr>
                <td id="div00" onclick="hacerMovimiento('00')">
                    <div id="pos00" class="ficha"></div>
                </td>
                <td id="div01" onclick="hacerMovimiento('01')">
                    <div id="pos01" class="ficha"></div>
                </td>
                 <td id="div02" onclick="hacerMovimiento('02')">
                    <div id="pos02" class="ficha"></div>
                </td>
            </tr>
            <tr>
                <td id="div10" onclick="hacerMovimiento('10')">
                    <div id="pos10" class="ficha"></div>
                </td>
                <td id="div11" onclick="hacerMovimiento('11')">
                    <div id="pos11" class="ficha"></div>
                </td>
                 <td id="div12" onclick="hacerMovimiento('12')">
                    <div id="pos12" class="ficha"></div>
                </td>
            </tr>
            <tr>
                <td id="div20" onclick="hacerMovimiento('20')">
                    <div id="pos20" class="ficha"></div>
                </td>
                <td id="div21" onclick="hacerMovimiento('21')">
                    <div id="pos21" class="ficha"></div>
                </td>
                 <td id="div22" onclick="hacerMovimiento('22')">
                    <div id="pos22" class="ficha"></div>
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
