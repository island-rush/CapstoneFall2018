//Javascript Functions used by the Island Rush Game
//Created by C1C Spencer Adolph (7/28/2018)

function showIsland(event, callingElement) {
    event.preventDefault();
    document.getElementsByClassName(callingElement.id)[0].style.display = "block";
    event.stopPropagation();
}




