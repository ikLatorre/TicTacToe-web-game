
function ponerFicha(pos, ficha) {
    var color;
    (getFicha() == ficha) ? color = "blue" : color = "red";
    document.getElementById("pos" + pos).setAttribute("style", "color:" + color);
    document.getElementById("pos" + pos).innerHTML = ficha;
    document.getElementById("div" + pos).removeAttribute("onclick");
}

function getFichaRival(){
    if (getFicha() == "O")
        return "X";
    else
        return "O";
}

function getFicha(){
    return document.getElementById("ficha").value;
}

//Se hace onLoad, preparar tablero y turno. Siempre empieza la ficha O, del jugador que la crea.)
//En el caso de ser el jugador que se une a una partida, bloquear tablero hasta que el rival haga el primer
//movimiento, o actualizarlo con el primer movimiento que ha hecho el rival.
function prepararTablero() {
    if (document.getElementById("tipo").value == "J" && getFicha() == "X") {
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
                    //Bloquear todo el tablero, a la espera del primer movimiento de la partida, del rival
                    bloquearTablero();
                    document.getElementById("resultado").innerHTML = "Esperando movimiento del rival...";
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
                    document.getElementById("resultado").innerHTML = "Realice el siguiente movimiento";
                }
            }
        };
        XHRObject.send('');
    } else {
        //Es contra la maquina o contra un jugador pero se ha creado la partida
        document.getElementById("resultado").innerHTML = "Realice el siguiente movimiento";
    }
}

function hacerMovimiento(posicion) {
    var XHRObject;
    if (XMLHttpRequest)
        XHRObject = new XMLHttpRequest();
    else
        XHRObject = new ActiveXObject("Microsoft.XMLHTTP");
    
    ponerFicha(posicion, getFicha());
    bloquearTablero();
    var url;
    if (document.getElementById("tipo").value == "J") {
        // PARTIDA CONTRA JUGADOR
        document.getElementById("resultado").innerHTML = "Esperando movimiento del rival...";
        url = "movimientoJugador.php?pos=" + posicion
                + "&id=" + document.getElementById("idPartida").value
                + "&ficha=" + getFicha();
        XHRObject.open("GET", url, "true");
        XHRObject.onreadystatechange = function () {  calcularResultadoJugada(XHRObject); };
    } else {
        // PARTIDA CONTRA MAQUINA
        document.getElementById("resultado").innerHTML = "Calculando movimiento...";
        url = "movimientoMaquina.php?pos=" + posicion
                + "&dificultad=" + document.getElementById("tipo").value
                + "&id=" + document.getElementById("idPartida").value;
        XHRObject.open("GET", url, "true");
        XHRObject.onreadystatechange = function () {  calcularResultadoMaquina(XHRObject); };
    }
    XHRObject.send();
}

var intervalConsultarTurno; //Almacenar interval para detenerlo cuando no se necesite

function esperarTurno() {
    intervalConsultarTurno = setInterval(consultarTurno, 3000);
}
function consultarTurno() {
    var XHRObject;
    if (XMLHttpRequest)
        XHRObject = new XMLHttpRequest();
    else
        XHRObject = new ActiveXObject("Microsoft.XMLHTTP");
    
    var url = "consultarTurnoJugador.php?id=" + + document.getElementById("idPartida").value
             + "&ficha=" + getFicha();
    XHRObject.open('GET', url, true);
    XHRObject.onreadystatechange = function () {
        if (XHRObject.readyState == 4 && XHRObject.status == 200) {
            var resultado = XHRObject.responseText;
            if(resultado != "esperar"){
                ponerFicha(resultado.substr(-2, 2), getFichaRival());
                clearInterval(intervalConsultarTurno);
                if(resultado.substr(0, 9) == "continuar"){
                    desbloquearTablero();
                    document.getElementById("resultado").innerHTML = "Realice el siguiente movimiento";
                }else{
                    //Partida finalizada por la jugada que ha hecho el jugador rival
                    mostrarPartidaFinalizada();
                    if(resultado == "empate")
                        alert("Se ha empatado la partida.");
                    else
                        alert("Has perdido la partida.");
                }
            }
        }
    };
    XHRObject.send('');
}

function calcularResultadoJugada(XHRObject){
    if (XHRObject.readyState == 4 && XHRObject.status == 200) {
        var resultado = XHRObject.responseText;
        if(resultado != "continuar"){
            //Partida finalizada por la jugada que ha hecho el jugador
            mostrarPartidaFinalizada();
            if(resultado == "empate")
                alert("Se ha empatado la partida.");
            else
                alert("ENHORABUENA! Has ganado la partida.");
        }else
            esperarTurno(); 
    }
}

function calcularResultadoMaquina(XHRObject){
    if (XHRObject.readyState == 4 && XHRObject.status == 200) {
        var resultado = XHRObject.responseText;
        var fichaRival = getFichaRival();
        if (resultado.length == 2) { //Se sigue la partida
            ponerFicha(resultado, fichaRival);
            desbloquearTablero();
            document.getElementById("resultado").innerHTML = "Realice el siguiente movimiento";
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
            mostrarPartidaFinalizada();
        }
    }
}

function mostrarPartidaFinalizada(){
    document.getElementById("resultado").innerHTML = "Partida finalizada";
    document.getElementById("resultado2").innerHTML = "<input type='button' value='Volver a jugar' onclick='reiniciarPartida()'/>";
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
    location.href = "index.xhtml";
}
