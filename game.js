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

function clickGameBoard(event, callingElement) {
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


function pieceDragstart(event, callingElement) {
    //canMove is dictated by phase and current Team
    if (canMove === "true" && event.target.getAttribute("data-placementTeamId") === myTeam) {
        //From the container (parent of the piece)
        event.dataTransfer.setData("positionId", event.target.parentNode.getAttribute("data-positionId"));
        //From the Piece
        event.dataTransfer.setData("placementId", event.target.getAttribute("data-placementId"));
        event.dataTransfer.setData("placementContainerId", event.target.getAttribute("data-placementContainerId"));
        event.dataTransfer.setData("placementCurrentMoves", event.target.getAttribute("data-placementCurrentMoves"));
        event.dataTransfer.setData("placementTeamId", event.target.getAttribute("data-placementTeamId"));
        event.dataTransfer.setData("unitTerrain", event.target.getAttribute("data-unitTerrain"));
        event.dataTransfer.setData("unitName", event.target.getAttribute("data-unitName"));
        event.dataTransfer.setData("unitId", event.target.getAttribute("data-unitId"));
    } else {
        event.preventDefault();  // This stops the drag
    }
}


function pieceDrop(event, callingElement) {
    event.preventDefault();
    //Already approved to move by pieceDragstart (same team and good phase)
    let placementId = event.dataTransfer.getData("placementId");

    let positionType = event.target.getAttribute("data-positionType");
    let unitTerrain = event.dataTransfer.getData("unitTerrain");

    let new_positionId = event.target.getAttribute("data-positionId");
    let old_positionId = event.dataTransfer.getData("positionId");

    let old_placementContainerId = event.dataTransfer.getData("placementContainerId");
    let new_placementContainerId = event.target.getAttribute("data-positionContainerId");

    let placementCurrentMoves = event.dataTransfer.getData("placementCurrentMoves");

    //Check good terrain (another function)
    //Check within # of moves (dist matrix)
    //Check spot not full?

    event.stopPropagation();
}


function pieceDragover(event, callingElement) {
    event.preventDefault();
    //Stops from Dropping into another piece (non-container element) (containers should not be draggable, only parent pieces)
    if (event.target.getAttribute("draggable") === "true") {
        event.dataTransfer.dropEffect = "none";
    } else {
        event.dataTransfer.dropEffect = "all";
    }
}





// function olddrop (event, element) {
//     event.preventDefault();
//     if (canMove === "true") {
//             var pieceTeam = event.dataTransfer.getData("team");
//             if (pieceTeam === myTeam) {
//                 var groundtype = event.target.getAttribute("data-groundtype");
//                 var unitTerrain = event.dataTransfer.getData("unitTerrain");
//                 var placementId = event.dataTransfer.getData("placementId");  // the id of piece that was dropped
//                 var newPos = event.target.getAttribute("data-positionId");  // the position of where to drop it
//                 var oldPos = event.dataTransfer.getData("positionId");  // the old position of where it was picked up
//                 var moves = event.dataTransfer.getData("moves");  // the number of moves before it was picked up
//                 var oldcontainer = event.dataTransfer.getData("oldcontainer");  // the containerId it was in before (999999 if not in one)
//                 var newcontainer = 999999;  // the containerId it is going into (dropping into) (999999 if not dropping in one)
//                 var xmlhttp = new XMLHttpRequest();
//                 xmlhttp.onreadystatechange = function () {
//                     if (this.readyState === 4 && this.status === 200) {
//                         var answer = this.responseText;
//                         if (answer !== "false") {  // false gets echo'd if not valid (from moves + other stuff) TODO: add more checks in php for random rules (transport on water only...other stuff not on water?)
//                             var xmlhttp2 = new XMLHttpRequest();
//                             xmlhttp2.open("POST", "update_position.php?placementId=" + placementId + "&newPos=" + newPos + "&oldPos=" + oldPos + "&newmoves=" + answer + "&oldcontainer=" + oldcontainer + "&newcontainer=" + newcontainer, true);
//                             xmlhttp2.send();
//                             element.appendChild(document.querySelector("[data-placementId='" + placementId + "']"));  // add the piece to the html of where it is going
//                             document.querySelector("[data-placementId='" + placementId + "']").setAttribute("data-moves", answer);  // change the moves inside the html to be updated
//                             var gamepiece = document.querySelector("[data-placementId='" + placementId + "']");  // piece for what was just moved
//                             var unitName = gamepiece.getAttribute("data-unitName");
//                             if (unitName === "transport" || unitName === "aircraftCarrier" || unitName === "lav") {  // if a transport was moved, set the new position inside the container (used elsewhere)
//                                 gamepiece.firstChild.setAttribute("data-positionId", element.getAttribute("data-positionId"));
//                             }
//                         }
//                     }
//                 };
//                 xmlhttp.open("POST", "checkvalid.php?newPos=" + newPos + "&oldPos=" + oldPos + "&moves=" + moves, true);
//                 xmlhttp.send();
//             }
//         }
//
// }