/* BOOKMARK POPUP JS */

function deleteMe(id) {
	opener.deleteBookmark(id);
	self.close();
}

function adjustFontSize() {
	var s=queryString("s"); 
	switch (s) {
		case "s":
			document.body.className = "sizeSmall"; break;
		case "m":
			document.body.className = "sizeMedium"; break;
		case "l":
			document.body.className = "sizeLarge";
	}
	window.resizeTo(350,500);
}
window.onload=adjustFontSize;
