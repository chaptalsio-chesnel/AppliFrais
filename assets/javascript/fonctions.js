	var total = 0;
	var a = 0;
	var b = 0;
	var c = 0;
	var d = 0;

	function start() {



	    var x = document.getElementById("montantETP");
	    var m = document.getElementById("ETP");
	    var montant = x.innerText || x.textContent;
	    var qte = m.value;
	    montant = parseFloat(montant);
	    var t = montant * qte;
	    document.getElementById("totalETP").innerHTML = t;
	    a = t;

	    var x = document.getElementById("montantKM");
	    var m = document.getElementById("KM");
	    var montant = x.innerText || x.textContent;
	    var qte = m.value;
	    montant = parseFloat(montant);
	    var t = montant * qte;
	    document.getElementById("totalKM").innerHTML = t;
	    b = t;

	    var x = document.getElementById("montantREP");
	    var m = document.getElementById("REP");
	    var montant = x.innerText || x.textContent;
	    var qte = m.value;
	    montant = parseFloat(montant);
	    var t = montant * qte;
	    document.getElementById("totalREP").innerHTML = t;
	    c = t;

	    var x = document.getElementById("montantNUI");
	    var m = document.getElementById("NUI");
	    var montant = x.innerText || x.textContent;
	    var qte = m.value;
	    montant = parseFloat(montant);
	    var t = montant * qte;
	    document.getElementById("totalNUI").innerHTML = t;
	    d = t;

	    total = a + b + c + d;
	    document.getElementById("total").innerHTML = total;
	}

	function montant(yolo) {

	    var id = yolo.id

	    var x = document.getElementById("montant" + id);
	    var m = document.getElementById(id);
	    var montant = x.innerText || x.textContent;
	    var qte = m.value;
	  
	    montant = parseFloat(montant);
	    document.getElementById("total" + id).innerHTML = montant * qte;
	    var tt = document.getElementById("total" + id);
	    var tot = tt.innerText || tt.textContent;
	    tot = parseFloat(tot);
	    if (id == "ETP") {
	        a = tot;
	    } else if (id == "KM") {
	        b = tot;
	    } else if (id == "NUI") {
	        c = tot;
	    } else {
	        d = tot;
	    }

	    total = a + b + c + d;
	    document.getElementById("total").innerHTML = total;


	}

	function valider() {
	    var result;
	    result = false;
	    var t = document.getElementById("ETP").value;
	    var r = document.getElementById("KM").value;
	    var y = document.getElementById("REP").value;
	    var u = document.getElementById("NUI").value;
	    if (t >= 0 && r >= 0 && y >= 0 && u >= 0) {
	        result = true;


	    }

	    return result;
	}
	function maxL(element, max){
	    value = element.value;
	    max = parseInt(max);
	    if(value.length > max){
	        element.value = value.substr(0, max);
	    }
	}