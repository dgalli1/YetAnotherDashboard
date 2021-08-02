/*Toggle dropdown list*/
/*https://gist.github.com/slavapas/593e8e50cf4cc16ac972afcbad4f70c8*/
var navMenuDiv = document.getElementById("nav-content");
var navMenu = document.getElementById("nav-toggle");
document.onclick = close;
navMenu.onclick = check;

function close() {
    if (!navMenuDiv.classList.contains('hidden')) {
        navMenuDiv.classList.toggle("hidden");
    }    
}
function check(event) {
    event.stopPropagation()
    navMenuDiv.classList.toggle("hidden");
}