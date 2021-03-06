function toggleTestOptions(field) {
	if (field && document.getElementById('contentHide')) {
		switch (field) {
			case "999" : document.getElementById('contentHide').className = "contentShow"; break;
			case "1" : document.getElementById('contentHide').className = "contentHide"; break;
			case "2" : document.getElementById('contentHide').className = "contentShow"; break;
			case "3" : document.getElementById('contentHide').className = "contentShow"; break;
			case "4" : document.getElementById('contentHide').className = "contentShow"; break;
			case "5" : document.getElementById('contentHide').className = "contentShow"; break;
			case "6" : document.getElementById('contentHide').className = "contentShow"; break;
			case "7" : document.getElementById('contentHide').className = "contentShow"; break;
			case "8" : document.getElementById('contentHide').className = "contentShow"; break;
			case "9" : document.getElementById('contentHide').className = "contentShow"; break;
			case "10" : document.getElementById('contentHide').className = "contentShow"; break;
		}
	}
}

function toggleFeedback(field) {
	if (field && document.getElementById('toggleFeedback')) {
		switch (field) {
			case "1" : document.getElementById('toggleFeedback').className = "contentShow"; break;
			case "0" : document.getElementById('toggleFeedback').className = "contentHide"; break;
		}
	}
}

function toggleDescription(field) {
	if (field && document.getElementById('descriptionLink')) {
		switch(field) {
			case "Bank" : document.getElementById('descriptionLink').className = "contentHide"; break;
			case "Module" : document.getElementById('descriptionLink').className = "contentShow"; break;
			case "Feedback" : document.getElementById('descriptionLink').className = "contentHide"; break;
		}
	}
}

function toggleType(field) {
	if (field && document.getElementById('contentAdvanced') && document.getElementById('contentMessage')) {
		switch (field) {
			case "Custom Content" : 
				document.getElementById('contentAdvanced').className = "contentShow"; 
				document.getElementById('contentMessage').className = "contentHide";
				break;
				
			case "Login" : 
				document.getElementById('contentAdvanced').className = "contentHide"; 
				document.getElementById('contentMessage').className = "noResults contentShow";
				break;
				 
			case "Register" : 
				document.getElementById('contentAdvanced').className = "contentShow"; 
				document.getElementById('contentMessage').className = "contentHide";
				break;
		}
	}
}

function toggleInfo(container, catDivider, currentClass, swapClass) {
	if (document.getElementById(container)) {
		var containerClass = document.getElementById(container);
		
		if (containerClass.className == "contentHide") {
			containerClass.className = "contentShow";
		} else {
			containerClass.className = "contentHide";
		}
		
		if (document.getElementById(catDivider)) {
			var dividerClass = document.getElementById(catDivider);
			
			if (dividerClass.className == currentClass) {
				dividerClass.className = swapClass;
			} else {
				dividerClass.className = currentClass;
			}
		}
	}
}

function toggleDisplay(field, toggle) {
	if (field) {
		if (field == "Linear") {
			document.getElementById(toggle).className = "contentHide";
		} else {
			document.getElementById(toggle).className = "contentShow";
		}
	}
}

function rollOverTools(div) {
	var togglePrep = document.getElementById(div).className;
	var toggle = togglePrep.split(" ");
	
	if (toggle['0'] == "contentHide") {
		var hide = toggle['1'] + " " + toggle['2'];
		document.getElementById(div).className = hide;
	} else {
		var show = "contentHide " + toggle['0'] + " " + toggle['1'];
		document.getElementById(div).className = show;
	}
}

function quickEdit(field) {
	
}