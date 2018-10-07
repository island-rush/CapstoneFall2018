//Javascript Functions used by the Island Rush Game
//Created by C1C Spencer Adolph (7/28/2018)

let islandTimer;
let pieceTimer;

//First function called to load the game...
function bodyLoader() {

    if (gameBattlePosSelected != "999999") {
        let phpPositionGet = new XMLHttpRequest();
        phpPositionGet.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                // alert(this.responseText);
                let decoded = JSON.parse(this.responseText);
                gameBattleAdjacentArray = decoded.adjacentArray;
            }
        };
        phpPositionGet.open("POST", "battleGetAdjacentPos.php?positionSelected=" + gameBattlePosSelected, true);
        phpPositionGet.send();
    }




    // alert(myTeam);
    document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase-1];
    // document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
    if (gameCurrentTeam === "Red") {
        //red highlight
        document.getElementById("red_team_indicator").classList.add("highlightedTeam");
        //blue unhighlight
        document.getElementById("blue_team_indicator").classList.remove("highlightedTeam");
    } else {
        //blue highlight
        document.getElementById("blue_team_indicator").classList.add("highlightedTeam");
        //red unhighlight
        document.getElementById("red_team_indicator").classList.remove("highlightedTeam");
    }

    document.getElementById("red_rPoints_indicator").innerHTML = gameRedRpoints;
    document.getElementById("blue_rPoints_indicator").innerHTML = gameBlueRpoints;

    document.getElementById("red_hPoints_indicator").innerHTML = gameRedHpoints;
    document.getElementById("blue_hPoints_indicator").innerHTML = gameBlueHpoints;



    //TODO: change this to be team specific (based on if I am the current team or not) (reorganize / refactor)(or is this already done with canAttack?)
    if (gameBattleSection !== "none" && gameBattleSection !== "selectPos" && gameBattleSection !== "selectPieces") {
        document.getElementById("battleZonePopup").style.display = "block";
    }

    if (gameBattleSection === "none") {
        document.getElementById("phase_button").disabled = false;
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Battle";
        document.getElementById("battle_button").onclick = function() {
            if (confirm("Are you sure you want to battle?")) {
                battleChangeSection("selectPos");
            }
        };
    } else if (gameBattleSection === "selectPos") {
        document.getElementById("phase_button").disabled = true;
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Pieces";
        document.getElementById("battle_button").onclick = function() { battleSelectPosition(); };
        userFeedback("Now click on the zone that you want to attack. Then click the Select Pieces button, where the Battle button used to be.");
        //more visual indication of selecting position
        document.getElementById("whole_game").style.backgroundColor = "yellow";
    } else if (gameBattleSection === "selectPieces") {
        document.getElementById("phase_button").disabled = true;
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Start Battle";
        document.getElementById("battle_button").onclick = function() { battleSelectPieces(); };
        document.getElementById("whole_game").style.backgroundColor = "yellow";
        document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").classList.add("selectedPos");

        userFeedback("Select the pieces you want to attack with. They must be adjacent to the zone being attacked. Then Start the Battle!");
    } else {
        userFeedback("Disable phase button?");
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
            document.getElementById("actionPopupButton").innerHTML = "HIT!! The Defender's unit was destroyed!! The Defender " +
                "has the opportunity to knock out the Attacker's unit. Defender: roll for defense bonus!";
            document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
        } else if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "counter") {
            if (myTeam !== gameCurrentTeam) {
                document.getElementById("actionPopupButton").disabled = true;
            } else {
                document.getElementById("actionPopupButton").disabled = false;
            }
            document.getElementById("actionPopupButton").innerHTML = "HIT!! The Defender's unit was destroyed!! The Defender " +
                "has the opportunity to knock out the Attacker's unit. Defender: roll for defense bonus!";
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
        if (gameBattleSection !== "attack" && gameBattleSection !== "counter" && gameBattleSection !== "askRepeat") {
            document.getElementById("battle_button").disabled = false;
        } else {
            document.getElementById("battle_button").disabled = true;
        }
    } else {
        document.getElementById("battle_button").disabled = true;
    }
    if (canUndo === "true") {
        if (gameBattleSection === "none") {
            document.getElementById("undo_button").disabled = false;
        }
    } else {
        document.getElementById("undo_button").disabled = true;
    }
    if (canNextPhase === "true") {
        if (gameBattleSection === "none") {
            document.getElementById("phase_button").disabled = false;
        } else {
            document.getElementById("phase_button").disabled = true;
        }
    } else {
        document.getElementById("phase_button").disabled = true;
    }
    // NEWS ALERT PHASE
    if (gamePhase === "1") {
        // Show popup with News Alert body, hide Hybrid body
        // TODO: this isn't always defaulted to news, the popup may be other titles onload -set by phase tho so this is fine for now
        document.getElementById("popupTitle").innerHTML = "News Alert";
        document.getElementById("newsBodyText").innerHTML = newsText;
        document.getElementById("newsBodySubText").innerHTML = newsEffectText;
        document.getElementById("popupBodyNews").style.display = "block";
        document.getElementById("popupBodyHybrid").style.display = "none";
        document.getElementById("popup").style.display = "block";
        userFeedback("Click Next Phase to advance to next phase.");
    } else {
        // Hide the popup because it shouldnt be showing.
        document.getElementById("popup").style.display = "none";
    }
    // HYBRID WAR PHASE
    if (gamePhase === "6") {
        // if they refresh, close the popup. they can press button again
        document.getElementById("popup").style.display = "none";
        //convert the battle button to be a hybrid warfare shop button
        document.getElementById("battle_button").innerHTML = "Hybrid Warfare";
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").onclick =function () {
            document.getElementById("popupTitle").innerHTML = "Hybrid Warfare Tool";
            document.getElementById("popupBodyNews").style.display = "none";
            document.getElementById("popupBodyHybrid").style.display = "block";
            document.getElementById("setRedRpoints").value = gameRedRpoints;
            document.getElementById("setRedHpoints").value = gameRedHpoints;
            document.getElementById("setBlueRpoints").value = gameBlueRpoints;
            document.getElementById("setBlueHpoints").value = gameBlueHpoints;
            document.getElementById("popup").style.display = "block";
        };

    }

    if (document.getElementById("battleActionPopup").style.display == "block") {
        showDice(gameBattleLastRoll);
    }
    //access the battle popup
    //change the dice image to the last roll

}

//TODO: disable sidepanel buttons during a battle!
//---------------------------------------------------
function pieceClick(event, callingElement) {
    // alert("clicked");
    event.preventDefault();
    //open container if applicable
    if (gameBattleSection === "selectPieces") {
        if (callingElement.getAttribute("data-placementTeamId") === myTeam) {
            if (gameBattleAdjacentArray.includes(parseInt(callingElement.parentNode.getAttribute("data-positionId")))) {
                if (callingElement.classList.contains("selected")) {
                    callingElement.classList.remove("selected");
                } else {
                    if (callingElement.getAttribute("data-placementBattleUsed") == 0) {
                        callingElement.classList.add("selected");
                    }
                }
            }
        }
    } else {
        if (gameBattleSection === "selectPos") {
            clearSelectedPos();
            callingElement.parentNode.classList.add("selectedPos");
        } else {
            let unitName = callingElement.getAttribute("data-unitName");
            if (unitName === "transport" || unitName === "aircraftCarrier") {
                hideContainers("transportContainer");
                hideContainers("aircraftCarrierContainer");
                if (callingElement.parentNode.getAttribute("data-positionId") !== "118") {
                    callingElement.childNodes[0].style.display = "block";
                    callingElement.style.zIndex = 30;
                    callingElement.parentNode.style.zIndex = 70;
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
    userFeedback("drag the piece around and hover over an island to place onto it.");
    //canMove is dictated by phase and current Team
    if ((canMove === "true") && callingElement.getAttribute("data-placementTeamId") === myTeam && gameBattleSection === "none") {
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
    // alert("dragleave");
    event.preventDefault();
    if (callingElement.getAttribute("data-unitName") === "transport" || callingElement.getAttribute("data-unitName") === "aircraftCarrier") {
        // alert("was container");
        // alert(callingElement.childNodes[0].getAttribute("data-containerPopped"));
        if (callingElement.childNodes[0].getAttribute("data-containerPopped") == "false") {
            // alert("clear timeout container not popped");
            clearTimeout(pieceTimer);
        }
    }
    event.stopPropagation();
}

function pieceDragenter(event, callingElement) {
    event.preventDefault();
    clearTimeout(pieceTimer);
    let unitName = callingElement.getAttribute("data-unitName");
    if (unitName === "transport" || unitName === "aircraftCarrier") {
        //only dragenter to open up container pieces
        if (callingElement.parentNode.getAttribute("data-positionId") !== "118") {
            clearTimeout(pieceTimer);
            pieceTimer = setTimeout(function() { pieceClick(event, callingElement);}, 1000);
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

            if (myTeam === "Red") {
                gameRedRpoints = gameRedRpoints - costOfPiece;
                document.getElementById("red_rPoints_indicator").innerHTML = gameRedRpoints;
            } else {
                gameBlueRpoints = gameBlueRpoints - costOfPiece;
                document.getElementById("blue_rPoints_indicator").innerHTML = gameBlueRpoints;
            }

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
        userFeedback("Move undone.");
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

            if (myTeam === "Red") {
                gameRedRpoints = gameRedRpoints + costOfPiece;
                document.getElementById("red_rPoints_indicator").innerHTML = gameRedRpoints;
            } else {
                gameBlueRpoints = gameBlueRpoints + costOfPiece;
                document.getElementById("blue_rPoints_indicator").innerHTML = gameBlueRpoints;
            }

            let phpTrashRequest = new XMLHttpRequest();
            phpTrashRequest.open("POST", "pieceTrash.php?placementId=" + placementId + "&myTeam=" + myTeam + "&gameId=" + gameId + "&newPoints=" + myPoints, true);
            phpTrashRequest.send();
        }
    }
    userFeedback("Piece trashed. Reinforcement Points refunded");
}

function containerDragleave(event, callingElement) {
    event.preventDefault();
    clearTimeout(pieceTimer);
    pieceTimer = setTimeout(function() { waterClick(event, callingElement);}, 1000);
    event.stopPropagation();
}

function containerDragenter(event, callingElement) {
    event.preventDefault();
    clearTimeout(pieceTimer);
    event.stopPropagation();
}


//TODO: this function now obsolete with movement check, needs to be removed
function containerHasSpotOpen(new_placementContainerId, unitName) {
    //Can't put transport inside another transport
    if (new_placementContainerId !== "999999") {
        if (unitName === "transport" || unitName === "aircraftCarrier") {
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
        s[r].parentNode.parentNode.style.zIndex = 10;
        s[r].setAttribute("data-containerPopped", "false");
    }
}
//---------------------------------------------------


function islandClick(event, callingElement) {
    event.preventDefault();
    hideIslands();  //only 1 island visible at a time
    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
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
    clearTimeout(islandTimer);
    islandTimer = setTimeout(function() { islandClick(event, callingElement);}, 1000);
    event.stopPropagation();
}

function islandDragleave(event, callingElement) {
    event.preventDefault();
    if (callingElement.getAttribute("data-islandPopped") === "false") {
        clearTimeout(islandTimer);
    }
    event.stopPropagation();
}

function popupDragleave(event, callingElement) {
    event.preventDefault();
    clearTimeout(islandTimer);
    islandTimer = setTimeout(function() { hideIslands();}, 1000);
    event.stopPropagation();
}

function popupDragOver(event, callingElement) {
    event.preventDefault();
    clearTimeout(islandTimer);
    event.stopPropagation();
}

function popupDragEnter(event, callingElement) {
    event.preventDefault();
    clearTimeout(islandTimer);
    event.stopPropagation();
}

function landDragLeave(event, callingElement) {
    event.preventDefault();
    clearTimeout(islandTimer);
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

    if (gameBattleSection === "selectPos" && gameCurrentTeam == myTeam) {
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
    clearHighlighted();
    event.stopPropagation();
}

function waterClick(event, callingElement) {
    event.preventDefault();
    hideIslands();
    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    clearHighlighted();
    if (gameBattleSection === "selectPos" && gameCurrentTeam == myTeam) {
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

    let islandFrom;
    let islandTo;
    //need to know which island number it is going to / came from
    if (old_positionId <= 54) {
        islandFrom = 0;
    } else if (old_positionId == 118) {
        islandFrom = -4;  //this represents the purchase container thingy (need to deal with this somehow)
    } else {
        islandFrom = document.querySelector("[data-positionId='" + old_positionId + "']").parentNode.getAttribute("data-islandNum");
    }
    if (new_positionId <= 54) {
        islandTo = 0;
    } else if (new_positionId == 118) {
        islandTo = -4;  //this represents the purchase container thingy (need to deal with this somehow)
    } else {
        islandTo = document.querySelector("[data-positionId='" + new_positionId + "']").parentNode.getAttribute("data-islandNum");
    }
    //another check
    if ((old_positionId != "118" && gamePhase != 5) || (old_positionId == "118" && gamePhase == 5)) {
        if (movementCheck(unitName, unitTerrain, new_placementContainerId, positionType) === true) {
            let phpMoveCheck = new XMLHttpRequest();
            phpMoveCheck.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    // alert(this.responseText);
                    let movementCost = parseInt(this.responseText);
                    if (movementCost >= 0) {
                        if ((new_placementContainerId !== "999999" && containerHasSpotOpen(new_placementContainerId, unitName) === "true") || new_placementContainerId === "999999") {
                            //MANY OTHER CHECKS FOR MOVEMENT CAN HAPPEN HERE, JUST NEST MORE FUNCTIONS (see above)
                            let new_placementCurrentMoves = old_placementCurrentMoves - movementCost;

                            //Update the html by moving the piece and changing the piece's attributes
                            newContainerElement.appendChild(pieceDropped);
                            pieceDropped.setAttribute("data-placementCurrentMoves", new_placementCurrentMoves.toString());
                            pieceDropped.setAttribute("data-placementContainerId", new_placementContainerId);
                            if (unitName === "transport" || unitName === "aircraftCarrier") {
                                pieceDropped.firstChild.setAttribute("data-positionId", newContainerElement.getAttribute("data-positionId"));
                            }

                            // title='".$unitName2."&#013;Moves: ".$placementCurrentMoves2."'
                            pieceDropped.setAttribute("title", unitName + "\n" +
                                "Moves: " + new_placementCurrentMoves);

                            //Update the placement in the database and add a movement to the database
                            let phpRequest = new XMLHttpRequest();
                            phpRequest.open("POST", "pieceMove.php?gameId=" + gameId + "&myTeam=" + myTeam + "&gameTurn=" + gameTurn + "&gamePhase=" + gamePhase + "&placementId=" + placementId + "&unitName=" + unitName + "&new_positionId=" + new_positionId + "&old_positionId=" + old_positionId + "&movementCost=" + movementCost  + "&new_placementCurrentMoves=" + new_placementCurrentMoves + "&old_placementContainerId=" + old_placementContainerId + "&new_placementContainerId=" + new_placementContainerId, true);
                            phpRequest.send();



                            let flagPositions = [55, 65, 75, 79, 83, 86, 90, 94, 97, 100, 103, 107, 111, 114];
                            let containerElement;
                            if (flagPositions.includes(parseInt(new_positionId)) || flagPositions.includes(parseInt(new_positionId))) {
                                if (flagPositions.includes(parseInt(new_positionId))) {
                                    containerElement = newContainerElement;
                                } else {
                                    containerElement = document.querySelector("[data-positionId='" + old_positionId + "']");
                                }
                                let parentTeam = containerElement.parentNode.classList[2];
                                let newTeam;
                                if (parentTeam === "Red") {
                                    newTeam = "Blue";
                                } else {
                                    newTeam = "Red";
                                }
                                let changeOwnership = "true";
                                let numChildren = containerElement.childElementCount;
                                if (numChildren === 0) {
                                    changeOwnership = "false";
                                }
                                for (let x = 0; x < numChildren; x++) {
                                    if (containerElement.childNodes[x].getAttribute("data-placementTeamId") === parentTeam) {
                                        changeOwnership = "false";
                                    }
                                }
                                if (changeOwnership === "true") {
                                    //TODO: could refactor and use ajax function combined for less duplication
                                    //change css of parent
                                    let parent = containerElement.parentNode;
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
                    } else{
                        // Piece was out of moves
                        if (movementCost == -2) {
                            userFeedback("News alert prevented that!");
                        } else {
                            userFeedback("This piece is out of moves!");
                        }

                    }
                }
            };
            phpMoveCheck.open("POST", "pieceMoveValid.php?new_positionId=" + new_positionId + "&old_placementContainerId=" + old_placementContainerId + "&new_placementContainerId=" + new_placementContainerId + "&old_positionId=" + old_positionId + "&placementId=" + placementId + "&islandFrom=" + islandFrom + "&islandTo=" + islandTo + "&unitName=" + unitName, true);
            phpMoveCheck.send();
        } else {
            // alert("failed move check");
            //TODO: user feedback here?
        }
    } else{
        // Cannot move this piece? (not sure if this is necessary since we disable pieces that shouldn't move..)
        userFeedback("Cannot move this piece.");
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
    islandTimer = setTimeout(function() { hideIslands();}, 1000)
}

function movementCheck(unitName, unitTerrain, new_placementContainerId, positionTerrain) {
    if (new_placementContainerId != "999999") {
        let containerParent = document.querySelector("[data-placementId='" + new_placementContainerId + "']");
        if (containerParent.getAttribute("data-unitName") === "transport") {
            let listPeople = ["marine", "soldier"];
            let listMachines = ["tank", "lav", "attackHeli", "sam", "artillery"];
            if (!listPeople.includes(unitName) && !listMachines.includes(unitName)) {
                return false;  //piece does not belong in transport container
            }
            if (containerParent.childNodes[0].childNodes.length === 0) {
                return true;  //valid piece can always go into empty transport
            }
            if (containerParent.childNodes[0].childNodes.length === 3) {
                return false;  //already full of soldiers (max number)
            }
            if (listPeople.includes(unitName)) {  //piece dropping in is a person
                if (containerParent.childNodes[0].childNodes.length === 2) {  //both were people, allow a 3rd person
                    return listPeople.includes(containerParent.childNodes[0].childNodes[0].getAttribute("data-unitName"))
                        && listPeople.includes(containerParent.childNodes[0].childNodes[1].getAttribute("data-unitName"));
                }
                return true;  //person dropping into transport with 1 piece in it (always allowed)
            } else {
                //machine can drop in with a single person, can't drop into a transport with 2 pieces inside
                return    (containerParent.childNodes[0].childNodes.length === 1
                        && listPeople.includes(containerParent.childNodes[0].childNodes[0].getAttribute("data-unitName")));
            }
        } else {  //not transport -> must be aircraftCarrier
            return unitName === "fighter" && containerParent.childNodes[0].childNodes.length < 2;  // room for another fighter
        }
    } else {  //wasn't a container
        return unitTerrain === "air" || unitTerrain === positionTerrain; //air anywhere, or match terrain
    }
}

function changePhase() {
    if (canNextPhase === "true") {
        if ((gamePhase == 4 && confirm("Any aircraft not on carriers/airstrips or heli's not over land will get deleted.\nAre you sure you want to continue?")) || gamePhase != 4) {
            let phpPhaseChange = new XMLHttpRequest();
            phpPhaseChange.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    // TODO: COMMENT WHAT IS HAPPENING HERE
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

                    newsEffect = decoded.newsEffect;
                    newsText = decoded.newsText;
                    newsEffectText = decoded.newsEffectText;
                    //fix these for refactor

                    let phaseText = decoded.phaseText;
                    //change to another part of the popup
                    // document.getElementById("newsBodyText").innerHTML = newsText;
                    // document.getElementById("newsBodySubText").innerHTML = newsEffectText;
                    // document.getElementById("newsText").innerHTML = phaseText;
                    // userFeedback(phaseText);

                    //Dont get these because these aren't update on phase (yet)
                    gameRedRpoints = decoded.gameRedRpoints;
                    gameBlueRpoints = decoded.gameBlueRpoints;
                    gameRedHpoints = decoded.gameRedHpoints;
                    gameBlueHpoints = decoded.gameBlueHpoints;
                    document.getElementById("red_rPoints_indicator").innerHTML = gameRedRpoints;
                    document.getElementById("blue_rPoints_indicator").innerHTML = gameBlueRpoints;
                    document.getElementById("red_hPoints_indicator").innerHTML = gameRedHpoints;
                    document.getElementById("blue_hPoints_indicator").innerHTML = gameBlueHpoints;


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

                    //SETTING THE CURRENT PHASE AND CURRENT TEAM DISPLAY
                    document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase - 1];
                    // document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
                    if (gameCurrentTeam === "Red") {
                        //red highlight
                        document.getElementById("red_team_indicator").classList.add("highlightedTeam");
                        //blue unhighlight
                        document.getElementById("blue_team_indicator").classList.remove("highlightedTeam");
                    } else {
                        //blue highlight
                        document.getElementById("blue_team_indicator").classList.add("highlightedTeam");
                        //red unhighlight
                        document.getElementById("red_team_indicator").classList.remove("highlightedTeam");
                    }
                    // NEWS ALERT PHASE
                    if (gamePhase === "1") {
                        document.getElementById("popupBodyHybrid").style.display = "none";
                        document.getElementById("popupBodyNews").style.display = "block";
                        document.getElementById("newsBodyText").innerHTML = newsText;
                        document.getElementById("newsBodySubText").innerHTML = newsEffectText;
                        document.getElementById("popup").style.display = "block";
                        userFeedback(phaseText); //tell the user what happened int the news alert ( rollDie )
                    } else {
                        document.getElementById("popup").style.display = "none";
                    }

                    // HYBRID WAR PHASE
                    if (gamePhase === "6") {
                        //convert the battle button to be a hybrid warfare shop button
                        document.getElementById("battle_button").innerHTML = "Hybrid Warfare";
                        document.getElementById("battle_button").disabled = false;
                        document.getElementById("battle_button").onclick =function () {
                            document.getElementById("hybridSubmitPoints").value = "Submit new Point Values";
                            document.getElementById("popupBodyNews").style.display = "none";
                            document.getElementById("popupBodyHybrid").style.display = "block";
                            document.getElementById("setRedRpoints").value = gameRedRpoints;
                            document.getElementById("setRedHpoints").value = gameRedHpoints;
                            document.getElementById("setBlueRpoints").value = gameBlueRpoints;
                            document.getElementById("setBlueHpoints").value = gameBlueHpoints;
                            document.getElementById("popup").style.display = "block";
                        };
                    }else{
                        // not hybrid, should be the battle button
                        //Let the canAttack check above enable or disable battle button, but set the html stuff back
                        document.getElementById("battle_button").innerHTML = "Select Battle";
                        document.getElementById("battle_button").onclick = function() {
                            if (confirm("Are you sure you want to battle?")) {
                                battleChangeSection("selectPos");
                            }
                        };
                    }

                    if (gamePhase === "7") { // TALLY POINTS/ROUND RECAP
                        userFeedback("Click next phase to advance to the other player's turn.");
                        let allPieces = document.querySelectorAll("[data-placementTeamId='" + myTeam + "']");
                        for (let x = 0; x < allPieces.length; x++) {
                            let currentPiece = allPieces[x];
                            let unitName = currentPiece.getAttribute("data-unitName");
                            let newMoves = unitsMoves[unitName];
                            currentPiece.setAttribute("data-placementCurrentMoves", newMoves);
                            currentPiece.setAttribute("data-placementBattleUsed", "0")
                        }
                    }

                }
            };
            phpPhaseChange.open("GET", "gamePhaseChange.php", true);  // removes the element from the database
            phpPhaseChange.send();
        }
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
        document.getElementById("phase_button").disabled = true;
        document.getElementById("undo_button").disabled = true;

        document.getElementById("battle_button").onclick = function() { battleSelectPosition(); };
        document.getElementById("battle_button").innerHTML = "Select Pieces";

        userFeedback("Now click on the zone that you want to attack. Then click the Select Pieces button, where the Battle button used to be.");
        //more visual indication of selecting position
        document.getElementById("whole_game").style.backgroundColor = "yellow";
    } else if (newSection === "selectPieces") {
        document.getElementById("battle_button").onclick = function() { battleSelectPieces(); };
        document.getElementById("battle_button").innerHTML = "Start Battle";

        gameBattleTurn = 0;

        userFeedback("Select the pieces you want to attack with. They must be adjacent to the zone being attacked. Then Start the Battle!");
        //more visual indication of selecting pieces
    } else if (newSection === "attack") {
        userFeedback("Attack the enemy by clicking on their unit you want to attack & the unit you want to attack with. Rememeber: attacker is on the right, defender is on the left.");
        document.getElementById("whole_game").style.backgroundColor = "black";

        document.getElementById("battle_button").disabled = true;
        clearSelected();
        if (document.getElementById("center_defender").childNodes.length === 1 && document.getElementById("center_attacker").childNodes.length === 1) {
            document.getElementById("attackButton").disabled = false;
            userFeedback("Now press the Attack button to roll the dice!");
        } else {
            document.getElementById("attackButton").disabled = true;
            // userFeedback("There must be a unit in both attacker and defender zones. Otherwise, end this round of attack by pressing Counter.")
        }
        document.getElementById("battleZonePopup").style.display = "block";
        document.getElementById("attackButton").innerHTML = "Attack section";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };

        if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {
            document.getElementById("changeSectionButton").disabled = false;
        }
        // alert("enabling button in section change my click");
        document.getElementById("changeSectionButton").disabled = false;
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
            // alert("counter click my team = current team disable true")
            document.getElementById("changeSectionButton").disabled = true;
        } else {
            document.getElementById("changeSectionButton").disabled = false;
        }

        document.getElementById("attackButton").disabled = true;
        document.getElementById("attackButton").innerHTML = "Counter Attack";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("defend"); };

        // alert("counter disabling true i clicked i know");
        // document.getElementById("changeSectionButton").disabled = true;
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
        document.getElementById("changeSectionButton").disabled = true;
        document.getElementById("attackButton").disabled = true;

        document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };

        document.getElementById("actionPopupButton").disabled = true;
        document.getElementById("actionPopupButton").disabled = true;

        gameBattleTurn = gameBattleTurn + 1;

    } else if (newSection === "none") {
        document.getElementById("phase_button").disabled = false;
        document.getElementById("undo_button").disabled = false;

        let phpBattleEnding = new XMLHttpRequest();
        phpBattleEnding.open("POST", "battleEnding.php?gameId=" + gameId, true);
        phpBattleEnding.send();
        
        //clear out the divs for battle piece deletion
        document.getElementById("battleZonePopup").style.display = "none";
        document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Battle";
        document.getElementById("battle_button").onclick = function() {
            if(confirm("Are you sure you want to battle?")) {
                battleChangeSection("selectPos");
            }
        };

        //check to see if flag ownership changed, if the position of battle was a flag position
        let flagPositions = [55, 65, 75, 79, 83, 86, 90, 94, 97, 100, 103, 107, 111, 114];
        let containerElement;
        if (flagPositions.includes(parseInt(gameBattlePosSelected))) {
            containerElement = document.querySelector("[data-positionId='" + gameBattlePosSelected + "']");
            let parentTeam = containerElement.parentNode.classList[2];
            let newTeam;
            if (parentTeam === "Red") {
                newTeam = "Blue";
            } else {
                newTeam = "Red";
            }
            let changeOwnership = "true";
            let numChildren = containerElement.childElementCount;
            if (numChildren === 0) {
                changeOwnership = "false";
            }
            for (let x = 0; x < numChildren; x++) {
                if (containerElement.childNodes[x].getAttribute("data-placementTeamId") === parentTeam) {
                    changeOwnership = "false";
                }
            }
            if (changeOwnership === "true") {
                //change css of parent
                let parent = containerElement.parentNode;
                parent.classList.remove(parentTeam);
                parent.classList.add(newTeam);
                //change css of parent parent
                let parentParent = parent.parentNode;
                parentParent.classList.remove(parentTeam);
                parentParent.classList.add(newTeam);
                //database change in games table
                let islandNumber = parentParent.id;
                let phpRequestTeamChange = new XMLHttpRequest();
                phpRequestTeamChange.open("POST", "gameIslandOwnerChange.php?gameId=" + gameId + "&islandToChange=" + islandNumber + "&newTeam=" + newTeam, true);
                phpRequestTeamChange.send();
            }
        }
    }

    // alert("changing section");

    // alert(gameBattleSection);
    // alert(gameBattleSubSection);
    // alert(gameBattleLastMessage);
    // alert(gameBattleLastRoll);
    // alert(gameBattlePosSelected);

    let phpBattleUpdate = new XMLHttpRequest();
    phpBattleUpdate.open("POST", "battleUpdateAttributes.php?gameBattleSection=" + gameBattleSection + "&gameBattleSubSection=" + gameBattleSubSection + "&gameBattleLastRoll=" + gameBattleLastRoll + "&gameBattleLastMessage=" + gameBattleLastMessage + "&gameBattlePosSelected=" + gameBattlePosSelected + "&gameBattleTurn=" + gameBattleTurn, true);
    phpBattleUpdate.send();

    // alert("thing sent");
}

function battleSelectPieces() {

    let parameterArray = [];

    let allPieces = document.getElementsByClassName("selected");
    let x;
    for (x = 0; x < allPieces.length; x++) {
        allPieces[x].setAttribute("data-placementBattleUsed", "1");
        parameterArray.push(allPieces[x].getAttribute("data-placementId"));
    }

    let sentArray = JSON.stringify(parameterArray);

    let phpPiecesSelect = new XMLHttpRequest();
    phpPiecesSelect.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            document.getElementById("unused_attacker").innerHTML = this.responseText;
        }
    };
    phpPiecesSelect.open("POST", "battlePiecesSelected.php?sentArray=" + sentArray + "&gameId=" + gameId + "&attackTeam=" + gameCurrentTeam, true);
    phpPiecesSelect.send();

    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
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
                actionButton.innerHTML = "HIT!! The Defender's unit was destroyed!! The Defender " +
                    "has the opportunity to knock out the Attacker's unit. Defender: roll for defense bonus!";
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
            let wasHitVariable = decoded.wasHit;
            gameBattleSubSection = decoded.new_gameBattleSubSection;
            battleChangeSection(gameBattleSection);  //This call to change roll and subsection
            document.getElementById("battleActionPopup").style.display = "block";
            rollDice();
        }
    };
    phpAttackCenter.open("GET", "battleAttackCenter.php?attackUnitId=" + attackUnitId + "&defendUnitId=" + defendUnitId + "&gameBattleSection=" + gameBattleSection + "&gameBattleSubSection=" + gameBattleSubSection + "&pieceId=" + pieceAttacked.getAttribute("data-battlePieceId"), true);
    phpAttackCenter.send();
}


let updateWait;
let waitTime = 10;

function waitForUpdate() {
    let phpUpdateBoard = new XMLHttpRequest();
    phpUpdateBoard.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            // alert(this.responseText);
            let decoded = JSON.parse(this.responseText);

            if (decoded.updateType === "pieceMove") {
                updatePieceMove(decoded.updatePlacementId, decoded.updateNewPositionId, decoded.updateNewContainerId, decoded.updateNewMoves);
            } else if (decoded.updateType === "pieceDelete") {
                updatePieceDelete(decoded.updatePlacementId);
            } else if (decoded.updateType === "rollDie") {
                updateRollDie(decoded.updatePlacementId);
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
            } else if (decoded.updateType === "islandChange") {
                updateIslandChange(decoded.updateIsland, decoded.updateIslandTeam);
            } else if (decoded.updateType === "battlePieceRemove") {
                updateBattlePieceRemove(decoded.updatePlacementId);
            }

            updateWait = window.setTimeout("waitForUpdate()", waitTime);
        }
    };
    phpUpdateBoard.open("GET", "updateBoard.php?gameId=" + gameId + "&myTeam=" + myTeam, true);  // removes the element from the database
    phpUpdateBoard.send();
}

function updateBattlePieceRemove(placementId) {

}

function updateRollDie(placementId) {
    document.querySelector("[data-placementId='" + placementId + "']").remove();  //mainboard
}

function updateIslandChange(islandIdentifier, newTeam) {
    // alert("should be updating team thing here, temp alert in place until ajax done");
    //TODO: this functionality does not work when island 13 or 14 (those are game enders however, so potentially this won't get called)
    let islandMain = document.getElementById(islandIdentifier);
    let islandPop = document.getElementById(islandIdentifier + "_pop");
    let oldTeam;
    if (newTeam === "Red") {
        oldTeam = "Blue";
    } else {
        oldTeam = "Red";
    }
    islandMain.classList.remove(oldTeam);
    islandMain.classList.add(newTeam);
    islandPop.classList.remove(oldTeam);
    islandPop.classList.add(newTeam);
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

function updatePieceMove(placementId, newPositionId, newContainerId, newMoves){
    // alert(placementId);
    // alert(newPositionId);
    // alert(newContainerId);
    let pieceToMove = document.querySelector("[data-placementId='" + placementId + "']");
    let theContainer;
    if (newContainerId != "999999") {
        theContainer = document.querySelector("[data-placementId='" + newContainerId + "']").childNodes[0];
    } else {
        theContainer = document.querySelector("[data-positionId='" + newPositionId + "']");
    }
    // alert(theContainer);
    theContainer.appendChild(pieceToMove);
    let unitName = pieceToMove.getAttribute("data-unitName");

    pieceToMove.setAttribute("title", unitName + "\n" +
        "Moves: " + newMoves);

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

            gameRedRpoints = decoded.gameRedRpoints;
            gameBlueRpoints = decoded.gameBlueRpoints;
            gameRedHpoints = decoded.gameRedHpoints;
            gameBlueHpoints = decoded.gameBlueHpoints;

            newsEffect = decoded.newsEffect;
            newsText = decoded.newsText;
            newsEffectText = decoded.newsEffectText;

            //TODO: 2 text elements change here (not yet implemented in game.php html + other js code)
            document.getElementById("popupTitle").innerHTML = "News Alert";

            document.getElementById("newsBodyText").innerHTML = newsText;
            document.getElementById("newsBodySubText").innerHTML = newsEffectText;


            document.getElementById("red_rPoints_indicator").innerHTML = gameRedRpoints;
            document.getElementById("blue_rPoints_indicator").innerHTML = gameBlueRpoints;
            document.getElementById("red_hPoints_indicator").innerHTML = gameRedHpoints;
            document.getElementById("blue_hPoints_indicator").innerHTML = gameBlueHpoints;

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
                document.getElementById("popup").style.display = "block";
            } else {
                // alert("not phase 1");
                document.getElementById("popup").style.display = "none";
            }
            document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase - 1];
            // document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
            if (gameCurrentTeam === "Red") {
                //red highlight
                document.getElementById("red_team_indicator").classList.add("highlightedTeam");
                //blue unhighlight
                document.getElementById("blue_team_indicator").classList.remove("highlightedTeam");
            } else {
                //blue highlight
                document.getElementById("blue_team_indicator").classList.add("highlightedTeam");
                //red unhighlight
                document.getElementById("red_team_indicator").classList.remove("highlightedTeam");
            }
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
                document.getElementById("actionPopupButton").innerHTML = "HIT! The Defender's unit was destroyed!! The Defender" +
                    "has the opportunity to knock out the Attacker's unit. Roll for defense bonus!";
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
    clearHighlighted();
    clearSelectedPos();
    clearSelected();

    document.getElementById("battleZonePopup").style.display = "block";
    document.getElementById("attackButton").innerHTML = "Attack section";  //already disabled by default?
    document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };
    document.getElementById("changeSectionButton").innerHTML = "Click to Counter";

    // alert("disable true because got update that pieces were selected");
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
                    document.getElementById("actionPopupButton").innerHTML = "HIT!! The Defender's unit was destroyed!! The Defender " +
                        "has the opportunity to knock out the Attacker's unit. Defender: roll for defense bonus!";
                    document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
                } else if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "counter") {
                    if (myTeam !== gameCurrentTeam) {
                        document.getElementById("actionPopupButton").disabled = true;
                    } else {
                        document.getElementById("actionPopupButton").disabled = false;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "HIT!! The Defender's unit was destroyed!! The Defender " +
                        "has the opportunity to knock out the Attacker's unit. Defender: roll for defense bonus!";
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

            // alert("changing section to something");
            if (gameBattleSection === "askRepeat" && myTeam === gameCurrentTeam) {
                // alert("myteam = current team enable");
                document.getElementById("attackButton").disabled = false;
                document.getElementById("changeSectionButton").disabled = false;
            } else if (gameBattleSection === "askRepeat" && myTeam !== gameCurrentTeam) {
                // alert("myteam != current team disable");
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
// Function to set the User Feedback text on the bottom bar of the game screen
function userFeedback(text){
    document.getElementById("user_feedback").innerHTML = text;
}
//Function for resetting the values of the hybrid tool inputs
function hybridResetPoints(){
    document.getElementById("setRedRpoints").value = gameRedRpoints;
    document.getElementById("setRedHpoints").value = gameRedHpoints;
    document.getElementById("setBlueRpoints").value = gameBlueRpoints;
    document.getElementById("setBlueHpoints").value = gameBlueHpoints;
}
//Function for sumbitting the values of the hybrid tool to the database
function hybridSetPoints(){
    let newRedRpoints = document.getElementById("setRedRpoints").valueOf();
    let newRedHpoints = document.getElementById("setRedHpoints").valueOf();
    let newBlueRpoints = document.getElementById("setBlueRpoints").valueOf();
    let newBlueHpoints = document.getElementById("setBlueHpoints").valueOf();
    let setPoints = new XMLHttpRequest();
    setPoints.open("POST", "hybridSetPoints.php?newRedRpoints=" + newRedRpoints + "&newRedHpoints=" + newRedHpoints + "&newBlueRpoints=" + newBlueRpoints + "&newBlueHpoints=" + newBlueHpoints, true);
    setPoints.send();
    document.getElementById("hybridSubmitPoints").value = "Submitted!";
}

function rollDice(){
    let numRolls = Math.floor(Math.random() * 40) + 20;
    let thingy;
    let i;
    for (i = 1; i < numRolls; i++) {
        let randomRoll = Math.floor(Math.random() * 6) + 1;
        thingy = setTimeout(function () {showDice(randomRoll)}, (i+1)*100);
    }
    thingy = setTimeout(function () {showDice(gameBattleLastRoll)}, (i+1)*100);

}


function showDice(diceNum){

    let diceImageThing = document.getElementById("dice_image");
    let currentCSS = diceImageThing.classList[0];
    diceImageThing.classList.remove(currentCSS);
    diceImageThing.classList.add("dice" + diceNum);


    // document.getElementById("dice_image").classList[1] = "dice" + diceNum;
    // document.getElementById("dice_image").classList[0].style.backgroundImage = "url(resources/diceImages/die-" + diceNum + ".gif)";
}
//
waitForUpdate();