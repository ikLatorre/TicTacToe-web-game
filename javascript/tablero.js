/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function hacerMovimiento(posicion){
    document.getElementById("p" + posicion).innerHTML = "X";
    
    var XHRObject = new XMLHttpRequest();
    XHRObject.open("GET", "movimiento.php?pos="+posicion, "true");
	//XHRObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//XHRObject.setRequestHeader("Content-length", params.length);
	XHRObject.send();
	
	XHRObject.onreadystatechange = function()
	{
            if (XHRObject.readyState == 4)
		{
                    var obj = document.getElementById("p" + XHRObject.responseText);
                    obj.innerHTML = "X";
		}
	}
}
