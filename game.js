//Javascript Functions used by the Island Rush Game
//Created by C1C Spencer Adolph (7/28/2018)

//First function called to load the game
function bodyLoader() {
    // alert(myTeam);
    document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase-1];
    document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;

    //TODO: change this to be team specific (based on if I am the current team or not) (reorganize / refactor)(or is this already done with canAttack?)
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
        if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "attack") {
            if (myTeam !== gameCurrentTeam) {
                document.getElementById("actionPopupButton").disabled = false;
            } else {
                document.getElementById("actionPopupButton").disabled = true;
            }
            document.getElementById("actionPopupButton").innerHTML = "click to roll for defense bonus";
            document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
        } else if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "counter") {
            if (myTeam !== gameCurrentTeam) {
                document.getElementById("actionPopupButton").disabled = true;
            } else {
                document.getElementById("actionPopupButton").disabled = false;
            }
            document.getElementById("actionPopupButton").innerHTML = "click to roll for defense bonus";
            document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
        } else if (gameBattleSubSection === "continue_choosing" && gameBattleSection === "attack") {
            if (myTeam !== gameCurrentTeam) {
                document.getElementById("actionPopupButton").disabled = true;
            } else {
                document.getElementById("actionPopupButton").disabled = false;
            }
            document.getElementById("actionPopupButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
            document.getElementById("actionPopupButton").onclick = function() { battleEndRoll(); };
        } else if (gameBattleSubSection === "continue_choosing" && gameBattleSection === "counter") {
            if (myTeam !== gameCurrentTeam) {
                document.getElementById("actionPopupButton").disabled = false;
            } else {
                document.getElementById("actionPopupButton").disabled = true;
            }
            document.getElementById("actionPopupButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
            document.getElementById("actionPopupButton").onclick = function() { battleEndRoll(); };
        }
    } else {
        document.getElementById("battleActionPopup").style.display = "none";
    }

    if (document.getElementById("center_defender").childNodes.length === 1 && document.getElementById("center_attacker").childNodes.length === 1) {
        if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {
            document.getElementById("attackButton").disabled = false;
        }
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
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("askRepeat") };
    } else if (gameBattleSection === "askRepeat") {
        document.getElementById("attackButton").innerHTML = "Click to Repeat";
        document.getElementById("attackButton").onclick = function() { battleChangeSection("attack") };
        document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };
    }

    if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {
        // document.getElementById("attackButton").disabled = false;
        document.getElementById("changeSectionButton").disabled = false;
    }

    if (gameBattleSection === "askRepeat" && myTeam === gameCurrentTeam) {
        document.getElementById("attackButton").disabled = false;
        document.getElementById("changeSectionButton").disabled = false;
    } else if (gameBattleSection === "askRepeat" && myTeam !== gameCurrentTeam) {
        document.getElementById("attackButton").disabled = true;
        document.getElementById("changeSectionButton").disabled = true;
    }

    if (canAttack === "true") {
        document.getElementById("battle_button").disabled = false;
    } else {
        document.getElementById("battle_button").disabled = true;
    }
    if (canUndo === "true") {
        document.getElementById("undo_button").disabled = false;
    } else {
        document.getElementById("undo_button").disabled = true;
    }
    if (canNextPhase === "true") {
        document.getElementById("phase_button").disabled = false;
    } else {
        document.getElementById("phase_button").disabled = true;
    }
    if (gamePhase === "1") {
        // alert("phase1");
        //TODO: phase effects here and grab phase stuff???
        document.getElementById("newsPopup").style.display = "block";
    } else {
        // alert("not phase 1");
        document.getElementById("newsPopup").style.display = "none";
    }
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
        if (gameBattleSection === "selectPos") {
            clearSelectedPos();
            callingElement.parentNode.classList.add("selectedPos");
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
            // let thisMoves = callingElement.getAttribute("data-placementCurrentMoves");
            // let thisPos = callingElement.parentNode.getAttribute("data-positionId");
            // let phpAvailableMoves = new XMLHttpRequest();
            // phpAvailableMoves.onreadystatechange = function () {
            //     if (this.readyState === 4 && this.status === 200) {
            //         let decoded = JSON.parse(this.responseText);
            //         let g;
            //         for (g = 0; g < decoded.length; g++) {
            //             let gridThing = document.querySelectorAll("[data-positionId='" + decoded[g] + "']")[0];
            //             gridThing.classList.add("highlighted");
            //             if (gridThing.classList[0] === "gridblockTiny") {
            //                 let parent = gridThing.parentNode;
            //                 let parclass = parent.classList;
            //                 if (parclass[0] !== "gridblockLeftBig" && parclass[0] !== "gridblockRightBig") {
            //                     let islandsquare = document.getElementById(parclass[0]);
            //                     islandsquare.classList.add("highlighted");
            //                     if (islandsquare.id === "special_island5") {
            //                         document.getElementById("special_island5_extra").classList.add("highlighted");
            //                     }
            //                 }
            //             }
            //
            //         }
            //     }
            // };
            // phpAvailableMoves.open("GET", "pieceMoveAvailable.php?thisPos=" + thisPos + "&thisMoves=" + thisMoves, true);
            // phpAvailableMoves.send();
        }
    }
    event.stopPropagation();
}

function pieceDragstart(event, callingElement) {
    //canMove is dictated by phase and current Team
    if ((canMove === "true" || canPurchase === "true") && callingElement.getAttribute("data-placementTeamId") === myTeam && gameBattleSection === "none") {
        //From the container (parent of the piece)
        event.dataTransfer.setData("positionId", callingElement.parentNode.getAttribute("data-positionId"));
        //From the Piece
        event.dataTransfer.setData("placementId", callingElement.getAttribute("data-placementId"));
        event.dataTransfer.setData("placementContainerId", callingElement.getAttribute("data-placementContainerId"));
        event.dataTransfer.setData("placementCurrentMoves", callingElement.getAttribute("data-placementCurrentMoves"));
        event.dataTransfer.setData("placementTeamId", callingElement.getAttribute("data-placementTeamId"));
        event.dataTransfer.setData("unitTerrain", callingElement.getAttribute("data-unitTerrain"));
        event.dataTransfer.setData("unitName", callingElement.getAttribute("data-unitName"));
        event.dataTransfer.setData("unitId", callingElement.getAttribute("data-unitId"));
        event.dataTransfer.setData("unitCost", callingElement.getAttribute("data-unitCost"));
    } else {
        event.preventDefault();  // This stops the drag
    }

    event.stopPropagation();
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

function piecePurchase(event, purchaseSquare) {
    event.preventDefault();
    if (canPurchase === "true") {
        let costOfPiece = parseInt(purchaseSquare.getAttribute("data-unitCost"));
        if (myPoints >= costOfPiece) {
            // alert("doing thing correctly");
            let unitId = purchaseSquare.getAttribute("data-unitId");
            let unitName = purchaseSquare.id;
            let unitMoves = unitsMoves[unitName];
            let terrain = purchaseSquare.getAttribute("data-unitTerrain");
            myPoints = myPoints - costOfPiece;
            let phpPurchaseRequest = new XMLHttpRequest();
            phpPurchaseRequest.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let parent = document.getElementById("purchased_container");
                    parent.innerHTML += this.responseText;
                }
            };
            phpPurchaseRequest.open("GET", "piecePurchase.php?unitId=" + unitId + "&costOfPiece=" + costOfPiece + "&newPoints=" + myPoints + "&myTeam=" + myTeam + "&unitName=" + unitName + "&unitMoves=" + unitMoves + "&unitTerrain=" + terrain + "&placementTeamId=" + myTeam + "&gameId=" + gameId, true);
            phpPurchaseRequest.send();
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
                    clearHighlighted();
                    //Update the piece's attributes
                    let pieceToUndo = document.querySelector("[data-placementId='" + decoded.placementId + "']");
                    pieceToUndo.setAttribute("data-placementContainerId", decoded.new_placementContainerId);
                    pieceToUndo.setAttribute("data-placementCurrentMoves", decoded.new_placementCurrentMoves);
                    //Append to New Position
                    if (decoded.new_placementContainerId !== 999999) {
                        document.querySelector("[data-placementId='" + decoded.new_placementContainerId + "']").firstChild.appendChild(pieceToUndo);
                    } else {
                        document.querySelector("[data-positionId='" + decoded.new_placementPositionId + "']").appendChild(pieceToUndo);
                    }
                }
            }
        };
        phpUndoRequest.open("GET", "pieceMoveUndo.php?gameId=" + gameId + "&gameTurn=" + gameTurn + "&gamePhase=" + gamePhase + "&myTeam=" + myTeam, true);
        phpUndoRequest.send();
    }
}

function pieceTrash(event, trashElement) {
    event.preventDefault();
    if (canTrash === "true") {
        if (event.dataTransfer.getData("positionId") === "118") {
            let placementId = event.dataTransfer.getData("placementId");
            document.querySelector("[data-placementId='" + placementId + "']").remove();
            let costOfPiece = parseInt(event.dataTransfer.getData("unitCost"));
            myPoints = myPoints + costOfPiece;
            let phpTrashRequest = new XMLHttpRequest();
            phpTrashRequest.open("POST", "pieceTrash.php?placementId=" + placementId + "&myTeam=" + myTeam + "&gameId=" + gameId + "&newPoints=" + myPoints, true);
            phpTrashRequest.send();
        }
    }
}

function containerDragleave(event, callingElement) {
    event.preventDefault();
    clearTimeout(hoverTimer);
    hoverTimer = setTimeout(function() { waterClick(event, callingElement);}, 1000);
    event.stopPropagation();
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

function hideContainers(containerType) {
    let s = document.getElementsByClassName(containerType);
    let r;
    for (r = 0; r < s.length; r++) {
        s[r].style.display = "none";
        s[r].parentNode.style.zIndex = 15;
        s[r].setAttribute("data-containerPopped", "false");
    }
}
//---------------------------------------------------


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

function islandDragenter(event, callingElement) {
    event.preventDefault();
    clearTimeout(hoverTimer);
    hoverTimer = setTimeout(function() { islandClick(event, callingElement);}, 1000);
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

function hideIslands() {
    let x = document.getElementsByClassName("special_island3x3");
    let i;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
        x[i].parentNode.style.zIndex = 10;  //10 is the default
        x[i].parentNode.setAttribute("data-islandPopped", "false");
    }
}



function landClick(event, callingElement) {
    event.preventDefault();

    if (gameBattleSection === "selectPos") {
        clearSelectedPos();
        callingElement.classList.add("selectedPos");
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

function waterClick(event, callingElement) {
    event.preventDefault();
    hideIslands();
    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    hideContainers("lavContainer");
    clearHighlighted();

    if (gameBattleSection === "selectPos") {
        clearSelectedPos();
        callingElement.classList.add("selectedPos");
    }

    event.stopPropagation();
}



function positionDrop(event, newContainerElement) {
    event.preventDefault();
    clearHighlighted();
    //Already approved to move by pieceDragstart (same team and good phase)
    let placementId = event.dataTransfer.getData("placementId");
    let unitName = event.dataTransfer.getData("unitName");
    let unitId = event.dataTransfer.getData("unitId");
    let pieceDropped = document.querySelector("[data-placementId='" + placementId + "']");
    let positionType = newContainerElement.getAttribute("data-positionType");
    let unitTerrain = event.dataTransfer.getData("unitTerrain");
    let new_positionId = newContainerElement.getAttribute("data-positionId");
    let old_positionId = event.dataTransfer.getData("positionId");
    let old_placementContainerId = event.dataTransfer.getData("placementContainerId");
    let new_placementContainerId = newContainerElement.getAttribute("data-positionContainerId");
    let old_placementCurrentMoves = event.dataTransfer.getData("placementCurrentMoves");

    if (old_positionId !== "118" || (old_positionId == "118" && gamePhase == 5)) {
        if (movementTerrainCheck(unitTerrain, positionType) === "true") {
            let phpMoveCheck = new XMLHttpRequest();
            phpMoveCheck.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let movementCost = this.responseText;
                    if (movementCost !== "-1") {
                        if ((new_placementContainerId !== "999999" && containerHasSpotOpen(new_placementContainerId, unitName) === "true") || new_placementContainerId === "999999") {
                            //MANY OTHER CHECKS FOR MOVEMENT CAN HAPPEN HERE, JUST NEST MORE FUNCTIONS (see above)
                            let new_placementCurrentMoves = old_placementCurrentMoves - movementCost;

                            //Update the html by moving the piece and changing the piece's attributes
                            newContainerElement.appendChild(pieceDropped);
                            pieceDropped.setAttribute("data-placementCurrentMoves", new_placementCurrentMoves.toString());
                            pieceDropped.setAttribute("data-placementContainerId", new_placementContainerId);
                            if (unitName === "transport" || unitName === "aircraftCarrier" || unitName === "lav") {
                                pieceDropped.firstChild.setAttribute("data-positionId", newContainerElement.getAttribute("data-positionId"));
                            }

                            //Update the placement in the database and add a movement to the database
                            let phpRequest = new XMLHttpRequest();
                            phpRequest.open("POST", "pieceMove.php?gameId=" + gameId + "&myTeam=" + myTeam + "&gameTurn=" + gameTurn + "&gamePhase=" + gamePhase + "&placementId=" + placementId + "&unitName=" + unitName + "&new_positionId=" + new_positionId + "&old_positionId=" + old_positionId + "&movementCost=" + movementCost  + "&new_placementCurrentMoves=" + new_placementCurrentMoves + "&old_placementContainerId=" + old_placementContainerId + "&new_placementContainerId=" + new_placementContainerId, true);
                            phpRequest.send();
                            let flagPositions = [55, 65, 75, 79, 83, 86, 90, 94, 97, 100, 103, 107, 111, 114];
                            let parentTeam = newContainerElement.parentNode.classList[2];
                            let newTeam;
                            if (parentTeam === "Red") {
                                newTeam = "Blue";
                            } else {
                                newTeam = "Red";
                            }
                            if (flagPositions.includes(parseInt(new_positionId))) {
                                let changeOwnership = "true";
                                let numChildren = newContainerElement.childElementCount;
                                for (let x = 0; x < numChildren; x++) {
                                    if (newContainerElement.childNodes[x].getAttribute("data-placementTeamId") === parentTeam) {
                                        changeOwnership = "false";
                                    }
                                }
                                if (changeOwnership === "true") {
                                    //change css of parent
                                    let parent = newContainerElement.parentNode;
                                    parent.classList.remove(parentTeam);
                                    parent.classList.add(newTeam);
                                    //change css of parent parent
                                    let parentParent = parent.parentNode;
                                    parentParent.classList.remove(parentTeam);
                                    parentParent.classList.add(newTeam);
                                    //database change in games table
                                    let islandNumber = parent.id;
                                    let phpRequestTeamChange = new XMLHttpRequest();
                                    phpRequestTeamChange.open("POST", "gameIslandOwnerChange.php?gameId=" + gameId + "&islandToChange=" + islandNumber + "&newTeam=" + newTeam, true);
                                    phpRequestTeamChange.send();
                                }
                            }
                        }
                    }
                }
            };
            phpMoveCheck.open("POST", "pieceMoveValid.php?new_positionId=" + new_positionId + "&old_positionId=" + old_positionId + "&placementCurrentMoves=" + old_placementCurrentMoves, true);
            phpMoveCheck.send();
        }
    }
    event.stopPropagation();
}

function positionDragover(event, callingElement) {
    event.preventDefault();
    //Stops from Dropping into another piece (non-container element) (containers should not be draggable, only parent pieces)
    if (callingElement.getAttribute("draggable") === "true") {
        event.dataTransfer.dropEffect = "none";
    } else {
        event.dataTransfer.dropEffect = "all";
    }
    hoverTimer = setTimeout(function() { hideIslands();}, 1000)
}

function movementTerrainCheck(unitTerrain, positionType) {
    return "true";
}



function changePhase() {
    if (canNextPhase === "true") {
        let phpPhaseChange = new XMLHttpRequest();
        phpPhaseChange.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                // alert(this.responseText);
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

                //TODO: deal with news alerts from table (not yet defined / implemented)
                // alert(decoded.newsalertthing1);

                if (canAttack === "true") {
                    document.getElementById("battle_button").disabled = false;
                } else {
                    document.getElementById("battle_button").disabled = true;
                }
                if (canUndo === "true") {
                    document.getElementById("undo_button").disabled = false;
                } else {
                    document.getElementById("undo_button").disabled = true;
                }
                if (canNextPhase === "true") {
                    document.getElementById("phase_button").disabled = false;
                } else {
                    document.getElementById("phase_button").disabled = true;
                }
                // alert(gamePhase);
                if (gamePhase === "1") {
                    // alert("phase1");
                    //TODO: phase effects here and grab phase stuff???
                    document.getElementById("newsPopup").style.display = "block";
                } else {
                    // alert("not phase 1");
                    document.getElementById("newsPopup").style.display = "none";
                }
                document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase - 1];
                document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
            }
        };
        phpPhaseChange.open("GET", "gamePhaseChange.php", true);  // removes the element from the database
        phpPhaseChange.send();
    }
}



function clearHighlighted() {
    let highlighted_things = document.getElementsByClassName("highlighted");
    while (highlighted_things.length) {
        highlighted_things[0].classList.remove("highlighted");
    }
}

function clearSelected() {
    let highlighted_things = document.getElementsByClassName("selected");
    while (highlighted_things.length) {
        highlighted_things[0].classList.remove("selected");
    }
}

function clearSelectedPos() {
    let highlighted_things = document.getElementsByClassName("selectedPos");
    while (highlighted_things.length) {
        highlighted_things[0].classList.remove("selectedPos");
    }
}



function battleChangeSection(newSection) {
    gameBattleSection = newSection;

    if (newSection === "selectPos") {
        document.getElementById("battle_button").onclick = function() { battleSelectPosition(); };
        document.getElementById("battle_button").innerHTML = "Select Pieces";

        alert("Select a Position on the Board");
        //more visual indication of selecting position
        document.getElementById("whole_game").style.backgroundColor = "yellow";
    } else if (newSection === "selectPieces") {
        document.getElementById("battle_button").onclick = function() { battleSelectPieces(); };
        document.getElementById("battle_button").innerHTML = "Start Battle";

        alert("Select pieces to attack with Adjacent to the Position");
        //more visual indication of selecting pieces
    } else if (newSection === "attack") {
        document.getElementById("whole_game").style.backgroundColor = "black";

        document.getElementById("battle_button").disabled = true;
        clearSelected();
        if (document.getElementById("center_defender").childNodes.length === 1 && document.getElementById("center_attacker").childNodes.length === 1) {
            document.getElementById("attackButton").disabled = false;
        } else {
            document.getElementById("attackButton").disabled = true;
        }
        document.getElementById("battleZonePopup").style.display = "block";
        document.getElementById("attackButton").innerHTML = "Attack section";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };

        if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {
            document.getElementById("changeSectionButton").disabled = false;
        }

        document.getElementById("changeSectionButton").innerHTML = "Click to Counter";
        document.getElementById("changeSectionButton").onclick = function() {
            battleChangeSection("counter");
            let newParent = document.getElementById('unused_attacker');
            let oldParent = document.getElementById('used_attacker');
            while (oldParent.childNodes.length > 0) {
                oldParent.childNodes[0].onclick = function() { battlePieceClick(event, this); };
                let phpMoveBattlePiece = new XMLHttpRequest();
                phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + oldParent.childNodes[0].getAttribute("data-battlePieceId") + "&new_battlePieceState=1", true);
                phpMoveBattlePiece.send();
                newParent.appendChild(oldParent.childNodes[0]);
            }
            newParent = document.getElementById('unused_defender');
            oldParent = document.getElementById('used_defender');
            while (oldParent.childNodes.length > 0) {
                oldParent.childNodes[0].onclick = function() { battlePieceClick(event, this); };
                let phpMoveBattlePiece = new XMLHttpRequest();
                phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + oldParent.childNodes[0].getAttribute("data-battlePieceId") + "&new_battlePieceState=2", true);
                phpMoveBattlePiece.send();
                newParent.appendChild(oldParent.childNodes[0]);
            }
        };
    } else if (newSection === "counter") {

        if (gameCurrentTeam === myTeam) {
            document.getElementById("changeSectionButton").disabled = true;
        } else {
            document.getElementById("changeSectionButton").disabled = false;
        }

        document.getElementById("attackButton").innerHTML = "Counter Attack";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("defend"); };

        document.getElementById("changeSectionButton").innerHTML = "Click End Counter";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("askRepeat"); };
    } else if (newSection === "askRepeat") {
        let newParent = document.getElementById('unused_attacker');
        let oldParent = document.getElementById('used_attacker');
        while (oldParent.childNodes.length > 0) {
            oldParent.childNodes[0].onclick = function() { battlePieceClick(event, this); };
            let phpMoveBattlePiece = new XMLHttpRequest();
            phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + oldParent.childNodes[0].getAttribute("data-battlePieceId") + "&new_battlePieceState=1", true);
            phpMoveBattlePiece.send();
            newParent.appendChild(oldParent.childNodes[0]);
        }
        newParent = document.getElementById('unused_defender');
        oldParent = document.getElementById('used_defender');
        while (oldParent.childNodes.length > 0) {
            oldParent.childNodes[0].onclick = function() { battlePieceClick(event, this); };
            let phpMoveBattlePiece = new XMLHttpRequest();
            phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + oldParent.childNodes[0].getAttribute("data-battlePieceId") + "&new_battlePieceState=2", true);
            phpMoveBattlePiece.send();
            newParent.appendChild(oldParent.childNodes[0]);
        }
        document.getElementById("attackButton").innerHTML = "Click to Repeat";
        if ((gameCurrentTeam === myTeam && gameBattleSection === "askRepeat")) {
            document.getElementById("attackButton").disabled = false;
        } else {
            document.getElementById("attackButton").disabled = true;
        }
        document.getElementById("attackButton").disabled = false;
        //TODO: move elements back to where they belong / used -> unused?
        document.getElementById("attackButton").onclick = function() { battleChangeSection("attack") };
        if ((gameCurrentTeam === myTeam && gameBattleSection === "askRepeat")) {
            document.getElementById("attackButton").disabled = false;
        } else {
            document.getElementById("attackButton").disabled = true;
        }
        document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };

        document.getElementById("actionPopupButton").disabled = true;
        document.getElementById("actionPopupButton").disabled = true;

    } else if (newSection === "none") {

        let phpBattleEnding = new XMLHttpRequest();
        phpBattleEnding.open("POST", "battleEnding.php?gameId=" + gameId, true);
        phpBattleEnding.send();

        document.getElementById("battleZonePopup").style.display = "none";
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Battle";
        document.getElementById("battle_button").onclick = function() { battleChangeSection("selectPos"); };
    }

    // alert("changing section");

    // alert(gameBattleSection);
    // alert(gameBattleSubSection);
    // alert(gameBattleLastMessage);
    // alert(gameBattleLastRoll);
    // alert(gameBattlePosSelected);

    let phpBattleUpdate = new XMLHttpRequest();
    phpBattleUpdate.open("POST", "battleUpdateAttributes.php?gameBattleSection=" + gameBattleSection + "&gameBattleSubSection=" + gameBattleSubSection + "&gameBattleLastRoll=" + gameBattleLastRoll + "&gameBattleLastMessage=" + gameBattleLastMessage + "&gameBattlePosSelected=" + gameBattlePosSelected, true);
    phpBattleUpdate.send();

    // alert("thing sent");
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
    clearSelectedPos();
    clearSelected();
    battleChangeSection("attack");
}

function battleSelectPosition() {
    if (document.getElementsByClassName("selectedPos").length === 0) {
        alert("didn't select a position")
    } else {
        gameBattlePosSelected = document.getElementsByClassName("selectedPos")[0].getAttribute("data-positionId");

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
}

function battlePieceClick(event, callingElement) {
    event.preventDefault();

    if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {
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
    }

    event.stopPropagation();
}

function battleEndRoll() {
    // alert("ending roll");
    gameBattleSubSection = "choosing_pieces";  //always defaults to this first
    document.getElementById("attackButton").disabled = true;

    let centerAttack = document.getElementById("center_attacker");
    let centerDefend = document.getElementById("center_defender");
    let usedAttack = document.getElementById("used_attacker");
    let usedDefend = document.getElementById("used_defender");
    let unusedAttack = document.getElementById("unused_attacker");
    let unusedDefend = document.getElementById("unused_defender");

    let centerAttackPiece = centerAttack.childNodes[0];
    let centerDefendPiece = centerDefend.childNodes[0];

    if (parseInt(centerAttackPiece.getAttribute("data-wasHit")) === 1) {
        let pieceId = centerAttackPiece.getAttribute("data-battlePieceId");
        document.querySelector("[data-placementId='" + pieceId + "']").remove();  //mainboard
        centerAttackPiece.remove();  //battlezone
        let phpPieceDelete = new XMLHttpRequest();
        phpPieceDelete.open("POST", "battlePieceUpdate.php?battlePieceId=" + pieceId + "&new_battlePieceState=9" + "&myTeam=" + myTeam + "&gameId=" + gameId, true);  // removes the element from the database
        phpPieceDelete.send();
    } else {
        let phpBattlePieceUpdate = new XMLHttpRequest();
        if (gameBattleSection === "attack") {
            centerAttack.removeChild(centerAttackPiece);
            usedAttack.appendChild(centerAttackPiece);
            centerAttackPiece.onclick = function() {  };
            phpBattlePieceUpdate.open("POST", "battlePieceUpdate.php?battlePieceId=" + centerAttackPiece.getAttribute("data-battlePieceId") + "&new_battlePieceState=3" + "&gameId=" + gameId, true);
            phpBattlePieceUpdate.send();
        } else {
            centerAttack.removeChild(centerAttackPiece);
            unusedAttack.appendChild(centerAttackPiece);
            phpBattlePieceUpdate.open("POST", "battlePieceUpdate.php?battlePieceId=" + centerAttackPiece.getAttribute("data-battlePieceId") + "&new_battlePieceState=1" + "&gameId=" + gameId, true);
            phpBattlePieceUpdate.send();
        }
    }

    if (parseInt(centerDefendPiece.getAttribute("data-wasHit")) === 1) {
        let pieceId = centerDefendPiece.getAttribute("data-battlePieceId");
        document.querySelector("[data-placementId='" + pieceId + "']").remove();  //mainboard
        centerDefendPiece.remove();  //battlezone
        let phpPieceDelete = new XMLHttpRequest();
        phpPieceDelete.open("POST", "battlePieceUpdate.php?battlePieceId=" + pieceId + "&new_battlePieceState=9" + "&myTeam=" + myTeam + "&gameId=" + gameId, true);  // removes the element from the database
        phpPieceDelete.send();
    } else {
        let phpBattlePieceUpdate = new XMLHttpRequest();
        if (gameBattleSection !== "attack") {
            centerDefend.removeChild(centerDefendPiece);
            usedDefend.appendChild(centerDefendPiece);
            centerDefendPiece.onclick = function() {  };
            phpBattlePieceUpdate.open("POST", "battlePieceUpdate.php?battlePieceId=" + centerDefendPiece.getAttribute("data-battlePieceId") + "&new_battlePieceState=4" + "&gameId=" + gameId, true);
            phpBattlePieceUpdate.send();
        } else {
            centerDefend.removeChild(centerDefendPiece);
            unusedDefend.appendChild(centerDefendPiece);
            phpBattlePieceUpdate.open("POST", "battlePieceUpdate.php?battlePieceId=" + centerDefendPiece.getAttribute("data-battlePieceId") + "&new_battlePieceState=2" + "&gameId=" + gameId, true);
            phpBattlePieceUpdate.send();
        }
    }

    battleChangeSection(gameBattleSection);
    document.getElementById("battleActionPopup").style.display = "none";
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
                actionButton.disabled = true;
                actionButton.innerHTML = "HIT! Roll for Defense Bonus";
                actionButton.onclick = function() { battleAttackCenter("defend"); };
            } else if (new_gameBattleSubSection === "continue_choosing" && gameBattleSection === "attack") {
                if (myTeam === gameCurrentTeam) {
                    actionButton.disabled = false;
                } else {
                    actionButton.disabled = true;
                }

                actionButton.innerHTML = "click to go back to Choosing";
                actionButton.onclick = function() { battleEndRoll(); };
            } else if (new_gameBattleSubSection === "continue_choosing" && gameBattleSection === "counter") {
                if (myTeam === gameCurrentTeam) {
                    actionButton.disabled = true;
                } else {
                    actionButton.disabled = false;
                }

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


let updateWait;
let waitTime = 50;

function waitForUpdate() {
    let phpUpdateBoard = new XMLHttpRequest();
    phpUpdateBoard.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            // alert(this.responseText);
            let decoded = JSON.parse(this.responseText);

            if (decoded.updateType === "pieceMove") {
                updatePieceMove(decoded.updatePlacementId, decoded.updateNewPositionId, decoded.updateNewContainerId);
            } else if (decoded.updateType === "pieceDelete") {
                updatePieceDelete(decoded.updatePlacementId);
            } else if (decoded.updateType === "pieceTrash") {
                updatePieceTrash(decoded.updatePlacementId);
            } else if (decoded.updateType === "piecePurchase") {
                updatePiecePurchase(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewUnitId));
            } else if (decoded.updateType === "battlePieceMove") {
                updateBattlePieceMove(parseInt(decoded.updatePlacementId), decoded.updateBattlePieceState);
            } else if (decoded.updateType === "phaseChange") {
                updateNextPhase();
            } else if (decoded.updateType === "positionSelected") {
                updateBattlePositionSelected(decoded.updateBattlePositionSelectedPieces);
            } else if (decoded.updateType === "piecesSelected") {
                updateBattlePiecesSelected(decoded.updateBattlePiecesSelected);
            } else if (decoded.updateType === "battleAttacked") {
                updateBattleAttack();
            } else if (decoded.updateType === "battleEnding") {
                updateBattleEnding();
            } else if (decoded.updateType === "battleSectionChange") {
                updateBattleSection();
            }

            updateWait = window.setTimeout("waitForUpdate()", waitTime);
        }
    };
    phpUpdateBoard.open("GET", "updateBoard.php?gameId=" + gameId + "&myTeam=" + myTeam, true);  // removes the element from the database
    phpUpdateBoard.send();
}

function updateBattlePieceMove(battlePieceId, battlePieceState) {
    // alert(battlePieceId);
    // alert(battlePieceState);
    let battlePiece = document.querySelector("[data-battlePieceId='" + battlePieceId + "']");
    document.querySelector("[data-boxId='" + battlePieceState + "']").appendChild(battlePiece);
}

function updatePiecePurchase(placementId, unitId) {
    // alert("purchasing");
    let purchaseContainer = document.getElementById("purchased_container");
    let notMyTeam;
    if (myTeam === "Red") {
        notMyTeam = "Blue";
    } else {
        notMyTeam = "Red";
    }
    let echoString = "";
    echoString += "<div class='" + unitNames[unitId] + " gamePiece " + notMyTeam + "' data-placementId='" + placementId + "' data-placementBattleUsed='0' data-placementCurrentMoves='" + unitsMoves[unitId] + "' data-placementContainerId='999999' data-placementTeamId='" + notMyTeam + "' data-unitName='" + unitNames[unitId] + "' data-unitId='" + unitId + "' draggable='true' ondragstart='pieceDragstart(event, this)' onclick='pieceClick(event, this);' ondragenter='pieceDragenter(event, this);' ondragleave='pieceDragleave(event, this);'>";
    if (unitNames[unitId] === "transport" || unitNames[unitId] === "aircraftCarrier") {
        let classthing;
        if (unitNames[unitId] === "transport") {
            classthing = "transportContainer";
        } else {
            classthing = "aircraftCarrierContainer";
        }
        echoString += "<div class='" + classthing + " " + notMyTeam + "' data-containerPopped='false' data-positionContainerId='" + placementId + "' data-positionType='" + classthing + "' data-positionId='118' ondragleave='containerDragleave(event, this);'  ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);'></div>";
    }
    echoString += "</div>";  // end the overall piece
    purchaseContainer.innerHTML += echoString;
}

function updatePieceMove(placementId, newPositionId, newContainerId){
    // alert(placementId);
    // alert(newPositionId);
    // alert(newContainerId);
    let pieceToMove = document.querySelector("[data-placementId='" + placementId + "']");
    let theContainer;
    if (newContainerId !== "999999") {
        theContainer = document.querySelector("[data-placementId='" + newContainerId + "']").firstChild;
    } else {
        theContainer = document.querySelector("[data-positionId='" + newPositionId + "']");
    }
    // alert(theContainer);
    theContainer.appendChild(pieceToMove);
    // theContainer.append
}

function updatePieceDelete(placementId) {
    document.querySelector("[data-placementId='" + placementId + "']").remove();  //mainboard
    document.querySelector("[data-battlePieceId='" + placementId + "']").remove();  //battlezone
}

function updatePieceTrash(placementId) {
    document.querySelector("[data-placementId='" + placementId + "']").remove();  //mainboard
}

function updateNextPhase() {
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
            if (canUndo === "true") {
                document.getElementById("undo_button").disabled = false;
            } else {
                document.getElementById("undo_button").disabled = true;
            }
            if (canNextPhase === "true") {
                document.getElementById("phase_button").disabled = false;
            } else {
                document.getElementById("phase_button").disabled = true;
            }
            if (gamePhase === "1") {
                // alert("phase1");
                //TODO: phase effects here and grab phase stuff???
                document.getElementById("newsPopup").style.display = "block";
            } else {
                // alert("not phase 1");
                document.getElementById("newsPopup").style.display = "none";
            }
            document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase - 1];
            document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
        }
    };
    phpPhaseChange.open("GET", "updateGetPhase.php", true);  // removes the element from the database
    phpPhaseChange.send();
}

function updateBattleAttack() {

    //get everything from database again (subsection / lastroll / lastmessage)
    //display and make buttons disabled or not based upon the team or current team

    let phpBattleUpdate = new XMLHttpRequest();
    phpBattleUpdate.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            // alert(this.responseText);
            let decoded = JSON.parse(this.responseText);
            gameBattleSection = decoded.gameBattleSection;
            gameBattleSubSection = decoded.gameBattleSubSection;
            gameBattleLastRoll = decoded.gameBattleLastRoll;
            gameBattleLastMessage = decoded.gameBattleLastMessage;

            document.getElementById("battleActionPopup").style.display = "block";
            if (gameBattleSubSection === "defense_bonus") {
                document.getElementById("actionPopupButton").disabled = false;
                document.getElementById("actionPopupButton").innerHTML = "HIT! Roll for Defense Bonus";
                document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
            } else if (gameBattleSubSection === "continue_choosing") {
                document.getElementById("actionPopupButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
                document.getElementById("actionPopupButton").onclick = function() { battleEndRoll(); };
            }

        }
    };
    phpBattleUpdate.open("GET", "updateGetBattle.php", true);  // removes the element from the database
    phpBattleUpdate.send();

}

function updateBattleEnding() {
    //mostly graphical stuff for end, next battle is completely re-do the innerhtml for stuff anyways
    document.getElementById("battleZonePopup").style.display = "none";
}

function updateBattlePositionSelected(positionPiecesHTML) {
    document.getElementById("unused_defender").innerHTML = positionPiecesHTML;
}

function updateBattlePiecesSelected(piecesSelectedHTML) {
    document.getElementById("unused_attacker").innerHTML = piecesSelectedHTML;

    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    hideContainers("lavContainer");
    clearHighlighted();
    clearSelectedPos();
    clearSelected();

    document.getElementById("battleZonePopup").style.display = "block";
    document.getElementById("attackButton").innerHTML = "Attack section";  //already disabled by default?
    document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };
    document.getElementById("changeSectionButton").innerHTML = "Click to Counter";

    document.getElementById("changeSectionButton").disabled = true;  //other client can't change section, only currentTeam

    document.getElementById("changeSectionButton").onclick = function() {
        battleChangeSection("counter");
        let newParent = document.getElementById('unused_attacker');
        let oldParent = document.getElementById('used_attacker');
        while (oldParent.childNodes.length > 0) {
            oldParent.childNodes[0].onclick = function() { battlePieceClick(event, this); };
            let phpMoveBattlePiece = new XMLHttpRequest();
            phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + oldParent.childNodes[0].getAttribute("data-battlePieceId") + "&new_battlePieceState=1", true);
            phpMoveBattlePiece.send();
            newParent.appendChild(oldParent.childNodes[0]);
        }
        newParent = document.getElementById('unused_defender');
        oldParent = document.getElementById('used_defender');
        while (oldParent.childNodes.length > 0) {
            oldParent.childNodes[0].onclick = function() { battlePieceClick(event, this); };
            let phpMoveBattlePiece = new XMLHttpRequest();
            phpMoveBattlePiece.open("POST", "battlePieceUpdate.php?battlePieceId=" + oldParent.childNodes[0].getAttribute("data-battlePieceId") + "&new_battlePieceState=2", true);
            phpMoveBattlePiece.send();
            newParent.appendChild(oldParent.childNodes[0]);
        }
    };

}

function updateBattleSection() {
    // alert("update battle section");
    let phpBattleUpdate = new XMLHttpRequest();
    phpBattleUpdate.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            // alert(this.responseText);
            let decoded = JSON.parse(this.responseText);
            gameBattleSection = decoded.gameBattleSection;
            gameBattleSubSection = decoded.gameBattleSubSection;
            gameBattleLastRoll = decoded.gameBattleLastRoll;
            gameBattleLastMessage = decoded.gameBattleLastMessage;


            if (gameBattleSubSection !== "choosing_pieces") {
                document.getElementById("battleActionPopup").style.display = "block";
                if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "attack") {
                    if (myTeam !== gameCurrentTeam) {
                        document.getElementById("actionPopupButton").disabled = false;
                    } else {
                        document.getElementById("actionPopupButton").disabled = true;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "click to roll for defense bonus";
                    document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
                } else if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "counter") {
                    if (myTeam !== gameCurrentTeam) {
                        document.getElementById("actionPopupButton").disabled = true;
                    } else {
                        document.getElementById("actionPopupButton").disabled = false;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "click to roll for defense bonus";
                    document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
                } else if (gameBattleSubSection === "continue_choosing" && gameBattleSection === "attack") {
                    if (myTeam !== gameCurrentTeam) {
                        document.getElementById("actionPopupButton").disabled = true;
                    } else {
                        document.getElementById("actionPopupButton").disabled = false;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
                    document.getElementById("actionPopupButton").onclick = function() { battleEndRoll(); };
                } else if (gameBattleSubSection === "continue_choosing" && gameBattleSection === "counter") {
                    if (myTeam !== gameCurrentTeam) {
                        document.getElementById("actionPopupButton").disabled = false;
                    } else {
                        document.getElementById("actionPopupButton").disabled = true;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
                    document.getElementById("actionPopupButton").onclick = function() { battleEndRoll(); };
                }
            } else {
                document.getElementById("battleActionPopup").style.display = "none";
            }








            if (gameBattleSection === "attack") {
                document.getElementById("attackButton").innerHTML = "Attack section";
                document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };
                document.getElementById("changeSectionButton").innerHTML = "Click to Counter";
                document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("counter") };
            } else if (gameBattleSection === "counter") {
                document.getElementById("attackButton").innerHTML = "Counter Attack";
                document.getElementById("attackButton").onclick = function() { battleAttackCenter("defend"); };
                document.getElementById("changeSectionButton").innerHTML = "Click End Counter";
                document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("askRepeat") };
            } else if (gameBattleSection === "askRepeat") {
                document.getElementById("attackButton").innerHTML = "Click to Repeat";
                document.getElementById("attackButton").onclick = function() { battleChangeSection("attack") };
                document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
                document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };
            }

            if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {

                if (document.getElementById("center_defender").childNodes.length === 1 && document.getElementById("center_attacker").childNodes.length === 1) {
                    document.getElementById("attackButton").disabled = false;
                } else {
                    document.getElementById("attackButton").disabled = true;
                }


                document.getElementById("changeSectionButton").disabled = false;
            }

            if (gameBattleSection === "askRepeat" && myTeam === gameCurrentTeam) {
                document.getElementById("attackButton").disabled = false;
                document.getElementById("changeSectionButton").disabled = false;
            } else if (gameBattleSection === "askRepeat" && myTeam !== gameCurrentTeam) {
                document.getElementById("attackButton").disabled = true;
                document.getElementById("changeSectionButton").disabled = true;
            }

            if (gameBattleSection === "none") {
                document.getElementById("battleZonePopup").style.display = "none";
            }
        }
    };
    phpBattleUpdate.open("GET", "updateGetBattle.php", true);  // removes the element from the database
    phpBattleUpdate.send();
}





waitForUpdate();