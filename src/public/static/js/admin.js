
var titleInput = document.getElementById("titleInput");
removeLineBreaks(titleInput);
adjustTextarea(titleInput);

var sidebar = document.getElementById("sidebar");
var sidebarDisplayButton = document.getElementById("sidebarDisplayButton");

function toggleSidebar(){

    var hide = (sidebar.style.display != "none");
    sidebar.style.display = hide ? "none" : "flex";
    sidebarDisplayButton.innerHTML = hide ? "show sidebar" : "hide sidebar";
}

function adjustTextarea(textarea){

    textarea.style.height = "";
    textarea.style.height = textarea.scrollHeight + "px";
}

function removeLineBreaks(input){

    input.value = input.value.replace(/\n/g, '');
}

function toggleBold(element){

    element.style.fontWeight = element.style.fontWeight == "bold" ? "" : "bold";
}
