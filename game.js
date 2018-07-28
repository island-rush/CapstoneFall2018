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


function pieceDrop(event, newContainerElement) {
    event.preventDefault();
    //Already approved to move by pieceDragstart (same team and good phase)
    let placementId = event.dataTransfer.getData("placementId");
    let unitName = event.dataTransfer.getData("unitName");
    let unitId = event.dataTransfer.getData("unitId");

    let pieceDropped = document.querySelector("[data-placementId='" + placementId + "']");

    let positionType = event.target.getAttribute("data-positionType");
    let unitTerrain = event.dataTransfer.getData("unitTerrain");

    let new_positionId = event.target.getAttribute("data-positionId");
    let old_positionId = event.dataTransfer.getData("positionId");

    let old_placementContainerId = event.dataTransfer.getData("placementContainerId");
    let new_placementContainerId = event.target.getAttribute("data-positionContainerId");

    let old_placementCurrentMoves = event.dataTransfer.getData("placementCurrentMoves");

    if (movementTerrainCheck(unitTerrain, positionType) === "true") {
        let movementCost = movementWithinMoves(unitName, old_positionId, new_positionId, old_placementCurrentMoves);
        if (movementCost !== -1) {
            if ((new_placementContainerId !== 999999 && containerHasSpotOpen(new_placementContainerId, unitName) === "true") || new_placementContainerId === 999999) {

                //MANY OTHER CHECKS FOR MOVEMENT CAN HAPPEN HERE, JUST NEST MORE FUNCTIONS (see above)

                let new_placementCurrentMoves = old_placementCurrentMoves - movementCost;

                //Update the placement in the database and add a movement to the database
                let phpRequest = new XMLHttpRequest();
                phpRequest.open("POST", "pieceMove.php?gameId=" + gameId + "&gameTurn=" + gameTurn + "&gamePhase=" + gamePhase + "&placementId=" + placementId + "&unitName=" + unitName + "&new_positionId=" + new_positionId + "&old_positionId=" + old_positionId + "&movementCost=" + movementCost  + "&new_placementCurrentMoves=" + new_placementCurrentMoves + "&old_placementContainerId=" + old_placementContainerId + "&new_placementContainerId=" + new_placementContainerId, true);
                phpRequest.send();

                //Update the html by moving the piece and changing the piece's attributes
                newContainerElement.appendChild(pieceDropped);
                pieceDropped.setAttribute("data-placementCurrentMoves", new_placementCurrentMoves.toString());
                pieceDropped.setAttribute("data-placementContainerId", new_placementContainerId);
                if (unitName === "transport" || unitName === "aircraftCarrier" || unitName === "lav") {
                    pieceDropped.firstChild.setAttribute("data-positionId", newContainerElement.getAttribute("data-positionId"));
                }
            }
        }
    }

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


function movementTerrainCheck(unitTerrain, positionType) {
    return "true";
}


function movementWithinMoves(unitName, old_positionId, new_positionId, placementCurrentMoves) {
    //Return -1 for not within usable moves
    return 5;
}


function containerHasSpotOpen(new_placementContainerId, unitName) {
    return "true";
}





