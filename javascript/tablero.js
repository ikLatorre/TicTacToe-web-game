/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function hacerMovimiento(posicion){
    document.getElementById("pos" + posicion).innerHTML = "O";
    document.getElementById("div" + posicion).removeAttribute("onclick");
    bloquearTablero();
    document.getElementById("resultado").innerHTML = "Calculando resultado...";
    
    var XHRObject = new XMLHttpRequest();
    XHRObject.open("GET", "movimiento.php?pos="+posicion, "true");
	XHRObject.send();
	XHRObject.onreadystatechange = function(){
            if (XHRObject.readyState == 4){
                var resultado = XHRObject.responseText;
                if(resultado.length == 2){ //Se sigue la partida
                    document.getElementById("pos" + resultado).innerHTML = "X";
                    document.getElementById("div" + resultado).removeAttribute("onclick");
                    desbloquearTablero();
                    document.getElementById("resultado").innerHTML = "";
                }else{ //Se termina la partida
                    if(resultado.substr(0, 1) == "M"){ //Mostrar ultimo movimiento de la maquina
                         document.getElementById("pos" + resultado.substr(-2, 2)).innerHTML = "X";
                    }
                    if(resultado.substr(1, 6) == "empate") alert("¡Se ha empatado la partida!");
                    else if(resultado.substr(5,1) == "O") alert("¡ENHORABUENA! Has ganado la partida.");
                    else if(resultado.substr(5,1) == "X") alert("¡Has perdido la partida!");
                    document.getElementById("resultado").innerHTML = "Partida finalizada.";
                }  
            }
	}
}

function bloquearTablero(){
    cambiarCasilla("00", null);
    cambiarCasilla("01", null);
    cambiarCasilla("02", null);
    cambiarCasilla("10", null);
    cambiarCasilla("11", null);
    cambiarCasilla("12", null);
    cambiarCasilla("20", null);
    cambiarCasilla("21", null);
    cambiarCasilla("22", null);
}

function desbloquearTablero(){
    cambiarCasilla("00", "hacerMovimiento('00')");
    cambiarCasilla("01", "hacerMovimiento('01')");
    cambiarCasilla("02", "hacerMovimiento('02')");
    cambiarCasilla("10", "hacerMovimiento('10')");
    cambiarCasilla("11", "hacerMovimiento('11')");
    cambiarCasilla("12", "hacerMovimiento('12')");
    cambiarCasilla("20", "hacerMovimiento('20')");
    cambiarCasilla("21", "hacerMovimiento('21')");
    cambiarCasilla("22", "hacerMovimiento('22')");
}

function cambiarCasilla(XX, onclickValue){
    var obj = document.getElementById("div" + XX);
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", onclickValue);
}