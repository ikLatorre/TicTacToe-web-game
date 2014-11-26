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
                document.getElementById("pos" + XHRObject.responseText).innerHTML = "X";
                document.getElementById("div" + XHRObject.responseText).removeAttribute("onclick");
                desbloquearTablero();
                document.getElementById("resultado").innerHTML = "";
            }
	}
}

function bloquearTablero(){
    var obj;
    obj = document.getElementById("div00");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);

    
    obj = document.getElementById("div01");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
    
    obj = document.getElementById("div02");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
    
    obj = document.getElementById("div10");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
    
    obj = document.getElementById("div11");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
    
    obj = document.getElementById("div12");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
    
    obj = document.getElementById("div20");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
    
    obj = document.getElementById("div21");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
    
    obj = document.getElementById("div22");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
    
    obj = document.getElementById("div00");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", null);
}

function desbloquearTablero(){
    var obj;
    
    obj = document.getElementById("div00");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('00'));
    
    obj = document.getElementById("div01");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('01'));
    
    obj = document.getElementById("div02");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('02'));
    
    obj = document.getElementById("div10");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('10'));
    
    obj = document.getElementById("div11");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('11'));
    
    obj = document.getElementById("div12");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('12'));
    
    obj = document.getElementById("div20");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('20'));
    
    obj = document.getElementById("div21");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('21'));
    
    obj = document.getElementById("div22");
    if(obj.hasAttribute("onclick"))
        obj.setAttribute("onclick", hacerMovimiento('22'));
}