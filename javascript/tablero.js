
//Poner la ficha [O|X] dada en la posicion 'XX' del tablero 3x3
function ponerFicha(pos, ficha) {
    var color;
    (getFicha() == ficha) ? color = "blue" : color = "red";
    document.getElementById("pos" + pos).setAttribute("style", "color:" + color);
    document.getElementById("pos" + pos).innerHTML = ficha;
    document.getElementById("div" + pos).removeAttribute("onclick");
}

function getFichaRival() {
    if (getFicha() == "O")
        return "X";
    else
        return "O";
}

function getFicha() {
    return document.getElementById("ficha").value;
}

//Se hace onLoad, preparar tablero y turno. Siempre empieza la ficha O, del jugador que la crea.
//En el caso de ser el jugador que se une a una partida, bloquear tablero hasta que el rival haga el primer
//movimiento, o actualizarlo con el primer movimiento que ha hecho el rival.
function prepararTablero() {
    document.getElementById("resultado2").innerHTML = "<input type='button' value='Abandonar partida' onclick='preguntarSalida()'/>";
    
    if (document.getElementById("tipo").value == "J" && getFicha() == "X") {
        var XHRObject = getXHRObject();
        XHRObject.open('GET', 'estadoInicial.php?id=' + document.getElementById("idPartida").value, true);
        XHRObject.onreadystatechange = function () {
            if (XHRObject.readyState == 4 && XHRObject.status == 200) {
                var partidaXML = XHRObject.responseXML;
                
                //Establecer el estado del tablero y turno correctos
                if (partidaXML.getElementsByTagName("partida")[0].getAttribute("siguiente") == "O") {
                    //Bloquear todo el tablero, a la espera del primer movimiento de la partida, del rival
                    bloquearTablero();
                    document.getElementById("resultado").innerHTML = "<span>Esperando movimiento del rival...</span><img id=\"loading\" src=\"images/loading.gif\" alt=\"gif_cargando\"/>";
                    esperarTurno();
                } else {
                    //Actualizar estado del tablero y dejar desbloqueadas las casillas correspondientes
                    var casillas = partidaXML.getElementsByTagName("casilla");
                    for (i = 0; i < casillas.length; i++)
                        if (casillas[i].childNodes.length > 0) { //Si tiene una ficha
                            var ficha = casillas[i].childNodes[0];
                            if (ficha.childNodes[0].nodeValue == "O") {
                                var pos = casillas[i].getAttribute('id');
                                ponerFicha(pos, "O");
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

function hacerMovimiento(posicion) {
    var XHRObject = getXHRObject();

    ponerFicha(posicion, getFicha());
    bloquearTablero();
    var url;
    if (document.getElementById("tipo").value == "J") {
        // PARTIDA CONTRA JUGADOR
        document.getElementById("resultado").innerHTML = "<span>Esperando movimiento del rival...</span><img id=\"loading\" src=\"images/loading.gif\" alt=\"gif_cargando\"/>";
        url = "movimientoJugador.php?pos=" + posicion
                + "&id=" + document.getElementById("idPartida").value
                + "&ficha=" + getFicha();
        XHRObject.open("GET", url, "true");
        XHRObject.onreadystatechange = function () {
            calcularResultadoJugada(XHRObject);
        };
    } else {
        // PARTIDA CONTRA MAQUINA
        document.getElementById("resultado").innerHTML = "<span>Calculando movimiento...</span><img id=\"loading\" src=\"images/loading.gif\" alt=\"gif_cargando\"/>";
        url = "movimientoMaquina.php?pos=" + posicion
                + "&dificultad=" + document.getElementById("tipo").value
                + "&id=" + document.getElementById("idPartida").value;
        XHRObject.open("GET", url, "true");
        XHRObject.onreadystatechange = function () {
            calcularResultadoMaquina(XHRObject);
        };
    }
    XHRObject.send();
}

/*
 * Se ejecuta al hacer un movimiento en una partida jugador vs maquina,
 * y evalúa el resultado AJAX de haber realizado dicho movimiento.
 */
function calcularResultadoMaquina(XHRObject) {
    if (XHRObject.readyState == 4 && XHRObject.status == 200) {
        var resultado = JSON.parse(XHRObject.responseText);
        var fichaRival = getFichaRival();
        if (resultado['estado'] == "continuar") { //Se sigue la partida
            ponerFicha(resultado['jugada'], fichaRival);
            desbloquearTablero();
            document.getElementById("resultado").innerHTML = "<span>Realice el siguiente movimiento</span>";
        } else { //Se termina la partida
            if (resultado['jugador'] == "R") { //Mostrar ultimo movimiento del rival
                ponerFicha(resultado['jugada'], fichaRival);
            }
            if (resultado['estado'] == "empate")
                alert("Se ha empatado la partida.");
            else if (resultado['estado'] != "gana" + fichaRival)
                alert("ENHORABUENA! Has ganado la partida.");
            else if (resultado['estado'] == "gana" + fichaRival)
                alert("Has perdido la partida.");
            mostrarPartidaFinalizada();
        }
    }
}

/*
 * Se ejecuta al hacer un movimiento en una partida jugador vs jugador,
 * y evalúa el resultado AJAX de haber realizado dicho movimiento.
 */
function calcularResultadoJugada(XHRObject) {
    if (XHRObject.readyState == 4 && XHRObject.status == 200) {
        var resultado = XHRObject.responseText;
        if (resultado != "continuar") {
            //Partida finalizada por la jugada que ha hecho el jugador
            mostrarPartidaFinalizada();
            if (resultado == "empate")
                alert("Se ha empatado la partida.");
            else
                alert("ENHORABUENA! Has ganado la partida.");
        } else
            esperarTurno();
    }
}

var intervalConsultarTurno; //Almacenar interval para detenerlo cuando no se necesite

function esperarTurno() {
    intervalConsultarTurno = setInterval(consultarTurno, 3000);
}

/*
 * Consultar el turno de la partida, o mostrar el mensaje correspondiente
 * si la partida ha finalizado por el movimiento del rival.
 */
function consultarTurno() {
    var XHRObject = getXHRObject();
    var url = "consultarTurnoJugador.php?id=" + document.getElementById("idPartida").value
            + "&ficha=" + getFicha();
    XHRObject.open('GET', url, true);
    XHRObject.onreadystatechange = function () {
        if (XHRObject.readyState == 4 && XHRObject.status == 200) {
            var resultado = JSON.parse(XHRObject.responseText);
            if(resultado['jugada'] == "abandonoRival"){
                mostrarPartidaFinalizada();  
                clearInterval(intervalConsultarTurno);
                alert("ENHORABUENA! Has ganado la partida por abandono del rival.");
            }else if (resultado['jugada'] != "esperar") {
                ponerFicha(resultado['jugada'], getFichaRival());
                clearInterval(intervalConsultarTurno);
                if (resultado['estado'] == "continuar") {
                    desbloquearTablero();
                    document.getElementById("resultado").innerHTML = "<span>Realice el siguiente movimiento</span>";
                } else {
                    //Partida finalizada por la jugada que ha hecho el jugador rival
                    mostrarPartidaFinalizada();
                    if (resultado['estado'] == "empate")
                        alert("Se ha empatado la partida.");
                    else
                        alert("Has perdido la partida.");
                }
            }
        }
    };
    XHRObject.send('');
}

/*
 * Bloquear casillas restantes de la partida 
 * (anular evento onClick de los div's asociados a ellas).
 */ 
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

/*
 * Desbloquear casillas restantes de la partida 
 * (reestablecer evento onClick de los div's asociados a ellas).
 */ 
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

/* Asignar el evento 'onclickValue' dado al atributo onClick de la casilla
 * especificada con la posición, si existiera. Si no existe dicho atributo significa
 * que la casilla ha sido ya marcada, no siendo ya una de las restantes
 * disponibles de la partida.
 */
function cambiarCasilla(pos, onclickValue) {
    var obj = document.getElementById("div" + pos);
    if (obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", onclickValue);
}

// Reiniciar el tipo de partida jugado (facil, dificil o vs jugador).
function reiniciarPartida() {
    document.getElementById("datos_partida").submit();
}

function volver() {
    location.href = "index.xhtml";
}

function mostrarPartidaFinalizada() {
    document.getElementById("resultado").innerHTML = "<span>Partida finalizada</span>";
    document.getElementById("resultado2").innerHTML = "<input type='button' value='Volver a jugar' onclick='reiniciarPartida()'/><input type='button' value='Volver al men&uacute;' onclick='volver()'/>";
    document.getElementById("partidaFinalizada").value = "si";
}

function getXHRObject(){
    var XHRObject;
    if (XMLHttpRequest)
        XHRObject = new XMLHttpRequest();
    else
        XHRObject = new ActiveXObject("Microsoft.XMLHTTP");
    return XHRObject;
}

function preguntarSalida(){    
   if (confirm("Est\u00e1 seguro de abandonar la partida?")){
       bloquearTablero();
       clearInterval(intervalConsultarTurno);
       finalizarPartida();
       mostrarPartidaFinalizada();
       alert("Has perdido la partida.");
   }
 }
 
 /*
  * Cambiar estado de la partida como terminada en el XML.
  * Este metodo es llamado por 'preguntarSalida()' al darle al botón 'Abandonar partida'
  */
 function finalizarPartida(){
    var partidaFinalizada = document.getElementById("partidaFinalizada");
    if (partidaFinalizada.value == "no") {
        partidaFinalizada.value = "si";
        var XHRObject = getXHRObject();
        var url = "finPartida.php?id=" + document.getElementById("idPartida").value;
        XHRObject.open('GET', url, true);
        XHRObject.send();
    }
    return;
 }