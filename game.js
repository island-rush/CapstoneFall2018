//Javascript Functions used by the Island Rush Game
//Created by C1C Spencer Adolph (7/28/2018)


function islandClick(event, callingElement) {
    event.preventDefault();
    hideIslands();  //only 1 island visible at a time
    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    hideContainers("lavContainer");
    clearHighlighted();
    if (gameBattleSection === "none" || gameBattleSection === "selectPos" || gameBattleSection === "selectPieces") {
        document.getElementsByClassName(callingElement.id)[0].style.display = "block";
        callingElement.style.zIndex = 20;  //default for a gridblock is 10
        callingElement.setAttribute("data-islandPopped", "true");
    }
    event.stopPropagation();
}


function waterClick(event, callingElement) {
    event.preventDefault();
    hideIslands();
    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    hideContainers("lavContainer");
    clearHighlighted();

    if (gameBattleSection === "selectPos") {
        gameBattlePosSelected = callingElement.getAttribute("data-positionId");
    }

    event.stopPropagation();
}


function landClick(event, callingElement) {
    event.preventDefault();

    if (gameBattleSection === "selectPos") {
        battleSelectPosition(callingElement.getAttribute("data-positionId"));
    }

    event.stopPropagation();
}


function gameboardClick(event, callingElement) {
    event.preventDefault();
    hideIslands();
    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    hideContainers("lavContainer");
    clearHighlighted();
    event.stopPropagation();
}


//---------------------------------------------------
function pieceClick(event, callingElement) {
    event.preventDefault();
    //open container if applicable
    if (gameBattleSection === "selectPieces") {
        if (callingElement.getAttribute("data-placementTeamId") === myTeam) {
            if (gameBattleAdjacentArray.includes(parseInt(callingElement.parentNode.getAttribute("data-positionId")))) {
                if (callingElement.classList.contains("selected")) {
                    callingElement.classList.remove("selected");
                } else {
                    callingElement.classList.add("selected");
                }
            }
        }
    } else {
        let unitName = callingElement.getAttribute("data-unitName");
        if (unitName === "transport" || unitName === "aircraftCarrier" || unitName === "lav") {
            hideContainers("transportContainer");
            hideContainers("aircraftCarrierContainer");
            hideContainers("lavContainer");
            if (callingElement.parentNode.getAttribute("data-positionId") !== "118") {
                callingElement.childNodes[0].style.display = "block";
                callingElement.style.zIndex = 30;
                callingElement.childNodes[0].setAttribute("data-containerPopped", "true");
            }
        }
        clearHighlighted();
        //show the piece's moves
        let thisMoves = callingElement.getAttribute("data-placementCurrentMoves");
        let thisPos = callingElement.parentNode.getAttribute("data-positionId");
        let phpAvailableMoves = new XMLHttpRequest();
        phpAvailableMoves.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let decoded = JSON.parse(this.responseText);
                let g;
                for (g = 0; g < decoded.length; g++) {
                    let gridThing = document.querySelectorAll("[data-positionId='" + decoded[g] + "']")[0];
                    gridThing.classList.add("highlighted");
                    if (gridThing.classList[0] === "gridblockTiny") {
                        let parent = gridThing.parentNode;
                        let parclass = parent.classList;
                        if (parclass[0] !== "gridblockLeftBig" && parclass[0] !== "gridblockRightBig") {
                            let islandsquare = document.getElementById(parclass[0]);
                            islandsquare.classList.add("highlighted");
                        }
                    }
                }
            }
        };
        phpAvailableMoves.open("GET", "pieceMoveAvailable.php?thisPos=" + thisPos + "&thisMoves=" + thisMoves, true);
        phpAvailableMoves.send();


        if (gameBattleSection === "selectPos") {
            battleSelectPosition(callingElement.parentNode.getAttribute("data-positionId"));
        }
    }




    event.stopPropagation();
}

function pieceDragstart(event, callingElement) {
    //canMove is dictated by phase and current Team
    if (canMove === "true" && event.target.getAttribute("data-placementTeamId") === myTeam && gameBattleSection === "none") {
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

function pieceDragleave(event, callingElement) {
    event.preventDefault();
    if (callingElement.getAttribute("data-unitName") === "transport" || callingElement.getAttribute("data-unitName") === "aircraftCarrier" || callingElement.getAttribute("data-unitName") === "lav") {
        if (callingElement.childNodes[0].getAttribute("data-containerPopped") === "false") {
            clearTimeout(hoverTimer);
        }
    }
    event.stopPropagation();
}

function pieceDragenter(event, callingElement) {
    event.preventDefault();
    let unitName = callingElement.getAttribute("data-unitName");
    if (unitName === "transport" || unitName === "aircraftCarrier" || unitName === "lav") {
        //only dragenter to open up container pieces
        if (callingElement.parentNode.getAttribute("data-positionId") !== "118") {
            clearTimeout(hoverTimer);
            hoverTimer = setTimeout(function() { pieceClick(event, callingElement);}, 1000);
        }
    }
    event.stopPropagation();
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
                parent.innerHTML = this.responseText;
            }
        };
        phpPurchaseRequest.open("GET", "piecePurchase.php?unitId=" + unitId + "&unitName=" + unitName + "&unitMoves=" + unitMoves + "&unitTerrain=" + terrain + "&placementTeamId=" + myTeam + "&gameId=" + gameId, true);
        phpPurchaseRequest.send();
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
//---------------------------------------------------


function hideIslands() {
    let x = document.getElementsByClassName("special_island3x3");
    let i;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
        x[i].parentNode.style.zIndex = 10;  //10 is the default
        x[i].parentNode.setAttribute("data-islandPopped", "false");
    }
}


function hideContainers(containerType) {
    let s = document.getElementsByClassName(containerType);
    let r;
    for (r = 0; r < s.length; r++) {
        s[r].style.display = "none";
        s[r].parentNode.style.zIndex = 15;
        s[r].setAttribute("data-containerPopped", "false");
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
        let phpMoveCheck = new XMLHttpRequest();
        phpMoveCheck.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let movementCost = this.responseText;
                if (movementCost !== "-1") {
                    if ((new_placementContainerId !== "999999" && containerHasSpotOpen(new_placementContainerId, unitName) === "true") || new_placementContainerId === "999999") {
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
        };
        phpMoveCheck.open("POST", "pieceMoveValid.php?new_positionId=" + new_positionId + "&old_positionId=" + old_positionId + "&placementCurrentMoves=" + old_placementCurrentMoves, true);
        phpMoveCheck.send();
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


function movementTerrainCheck(unitTerrain, positionType) {
    return "true";
}


function containerHasSpotOpen(new_placementContainerId, unitName) {
    //Can't put transport inside another transport
    if (new_placementContainerId !== "999999") {
        if (unitName === "transport" || unitName === "aircraftCarrier" || unitName === "lav") {
            return "false";
        }
    }

    return "true";
}


function islandDragenter(event, callingElement) {
    event.preventDefault();
    clearTimeout(hoverTimer);
    hoverTimer = setTimeout(function() { islandClick(event, callingElement);}, 1000);
    event.stopPropagation();
}


function containerDragleave(event, callingElement) {
    event.preventDefault();
    clearTimeout(hoverTimer);
    hoverTimer = setTimeout(function() { waterClick(event, callingElement);}, 1000);
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


function clearHighlighted() {
    let highlighted_things = document.getElementsByClassName("highlighted");
    while (highlighted_things.length) {
        highlighted_things[0].classList.remove("highlighted");
    }
}


function bodyLoader() {
    document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase-1];
    document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;

    if (gameBattleSection !== "none" && gameBattleSection !== "selectPos" && gameBattleSection !== "selectPieces") {
        document.getElementById("battleZonePopup").style.display = "block";
    }

    if (gameBattleSection === "none") {
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Battle";
        document.getElementById("battle_button").onclick = function() { battleChangeSection("selectPos"); };
    } else if (gameBattleSection === "selectPos") {
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Pieces";
        document.getElementById("battle_button").onclick = function() { battleSelectPosition(); };
    } else if (gameBattleSection === "selectPieces") {
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Start Battle";
        document.getElementById("battle_button").onclick = function() { battleSelectPieces(); };
    } else {
        document.getElementById("battle_button").disabled = true;
        document.getElementById("battle_button").innerHTML = "Select Battle";
    }

    //deal with buttons and things on the battleZonePopup (as they should appear based upon game states / subsections
    if (gameBattleSubSection !== "choosing_pieces") {
        document.getElementById("battleActionPopup").style.display = "block";
        if (gameBattleSubSection === "defense_bonus") {
            document.getElementById("actionButton").innerHTML = "click to roll for defense bonus";
            document.getElementById("actionButton").onclick = function() { battleAttackCenter("defend"); };
        } else if (gameBattleSubSection === "continue_choosing") {
            document.getElementById("actionButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
            document.getElementById("actionButton").onclick = function() { battleEndRoll(); };
        }
    }

    if (document.getElementById("center_defender").childNodes.length === 1 && document.getElementById("center_attacker").childNodes.length === 1) {
        document.getElementById("attackButton").disabled = false;
    }

    //could consolidate these with a call to change section (with same section)
    if (gameBattleSection === "attack") {
        document.getElementById("attackButton").innerHTML = "Attack section";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };
        document.getElementById("changeSectionButton").innerHTML = "Click to Counter";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("counter") };
    } else if (gameBattleSection === "counter") {
        document.getElementById("attackButton").innerHTML = "Counter Attack";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("defend"); };
        document.getElementById("changeSectionButton").innerHTML = "Click End Counter";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("ask_repeat") };
    } else if (gameBattleSection === "askRepeat") {
        document.getElementById("attackButton").innerHTML = "Click to Repeat";
        document.getElementById("attackButton").disabled = false;
        document.getElementById("attackButton").onclick = function() { battleChangeSection("attack") };
        document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };
    }
}


function changePhase() {
    if (canNextPhase === "true") {
        let phpPhaseChange = new XMLHttpRequest();
        phpPhaseChange.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let decoded = JSON.parse(this.responseText);
                gamePhase = decoded.gamePhase;
                gameTurn = decoded.gameTurn;
                gameCurrentTeam = decoded.gameCurrentTeam;
                canMove = decoded.canMove;
                canPurchase = decoded.canPurchase;
                canUndo = decoded.canUndo;
                canNextPhase = decoded.canNextPhase;
                canTrash = decoded.canTrash;
                canAttack = decoded.canAttack;
                if (canAttack === "true") {
                    document.getElementById("battle_button").disabled = false;
                } else {
                    document.getElementById("battle_button").disabled = true;
                }
                document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase - 1];
                document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
            }
        };
        phpPhaseChange.open("GET", "gamePhaseChange.php", true);  // removes the element from the database
        phpPhaseChange.send();
    }
}





function battleChangeSection(newSection) {
    gameBattleSection = newSection;

    if (newSection === "selectPos") {
        document.getElementById("battle_button").onclick = function() { battleSelectPosition(); };
        document.getElementById("battle_button").innerHTML = "Select Pieces";
    } else if (newSection === "selectPieces") {
        document.getElementById("battle_button").onclick = function() { battleSelectPieces(); };
        document.getElementById("battle_button").innerHTML = "Start Battle";
    } else if (newSection === "attack") {
        document.getElementById("battleZonePopup").style.display = "block";
        document.getElementById("attackButton").innerHTML = "Attack section";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };
        document.getElementById("changeSectionButton").innerHTML = "Click to Counter";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("counter") };
    } else if (newSection === "counter") {
        document.getElementById("attackButton").innerHTML = "Counter Attack";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("defend"); };
        document.getElementById("changeSectionButton").innerHTML = "Click End Counter";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("ask_repeat") };
    } else if (newSection === "askRepeat") {
        document.getElementById("attackButton").innerHTML = "Click to Repeat";
        document.getElementById("attackButton").disabled = false;
        document.getElementById("attackButton").onclick = function() { battleChangeSection("attack") };
        document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };
    } else if (newSection === "none") {
        document.getElementById("battleZonePopup").style.display = "none";
    }

    let phpBattleUpdate = new XMLHttpRequest();
    phpBattleUpdate.open("POST", "battleUpdateAttributes.php?gameBattleSection=" + gameBattleSection + "&gameBattleSubSection=" + gameBattleSubSection + "&gameBattleLastRoll=" + gameBattleLastRoll + "&gameBattleLastMessage=" + gameBattleLastMessage + "&gameBattlePosSelected=" + gameBattlePosSelected, true);
    phpBattleUpdate.send();
}


function battleSelectPieces() {

    let parameterArray = [];

    let allPieces = document.getElementsByClassName("selected");
    let x;
    for (x = 0; x < allPieces.length; x++) {
        parameterArray.push(allPieces[x].getAttribute("data-placementId"));
    }

    let sentArray = JSON.stringify(parameterArray);

    let phpPiecesSelect = new XMLHttpRequest();
    phpPiecesSelect.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            document.getElementById("unused_attacker").innerHTML += this.responseText;
        }
    };
    phpPiecesSelect.open("POST", "battlePiecesSelected.php?sentArray=" + sentArray + "&gameId=" + gameId + "&attackTeam=" + gameCurrentTeam, true);
    phpPiecesSelect.send();

    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    hideContainers("lavContainer");
    clearHighlighted();
    battleChangeSection("attack");
}


function battleSelectPosition() {
    let battleTerrain = document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").getAttribute("data-positionType");
    let defenseTeam;

    if (gameCurrentTeam === "Red") {
        defenseTeam = "Blue";
    } else {
        defenseTeam = "Red";
    }

    let phpPositionSelect = new XMLHttpRequest();
    phpPositionSelect.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let decoded = JSON.parse(this.responseText);
            document.getElementById("unused_defender").innerHTML = decoded.htmlString;
            gameBattleAdjacentArray = decoded.adjacentArray;
        }
    };
    phpPositionSelect.open("POST", "battlePositionSelected.php?positionSelected=" + gameBattlePosSelected + "&gameId=" + gameId + "&defenseTeam=" + defenseTeam + "&battleTerrain=" + battleTerrain, true);
    phpPositionSelect.send();

    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    hideContainers("lavContainer");
    clearHighlighted();
    battleChangeSection("selectPieces");
}


function battlePieceClick(event, callingElement) {
    event.preventDefault();
    let boxId = callingElement.parentNode.getAttribute("data-boxId");
    let battlePieceId = callingElement.getAttribute("data-battlePieceId");
    let phpMoveBattlePiece = new XMLHttpRequest();
    if (boxId === "5" || boxId === "6") {
        if (boxId === "5") {
            //box 5 -> 1
            document.getElementById("center_attacker").removeChild(callingElement);
            document.getElementById("unused_attacker").appendChild(callingElement);
            phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + battlePieceId + "&new_battlePieceState=1", true);
            phpMoveBattlePiece.send();
        } else {
            //box 6 -> 2
            document.getElementById("center_defender").removeChild(callingElement);
            document.getElementById("unused_defender").appendChild(callingElement);
            phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + battlePieceId + "&new_battlePieceState=2", true);
            phpMoveBattlePiece.send();
        }
    } else {
        if (boxId === "1") {
            //box 1 -> 5
            if (document.getElementById("center_attacker").childNodes.length === 0) {
                document.getElementById("unused_attacker").removeChild(callingElement);
                document.getElementById("center_attacker").appendChild(callingElement);
                phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + battlePieceId + "&new_battlePieceState=5", true);
                phpMoveBattlePiece.send();
            }
        } else {
            //box 2 -> 6
            if (document.getElementById("center_defender").childNodes.length === 0) {
                document.getElementById("unused_defender").removeChild(callingElement);
                document.getElementById("center_defender").appendChild(callingElement);
                phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + battlePieceId + "&new_battlePieceState=6", true);
                phpMoveBattlePiece.send();
            }
        }
    }
    if ((document.getElementById("center_defender").childNodes.length === 1 && document.getElementById("center_attacker").childNodes.length === 1) || gameBattleSection === "askRepeat") {
        document.getElementById("attackButton").disabled = false;
    } else {
        document.getElementById("attackButton").disabled = true;
    }
    event.stopPropagation();
}



function battleEndRoll() {

}


function battleAttackCenter(type) {
    let attackUnitId;
    let defendUnitId;
    let pieceAttacked;
    if (type === "attack") {
        attackUnitId = document.getElementById("center_attacker").childNodes[0].getAttribute("data-unitId");
        defendUnitId = document.getElementById("center_defender").childNodes[0].getAttribute("data-unitId");
        pieceAttacked = document.getElementById("center_defender").childNodes[0];
    } else {
        attackUnitId = document.getElementById("center_defender").childNodes[0].getAttribute("data-unitId");
        defendUnitId = document.getElementById("center_attacker").childNodes[0].getAttribute("data-unitId");
        pieceAttacked = document.getElementById("center_attacker").childNodes[0];
    }

    let phpAttackCenter = new XMLHttpRequest();
    phpAttackCenter.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let decoded = JSON.parse(this.responseText);
            let new_gameBattleSubSection = decoded.new_gameBattleSubSection;

            let actionButton = document.getElementById("actionPopupButton");
            if (new_gameBattleSubSection === "defense_bonus") {
                actionButton.innerHTML = "HIT! Roll for Defense Bonus";
                actionButton.onclick = function() { battleAttackCenter("defend"); };
            } else if (new_gameBattleSubSection === "continue_choosing") {
                actionButton.innerHTML = "click to go back to Choosing";
                actionButton.onclick = function() { battleEndRoll(); };
            }

            pieceAttacked.setAttribute("data-wasHit", decoded.wasHit);

            gameBattleLastRoll = decoded.lastRoll;
            gameBattleSubSection = decoded.new_gameBattleSubSection;

            battleChangeSection(gameBattleSection);  //This call to change roll and subsection
            document.getElementById("battleActionPopup").style.display = "block";
        }
    };
    phpAttackCenter.open("GET", "battleAttackCenter.php?attackUnitId=" + attackUnitId + "&defendUnitId=" + defendUnitId + "&gameBattleSection=" + gameBattleSection + "&gameBattleSubSection=" + gameBattleSubSection + "&pieceId=" + pieceAttacked.getAttribute("data-battlePieceId"), true);
    phpAttackCenter.send();
}


