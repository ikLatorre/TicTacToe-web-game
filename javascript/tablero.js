/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Se hace onLoad (preparar tablero y turno. Siempre empieza la ficha O, siempre del jugador que creala partida)
//En el caso de ser el jugador que se une a una partida, bloquear tablero hasta que el rival haga el primer
//movimiento, o actualizarlo con el primer movimiento que ha hecho el rival.
function ponerFicha(pos, ficha) {
    var color;
    (document.getElementById("ficha").value == ficha) ? color = "blue" : color = "red";
    document.getElementById("pos" + pos).setAttribute("style", "color:" + color);
    document.getElementById("pos" + pos).innerHTML = ficha;
    document.getElementById("div" + pos).removeAttribute("onclick");
}
function prepararTablero() {
    //Realizar solo si se ha unido (y no creado) a una partida jugador vs jugador
    if (document.getElementById("tipo").value == "J" && document.getElementById("ficha").value == "X") {
        var XHRObject;
        if (XMLHttpRequest)
            XHRObject = new XMLHttpRequest();
        else
            XHRObject = new ActiveXObject("Microsoft.XMLHTTP");
        XHRObject.open('GET', 'estadoInicial.php?id=' + document.getElementById("idPartida").value, true);
        XHRObject.onreadystatechange = function () {
            if (XHRObject.readyState == 4 && XHRObject.status == 200) {
                var partidaString = XHRObject.responseText;
                var parser = new DOMParser();
                var partidaXML = parser.parseFromString(partidaString, 'text/xml');

                //Establecer el estado del tablero y turno correctos
                if (partidaXML.getElementsByTagName("partida")[0].getAttribute("siguiente") == "O") {
                    //bloquear todo el tablero, a la espera del primer movimiento de la partida, del rival
                    bloquearTablero();
                    document.getElementById("resultado").innerHTML = "<span>Esperando movimiento del rival...</span><img id=\"loading\" src=\"images/loading.gif\" alt=\"gif_cargando\"/>";
                    // Hacer consultas periodicas para saber cuando habilitar el tablero
                    esperarTurno();
                } else {
                    //actualizar estado del tablero y dejar desbloqueadas las casillas correspondientes
                    var casillas = partidaXML.getElementsByTagName("casilla");
                    for (i = 0; i < casillas.length; i++) {
                        if (casillas[i].childNodes.length > 0) {
                            var ficha = casillas[i].childNodes[0];
                            if (ficha.childNodes[0].nodeValue == "O") {
                                var pos = casillas[i].getAttribute('id');
                                ponerFicha(pos, "O");
                            }
                        }
                    }
                    document.getElementById("resultado").innerHTML = "<span>Realice el siguiente movimiento</span>";
                }
            }
        };
        XHRObject.send('');
    } else {
        //Es contra la maquina o contra un jugador pero se ha creado la partida
        document.getElementById("resultado").innerHTML = "<span>Realice el siguiente movimiento</span>";
    }
}

//Variable para guardar el interval, y poder detenerlo.
var interval;
function esperarTurno() {
    interval = setInterval(consultarEstado, 3000);
}

function consultarEstado() {
    var XHRObject;
    if (XMLHttpRequest)
        XHRObject = new XMLHttpRequest();
    else
        XHRObject = new ActiveXObject("Microsoft.XMLHTTP");
    XHRObject.open('GET', 'estadoInicial.php?id=' + document.getElementById("idPartida").value, true);
    XHRObject.onreadystatechange = function () {
        if (XHRObject.readyState == 4 && XHRObject.status == 200) {
            var partidaString = XHRObject.responseText;
            var parser = new DOMParser();
            var partidaXML = parser.parseFromString(partidaString, 'text/xml');

            if (partidaXML.getElementsByTagName("partida")[0].getAttribute("siguiente") == "X") {
                //actualizar estado del tablero y dejar desbloqueadas las casillas correspondientes
                var casillas = partidaXML.getElementsByTagName("casilla");
                for (i = 0; i < casillas.length; i++) {
                    if (casillas[i].childNodes.length > 0) {
                        var ficha = casillas[i].childNodes[0];
                        if (ficha.childNodes[0].nodeValue == "O") {
                            var pos = casillas[i].getAttribute('id');
                            ponerFicha(pos, "O");
                        }
                    }
                }
                clearInterval(interval);
                desbloquearTablero();
                document.getElementById("resultado").innerHTML = "<span>Realice el siguiente movimiento</span>";
            }
        }
    };
    XHRObject.send('');
}

function hacerMovimiento(posicion) {
    var url, mensajeEspera;
    if (document.getElementById("tipo").value == "J") {
        url = "movimientoJugador.php?pos=" + posicion
                + "&id=" + document.getElementById("idPartida").value
                + "&ficha=" + document.getElementById("ficha").value;
        mensajeEspera = "<span>Esperando movimiento del rival...</span><img id=\"loading\" src=\"images/loading.gif\" alt=\"gif_cargando\"/>";
    } else {
        url = "movimientoMaquina.php?pos=" + posicion
                + "&dificultad=" + document.getElementById("tipo").value
                + "&id=" + document.getElementById("idPartida").value;
        mensajeEspera = "<span>Calculando movimiento...</span><img id=\"loading\" src=\"images/loading.gif\" alt=\"gif_cargando\"/>";
    }
    ponerFicha(posicion, document.getElementById("ficha").value);
    bloquearTablero();
    document.getElementById("resultado").innerHTML = mensajeEspera;

    var fichaRival;
    if (document.getElementById("ficha").value == "O")
        fichaRival = "X";
    else
        fichaRival = "O";

    var XHRObject;
    if (XMLHttpRequest)
        XHRObject = new XMLHttpRequest();
    else
        XHRObject = new ActiveXObject("Microsoft.XMLHTTP");
    XHRObject.open("GET", url, "true");
    XHRObject.onreadystatechange = function () {
        if (XHRObject.readyState == 4) {
            var resultado = XHRObject.responseText;
            if (resultado.length == 2) { //Se sigue la partida
                ponerFicha(resultado, fichaRival);
                desbloquearTablero();
                document.getElementById("resultado").innerHTML = "<span>Realice el siguiente movimiento</span>";
            } else { //Se termina la partida
                if (resultado.substr(0, 1) == "R") { //Mostrar ultimo movimiento del rival
                    ponerFicha(resultado.substr(-2, 2), fichaRival);
                }
                if (resultado.substr(1, 6) == "empate")
                    alert("Se ha empatado la partida.");
                else if (resultado.substr(5, 1) != fichaRival)
                    alert("ENHORABUENA! Has ganado la partida.");
                else if (resultado.substr(5, 1) == fichaRival)
                    alert("Has perdido la partida.");
                document.getElementById("resultado").innerHTML = "<span>Partida finalizada</span>";
                document.getElementById("resultado2").innerHTML = "<input type='button' value='Volver a jugar' onclick='reiniciarPartida()'/><input type='button' value='Volver al men&uacute;' onclick='volver()'/>";
            }
        }
    };
    XHRObject.send();
}

function bloquearTablero() {
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

function desbloquearTablero() {
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

function cambiarCasilla(pos, onclickValue) {
    var obj = document.getElementById("div" + pos);
    if (obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", onclickValue);
}

function reiniciarPartida() {
    document.getElementById("datos_partida").submit();
}

function volver() {
    location.href = "index.xhtml";
}
