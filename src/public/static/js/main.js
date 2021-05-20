
var darkMode = false;
var lightButton = document.getElementById("lightButton");

if(document.cookie.includes("darkMode=true")){
    
    darkMode = false;
    toggleLights();
}

function toggleLights(){

    lightButton.innerHTML = darkMode ? "dark mode" : "light mode"; 
    darkMode = !darkMode;
    document.cookie = "darkMode=" + darkMode + "; expires=Thu, 02 May 2031 12:00:00 UTC; Secure";
    document.body.classList.toggle("fly-dark");
}

function toggleVisibility(element){

    element.hidden = !element.hidden;
}
