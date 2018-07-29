//Javascript Functions used by the Island Rush Game
//Created by C1C Spencer Adolph (7/28/2018)


function clickIsland(event, callingElement) {
    event.preventDefault();
    hideIslands();  //only 1 island visible at a time
    document.getElementsByClassName(callingElement.id)[0].style.display = "block";
    callingElement.style.zIndex = 20;  //default for a gridblock is 10
    callingElement.setAttribute("data-islandPopped", "true");
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
        x[i].parentNode.setAttribute("data-islandPopped", "false");
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


function positionDrop(event, newContainerElement) {
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


function positionDragover(event, callingElement) {
    event.preventDefault();
    //Stops from Dropping into another piece (non-container element) (containers should not be draggable, only parent pieces)
    if (event.target.getAttribute("draggable") === "true") {
        event.dataTransfer.dropEffect = "none";
    } else {
        event.dataTransfer.dropEffect = "all";
    }
}


function pieceTrash(event, trashElement) {
    event.preventDefault();
    if (canTrash === "true") {
        if (event.dataTransfer.getData("positionId") === "118") {
            let placementId = event.dataTransfer.getData("placementId");
            document.querySelector("[data-placementId='" + placementId + "']").remove();
            let phpTrashRequest = new XMLHttpRequest();
            phpTrashRequest.open("POST", "pieceTrash.php?placementId=" + placementId, true);
            phpTrashRequest.send();
        }
    }
}


function pieceMoveUndo() {
    if (canUndo === "true") {
        let phpUndoRequest = new XMLHttpRequest();
        phpUndoRequest.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let decoded = JSON.parse(this.responseText);
                if (decoded.placementId !== null) {
                    //Update the piece's attributes
                    let pieceToUndo = document.querySelector("[data-placementId='" + decoded.placementId + "']");
                    pieceToUndo.setAttribute("data-placementContainerId", decoded.new_placementContainerId);
                    pieceToUndo.setAttribute("data-placementCurrentMoves", (parseInt(pieceToUndo.getAttribute("data-placementCurrentMoves")) + decoded.movementCost));
                    //Remove from Old Position
                    if (decoded.old_placementContainerId !== 999999) {
                        document.querySelector("[data-placementId='" + decoded.old_placementContainerId + "']").firstChild.removeChild(pieceToUndo);
                    } else {
                        document.querySelector("[data-positionId='" + decoded.old_placementPositionId + "']").removeChild(pieceToUndo);
                    }
                    //Append to New Position
                    if (decoded.new_placementContainerId !== 999999) {
                        document.querySelector("[data-placementId='" + decoded.new_placementContainerId + "']").firstChild.appendChild(pieceToUndo);
                    } else {
                        document.querySelector("[data-positionId='" + decoded.new_placementPositionId + "']").appendChild(pieceToUndo);
                    }
                }
            }
        };
        phpUndoRequest.open("GET", "pieceMoveUndo.php?gameId=" + gameId + "&gameTurn=" + gameTurn + "&gamePhase=" + gamePhase, true);
        phpUndoRequest.send();
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


function piecePurchase(event, purchaseButton) {
    event.preventDefault();
    if (canPurchase === "true") {
        let unitId = purchaseButton.getAttribute("data-unitId");
        let unitName = event.target.id;
        let unitMoves = unitsMoves[unitName];
        let terrain = purchaseButton.getAttribute("data-unitTerrain");

        let phpPurchaseRequest = new XMLHttpRequest();
        phpPurchaseRequest.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let parent = document.getElementById("purchased_container");
                parent.innerHTML += this.responseText;
                // if (unitName === "transport"  || unitName === "aircraftCarrier" || unitName === "lav") {
                //     document.getElementById("purchased_container").lastChild.firstChild.style.display = "none";
                // }
            }
        };
        phpPurchaseRequest.open("GET", "piecePurchase.php?unitId=" + unitId + "&unitName=" + unitName + "&unitMoves=" + unitMoves + "&unitTerrain=" + terrain + "&placementTeamId=" + myTeam + "&gameId=" + gameId, true);
        phpPurchaseRequest.send();
    }
}


function islandDragenter(event, callingElement) {
    event.preventDefault();
    clearTimeout(hoverTimer);
    hoverTimer = setTimeout(function() { clickIsland(event, callingElement);}, 1000);
    event.stopPropagation();
}


function islandDragleave(event, callingElement) {
    event.preventDefault();
    if (callingElement.getAttribute("data-islandPopped") === "false") {
        clearTimeout(hoverTimer);
    }
    event.stopPropagation();
}


function popupDragleave(event, callingElement) {
    event.preventDefault();
    clearTimeout(hoverTimer);
    hoverTimer = setTimeout(function() { hideIslands();}, 1000);
    event.stopPropagation();
}