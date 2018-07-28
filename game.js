//Javascript Functions used by the Island Rush Game
//Created by C1C Spencer Adolph (7/28/2018)


function clickIsland(event, callingElement) {
    event.preventDefault();

    hideIslands();  //only 1 island visible at a time

    document.getElementsByClassName(callingElement.id)[0].style.display = "block";
    callingElement.style.zIndex = 20;  //default for a gridblock is 10

    event.stopPropagation();
}


function clickWater(event, callingElement) {
    event.preventDefault();

    hideIslands();

    event.stopPropagation();
}


function hideIslands() {
    let x = document.getElementsByClassName("bigblock3x3");
    let i;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
        x[i].parentNode.style.zIndex = 10;  //10 is the default
    }
}

