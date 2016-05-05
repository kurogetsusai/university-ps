function prepare() {
	var buttons = document.getElementsByClassName("main-menu-button");
	for(var i = 0; i < buttons.length; i++) {
		buttons[i].onmouseover = buttonOn;
		buttons[i].onmouseout = buttonOff;
	}
}

function buttonOn() {
	this.style.backgroundColor = "#530053";
}

function buttonOff() {
	this.style.backgroundColor = "#000000";
}
prepare();