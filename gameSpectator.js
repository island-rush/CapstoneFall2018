//Javascript Functions used by the Island Rush Game
//Created by C1C Spencer Adolph (7/28/2018).

let islandTimer;
let pieceTimer;
let deleteHybridState = "false";
let disableAirfieldHybridState = "false";
let bankHybridState = "false";
let nukeHybridState = "false";
let randomTimer;

//First function called to load the game...
function bodyLoader() {

    if (gameBattlePosSelected != "999999") {
        let phpPositionGet = new XMLHttpRequest();
        phpPositionGet.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let decoded = JSON.parse(this.responseText);
                gameBattleAdjacentArray = decoded.adjacentArray;
            }
        };
        phpPositionGet.open("POST", "battleGetAdjacentPos.php?positionSelected=" + gameBattlePosSelected, true);
        phpPositionGet.send();
    }

    // if (gameBattleSection == "selectPieces") {
    //     document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").classList.add("selectedPos");
    // }

    document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase-1];
    // document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
    if (gameCurrentTeam === "Red") {
        //red highlight
        document.getElementById("red_team_indicator").classList.add("highlightedTeamRed");
        //blue unhighlight
        document.getElementById("blue_team_indicator").classList.remove("highlightedTeamBlue");
    } else {
        //blue highlight
        document.getElementById("blue_team_indicator").classList.add("highlightedTeamBlue");
        //red unhighlight
        document.getElementById("red_team_indicator").classList.remove("highlightedTeamRed");
    }

    document.getElementById("red_rPoints_indicator").innerHTML = gameRedRpoints;
    document.getElementById("blue_rPoints_indicator").innerHTML = gameBlueRpoints;

    document.getElementById("red_hPoints_indicator").innerHTML = gameRedHpoints;
    document.getElementById("blue_hPoints_indicator").innerHTML = gameBlueHpoints;

    document.getElementById("lastBattleMessage").innerHTML = gameBattleLastMessage;

    document.getElementById("actionPopupButton").style.display = "block";
    showDice(gameBattleLastRoll);

    if (gameBattleSection !== "none" && gameBattleSection !== "selectPos" && gameBattleSection !== "selectPieces") {
        document.getElementById("battleZonePopup").style.display = "block";
    }

    if (gameBattleSection === "none") {
        // document.getElementById("phase_button").disabled = false;
        // document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Battle";
        document.getElementById("battle_button").onclick = function() {
            if (confirm("Are you sure you want to battle?")) {
                battleChangeSection("selectPos");
            }
        };
    } else if (gameBattleSection === "selectPos") {
        document.getElementById("phase_button").disabled = true;
        // document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Pieces";
        document.getElementById("battle_button").onclick = function() { battleSelectPosition(); };
        document.getElementById("whole_game").style.backgroundColor = "yellow";
    } else if (gameBattleSection === "selectPieces") {
        document.getElementById("phase_button").disabled = true;
        // document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Start Battle";
        document.getElementById("battle_button").onclick = function() { battleSelectPieces(); };
        document.getElementById("whole_game").style.backgroundColor = "yellow";
        document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").classList.add("selectedPos");
    } else {
        document.getElementById("battle_button").disabled = true;
        document.getElementById("battle_button").innerHTML = "Select Battle";
        document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").classList.add("selectedPos");
    }

    //deal with buttons and things on the battleZonePopup (as they should appear based upon game states / subsections
    if (gameBattleSubSection !== "choosing_pieces") {
        document.getElementById("battleActionPopup").style.display = "block";
        if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "attack") {
            if (myTeam !== gameCurrentTeam) {
                // document.getElementById("actionPopupButton").disabled = false;
            } else {
                document.getElementById("actionPopupButton").disabled = true;
            }
            document.getElementById("actionPopupButton").innerHTML = "HIT!! The Defender's unit was destroyed!! The Defender " +
                "has the opportunity to knock out the Attacker's unit. Defender: roll for defense bonus!";
            document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
        } else if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "counter") {
            if (myTeam !== gameCurrentTeam){
                document.getElementById("actionPopupButton").disabled = true;
            } else {
                // document.getElementById("actionPopupButton").disabled = false;
            }
            document.getElementById("actionPopupButton").innerHTML = "HIT!! The Defender's unit was destroyed!! The Defender " +
                "has the opportunity to knock out the Attacker's unit. Defender: roll for defense bonus!";
            document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
        } else if (gameBattleSubSection === "continue_choosing" && gameBattleSection === "attack") {
            if (myTeam !== gameCurrentTeam) {
                document.getElementById("actionPopupButton").disabled = true;
            } else {
                // document.getElementById("actionPopupButton").disabled = false;
            }
            document.getElementById("actionPopupButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
            document.getElementById("actionPopupButton").onclick = function() { battleEndRoll(); };
        } else if (gameBattleSubSection === "continue_choosing" && gameBattleSection === "counter") {
            if (myTeam !== gameCurrentTeam) {
                // document.getElementById("actionPopupButton").disabled = false;
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
            // document.getElementById("attackButton").disabled = false;
            let upperBox = document.getElementById("battle_outcome");
            let defendPieceId = parseInt(document.getElementById("center_defender").childNodes[0].getAttribute("data-unitId"));
            let attackPieceId = parseInt(document.getElementById("center_attacker").childNodes[0].getAttribute("data-unitId"));
            let needToKill = 0;
            if (gameBattleSection === "attack") {
                needToKill = attackMatrix[attackPieceId][defendPieceId];
            } else {
                needToKill = attackMatrix[defendPieceId][attackPieceId];
            }
            upperBox.innerHTML = "You must roll a " + needToKill + " in order to kill.";
        }
    }

    //could consolidate these with a call to change section (with same section)
    if (gameBattleSection === "attack") {
        document.getElementById("attackButton").innerHTML = "Attack!";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };
        document.getElementById("changeSectionButton").innerHTML = "End Attack/Start Counter";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("counter") };
    } else if (gameBattleSection === "counter") {
        document.getElementById("attackButton").innerHTML = "Counter Attack";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("defend"); };
        document.getElementById("changeSectionButton").innerHTML = "End Counter Attack";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("askRepeat") };
    } else if (gameBattleSection === "askRepeat") {
        document.getElementById("attackButton").innerHTML = "Click to Repeat";
        document.getElementById("attackButton").onclick = function() { battleChangeSection("attack") };
        document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };
    }

    if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {
        // document.getElementById("attackButton").disabled = false;
        // document.getElementById("changeSectionButton").disabled = false;
    }

    if (gameBattleSection === "askRepeat" && myTeam === gameCurrentTeam) {
        // document.getElementById("attackButton").disabled = false;
        // document.getElementById("changeSectionButton").disabled = false;
    } else if (gameBattleSection === "askRepeat" && myTeam !== gameCurrentTeam) {
        document.getElementById("attackButton").disabled = true;
        document.getElementById("changeSectionButton").disabled = true;
    }

    if (canAttack === "true") {
        if (gameBattleSection !== "attack" && gameBattleSection !== "counter" && gameBattleSection !== "askRepeat") {
            // document.getElementById("battle_button").disabled = false;
        } else {
            document.getElementById("battle_button").disabled = true;
        }
    } else {
        document.getElementById("battle_button").disabled = true;
    }
    if (canUndo === "true") {
        if (gameBattleSection === "none") {
            // document.getElementById("undo_button").disabled = false;
        }
    } else {
        document.getElementById("undo_button").disabled = true;
    }
    if (canNextPhase === "true") {
        if (gameBattleSection === "none") {
            // document.getElementById("phase_button").disabled = false;
        } else {
            document.getElementById("phase_button").disabled = true;
        }
    } else {
        document.getElementById("phase_button").disabled = true;
    }
    // NEWS ALERT PHASE
    if (gamePhase === "1") {
        // Show popup with News Alert body, hide Hybrid body
        document.getElementById("popupTitle").innerHTML = "News Alert";
        document.getElementById("newsBodyText").innerHTML = newsText;
        document.getElementById("newsBodySubText").innerHTML = newsEffectText;
        document.getElementById("popupBodyNews").style.display = "block";
        document.getElementById("popupBodyHybridMenu").style.display = "none";
        document.getElementById("popup").style.display = "block";
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
        if (myTeam === gameCurrentTeam) {
            // document.getElementById("battle_button").disabled = false;
        } else {
            document.getElementById("battle_button").disabled = true;
        }

        document.getElementById("battle_button").onclick =function () {
            if(document.getElementById("popup").style.display === "block"){
                document.getElementById("popup").style.display = "none";
            }
            else{
                document.getElementById("popupTitle").innerHTML = "Hybrid Warfare Menu";
                document.getElementById("popupBodyNews").style.display = "none";
                document.getElementById("popupBodyHybridMenu").style.display = "block";
                document.getElementById("popup").style.display = "block";
            }
        };
    }

    if (document.getElementById("battleActionPopup").style.display == "block") {
        showDice(gameBattleLastRoll);
    }

    if (gamePhase === "1" && gameBattleSection === "none") {
        userFeedback("News Phase. Click Next Phase to advance to next phase.");
    } else if (gamePhase === "2" && gameBattleSection === "none") {
        userFeedback("Buy Reinforcements Phase. Click Next Phase to advance to next phase.");
    } else if (gamePhase === "3" && gameBattleSection === "none") {
        userFeedback("Combat Phase. Click Next Phase to advance to next phase.");
    } else if (gamePhase === "4" && gameBattleSection === "none") {
        userFeedback("Fortify Phase. Click Next Phase to advance to next phase.");
    } else if (gamePhase === "5" && gameBattleSection === "none") {
        userFeedback("Place Reinforcements Phase. Click Next Phase to advance to next phase.");
    } else if (gamePhase === "6" && gameBattleSection === "none") {
        userFeedback("Hybrid Warfare Phase. Click Next Phase to advance to next phase.");
    } else if (gamePhase === "7" && gameBattleSection === "none") {
        userFeedback("Round Recap Phase. Click Next Phase to advance to next phase.");
    } else if (gameBattleSection === "selectPos") {
        userFeedback("Now click on the zone that you want to attack. Then click the Select Pieces button.");
    } else if (gameBattleSection === "selectPieces") {
        userFeedback("Select the pieces you want to attack with. They must be adjacent to the zone being attacked. Then Start the Battle!");
    } else {
        //TODO: could deal with more specific userfeedback (if they need to roll or something) (unlikely they would refresh in the middle of battle)
        userFeedback("Battle in progress.")
    }
}

function logout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.replace("logout.php");
    }
}

function logout2() {
    window.location.replace("logout.php?reason=1");
}

//---------------------------------------------------
function pieceClick(event, callingElement) {
    event.preventDefault();
    //open container if applicable
    if (gameBattleSection === "selectPieces" && myTeam === gameCurrentTeam) {
        if (callingElement.getAttribute("data-placementTeamId") === myTeam) {
            if (gameBattleAdjacentArray.includes(parseInt(callingElement.parentNode.getAttribute("data-positionId")))) {
                let planes = [11, 12, 13, 14];
                if (planes.includes(parseInt(callingElement.getAttribute("data-unitId")))){
                    if (callingElement.parentNode.getAttribute("data-positionId") == gameBattlePosSelected) {
                        if (callingElement.classList.contains("selected")) {
                            callingElement.classList.remove("selected");
                        } else {
                            if (callingElement.getAttribute("data-placementBattleUsed") == 0) {
                                callingElement.classList.add("selected");
                            }
                        }
                    } else {
                        userFeedback("Plane must be in same square to attack");
                    }
                } else {
                    if (callingElement.classList.contains("selected")) {
                        callingElement.classList.remove("selected");
                    } else {
                        if (callingElement.getAttribute("data-placementBattleUsed") == 0) {
                            callingElement.classList.add("selected");
                        }
                    }
                }
            } else {
                userFeedback("Piece must be adjacent to battle position.");
            }
        } else {
            userFeedback("Can only select your pieces.");
        }
    } else {
        if (gameBattleSection === "selectPos" && myTeam === gameCurrentTeam) {
            if (callingElement.getAttribute("data-unitName") !== "missile"){
                clearSelectedPos();
                callingElement.parentNode.classList.add("selectedPos");
            } else {
                userFeedback("Can't select missile position for battle.");
            }
        } else {
            if (deleteHybridState === "true" && myTeam === gameCurrentTeam) {
                callingElement.classList.add("selected");
                randomTimer = setTimeout(function() {
                    if (confirm("Is this the piece you want to delete?")) {
                        //delete the piece db
                        let placementId = callingElement.getAttribute("data-placementId");
                        let phpDeleteRequest = new XMLHttpRequest();
                        phpDeleteRequest.open("POST", "hybridDeletePiece.php?placementId=" + placementId, true);
                        phpDeleteRequest.send();
                        //html remove
                        callingElement.remove();
                        document.getElementById("whole_game").style.backgroundColor = "black";
                        deleteHybridState = "false";
                        // document.getElementById("battle_button").disabled = false;
                        // document.getElementById("phase_button").disabled = false;
                    } else {
                        callingElement.classList.remove("selected");
                    }}, 50);
            } else {
                let unitName = callingElement.getAttribute("data-unitName");
                if (unitName === "Transport" || unitName === "AircraftCarrier") {
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
            }
        }
    }
    event.stopPropagation();
}

function pieceDragstart(event, callingElement) {
    userFeedback("Drag this piece and hover over an island, Transport, or AircraftCarrier to open them up. Drop it when ready to move.");
    //canMove is dictated by phase and current Team
    if ((canMove === "true") && callingElement.getAttribute("data-placementTeamId") === myTeam && gameBattleSection === "none") {
        //From the container (parent of the piece)(or position)
        if (callingElement.parentNode.getAttribute("data-positionId") == null) {
            event.dataTransfer.setData("positionId", callingElement.parentNode.parentNode.parentNode.getAttribute("data-positionId"));
        } else {
            event.dataTransfer.setData("positionId", callingElement.parentNode.getAttribute("data-positionId"));
        }
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
        //TODO: could get specific about reasons why can't move, can also disable drag based upon phase and position (can't drag board pieces during piece purchase)
        userFeedback("Unable to move this piece.");
        event.preventDefault();  // This stops the drag
    }

    event.stopPropagation();
}

function pieceDragleave(event, callingElement) {
    event.preventDefault();
    if (callingElement.getAttribute("data-unitName") === "transport" || callingElement.getAttribute("data-unitName") === "aircraftCarrier") {
        if (callingElement.childNodes[0].getAttribute("data-containerPopped") == "false") {
            clearTimeout(pieceTimer);
        }
    }
    event.stopPropagation();
}

function pieceDragenter(event, callingElement) {
    event.preventDefault();
    clearTimeout(pieceTimer);
    let unitName = callingElement.getAttribute("data-unitName");
    if (unitName === "Transport" || unitName === "AircraftCarrier") {
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
        let myPoints = gameRedRpoints;
        if (myTeam === "Blue") {
            myPoints = gameBlueRpoints;
        }
        let costOfPiece = parseInt(purchaseSquare.getAttribute("data-unitCost"));
        if (myPoints >= costOfPiece) {
            let unitId = purchaseSquare.getAttribute("data-unitId");
            let unitName = purchaseSquare.id;
            let unitMoves = unitsMoves[unitName];
            let terrain = purchaseSquare.getAttribute("data-unitTerrain");
            myPoints = myPoints - costOfPiece;

            if (myTeam === "Red") {
                gameRedRpoints = myPoints;
                document.getElementById("red_rPoints_indicator").innerHTML = gameRedRpoints;
            } else {
                gameBlueRpoints = myPoints;
                document.getElementById("blue_rPoints_indicator").innerHTML = gameBlueRpoints;
            }

            userFeedback("Piece purchased for " + costOfPiece + " points.");

            let phpPurchaseRequest = new XMLHttpRequest();
            phpPurchaseRequest.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let parent = document.getElementById("purchased_container");
                    parent.innerHTML += this.responseText;
                }
            };
            phpPurchaseRequest.open("GET", "piecePurchase.php?unitId=" + unitId + "&costOfPiece=" + costOfPiece + "&newPoints=" + myPoints + "&myTeam=" + myTeam + "&unitName=" + unitName + "&unitMoves=" + unitMoves + "&unitTerrain=" + terrain + "&placementTeamId=" + myTeam + "&gameId=" + gameId, true);
            phpPurchaseRequest.send();
        } else {
            userFeedback("Not enough points.");
        }
    } else {
        //TODO: could be more specific about why not
        userFeedback("Unable to purchase at this time.");
    }
}

function pieceMoveUndo() {
    if (canUndo === "true") {
        let phpUndoRequest = new XMLHttpRequest();
        phpUndoRequest.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                if (this.responseText != 3) {
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

                        let unitName = pieceToUndo.getAttribute("data-unitName");

                        pieceToUndo.setAttribute("title", unitName + "\n" +
                            "Moves: " + decoded.new_placementCurrentMoves);


                    }
                }
            }
        };
        phpUndoRequest.open("GET", "pieceMoveUndo.php?gameId=" + gameId + "&gameTurn=" + gameTurn + "&gamePhase=" + gamePhase + "&myTeam=" + myTeam, true);
        phpUndoRequest.send();
        userFeedback("Move undone.");
    } else {
        userFeedback("Move cannot be undone.")
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
            userFeedback("Piece recycled. Reinforcement Points refunded");
        } else {
            userFeedback("Can't recycle pieces from the board.");
        }
    } else {
        userFeedback("Unable to recycle pieces at this time.");
    }
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

function waterdblclick(event, callingElement){
    event.preventDefault();
    let pos = parseInt(callingElement.getAttribute("data-positionId"));
    showAdjacent(pos);
    event.stopPropagation();
}

function landdblclick(event, callingElement) {
    event.preventDefault();
    let pos = parseInt(callingElement.getAttribute("data-positionId"));
    showAdjacent(pos);
    event.stopPropagation();
}

function showAdjacent(pos){
    clearHighlighted();
    let thisPos = pos;
    let phpAvailableMoves = new XMLHttpRequest();
    phpAvailableMoves.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {  //movement_undo echos a JSON with info about the new placement
            // alert(this.responseText);
            let decoded = JSON.parse(this.responseText);
            for (let g = 0; g < decoded.length; g++) {
                let gridThing = document.querySelectorAll("[data-positionId='" + decoded[g] + "']")[0];
                gridThing.classList.add("highlighted");
                // let parent = gridThing.parentNode;
                // let parclass = parent.classList;
                // if (parclass[0] !== "gridblockLeftBig" && parclass[0] !== "gridblockRightBig") {
                //     let islandsquare = document.getElementById(parclass[0]);
                //     islandsquare.classList.add("highlighted");
                // }
            }
        }
    };
    phpAvailableMoves.open("GET", "gameGetAdjacent.php?thisPos=" + thisPos, true);
    phpAvailableMoves.send();
    userFeedback("These are the positions adjacent. Click another position to clear.");
    event.stopPropagation();
}

function islandClick(event, callingElement) {
    event.preventDefault();
    // hideIslands();  //only 1 island visible at a time

    let x = document.getElementsByClassName("special_island3x3");
    let i;
    let islandPopped = false;
    for (i = 0; i < x.length; i++) {
        if (x[i].parentNode.getAttribute("data-islandPopped") === "true") {
            islandPopped = true;
        }
        x[i].style.display = "none";
        x[i].parentNode.style.zIndex = 10;  //10 is the default
        x[i].parentNode.setAttribute("data-islandPopped", "false");
    }
    if (islandPopped) {
        hideIslands();
    }

    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    // clearHighlighted();
    // if (gameBattleSection === "none" || gameBattleSection === "selectPos" || gameBattleSection === "selectPieces") {
    //     if (bankHybridState === "true") {
    //         if (callingElement.classList[2] !== myTeam) {
    //             callingElement.classList.add("selectedPos");
    //             randomTimer = setTimeout(function() {
    //                 if (confirm("Are you sure you want this island's points for next two turns?")) {
    //                     let islandId = callingElement.id;
    //                     let lastNumber = islandId[islandId.length-1];
    //                     let phpBankRequest = new XMLHttpRequest();
    //                     phpBankRequest.open("POST", "hybridBank.php?lastNumber=" + lastNumber, true);
    //                     phpBankRequest.send();
    //                     document.getElementById("whole_game").style.backgroundColor = "black";
    //                     callingElement.classList.remove("selectedPos");
    //                     bankHybridState = "false";
    //                     document.getElementById("battle_button").disabled = false;
    //                     document.getElementById("phase_button").disabled = false;
    //                 } else {
    //                     callingElement.classList.remove("selectedPos");
    //                 }}, 50);
    //         }
    //     } else {
    //         if (nukeHybridState === "true") {
    //             callingElement.classList.add("selectedPos");
    //             randomTimer = setTimeout(function() {
    //                 if (confirm("Are you sure you want to nuke this island?")) {
    //                     let islandId = callingElement.id;
    //                     let lastNumber = islandId[islandId.length-1];
    //                     let phpNukeRequest = new XMLHttpRequest();
    //                     phpNukeRequest.open("POST", "hybridNuke.php?lastNumber=" + lastNumber, true);
    //                     phpNukeRequest.send();
    //                     document.getElementById("whole_game").style.backgroundColor = "black";
    //                     nukeHybridState = "false";
    //                     document.getElementById("battle_button").disabled = false;
    //                     document.getElementById("phase_button").disabled = false;
    //                     callingElement.classList.remove("selectedPos");
    //                 } else {
    //                     callingElement.classList.remove("selectedPos");
    //                 }}, 50);
    //         } else {
    //             document.getElementsByClassName(callingElement.id)[0].style.display = "block";
    //             callingElement.style.zIndex = 20;  //default for a gridblock is 10
    //             callingElement.setAttribute("data-islandPopped", "true");
    //         }
    //     }
    // }
    if (bankHybridState === "true") {
        if (callingElement.classList[2] !== myTeam) {
            callingElement.classList.add("selectedPos");
            randomTimer = setTimeout(function() {
                if (confirm("Are you sure you want this island's points for next two turns?")) {
                    let islandId = callingElement.id;
                    let lastNumber = islandId[islandId.length-1];
                    let phpBankRequest = new XMLHttpRequest();
                    phpBankRequest.open("POST", "hybridBank.php?lastNumber=" + lastNumber, true);
                    phpBankRequest.send();
                    document.getElementById("whole_game").style.backgroundColor = "black";
                    callingElement.classList.remove("selectedPos");
                    bankHybridState = "false";
                    // document.getElementById("battle_button").disabled = false;
                    // document.getElementById("phase_button").disabled = false;
                } else {
                    callingElement.classList.remove("selectedPos");
                }}, 50);
        } else {
            userFeedback("Can't select Bank Option for your own island.");
        }
    } else {
        if (nukeHybridState === "true") {
            callingElement.classList.add("selectedPos");
            randomTimer = setTimeout(function() {
                if (confirm("Are you sure you want to nuke this island?")) {
                    let islandId = callingElement.id;
                    let lastNumber = islandId[islandId.length-1];
                    let phpNukeRequest = new XMLHttpRequest();
                    phpNukeRequest.open("POST", "hybridNuke.php?lastNumber=" + lastNumber, true);
                    phpNukeRequest.send();
                    document.getElementById("whole_game").style.backgroundColor = "black";
                    nukeHybridState = "false";
                    // document.getElementById("battle_button").disabled = false;
                    // document.getElementById("phase_button").disabled = false;
                    callingElement.classList.remove("selectedPos");
                } else {
                    callingElement.classList.remove("selectedPos");
                }}, 50);
        } else {
            document.getElementsByClassName(callingElement.id)[0].style.display = "block";
            callingElement.style.zIndex = 20;  //default for a gridblock is 10
            callingElement.setAttribute("data-islandPopped", "true");
        }
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

    callingElement.classList.add("mouseOver");

    event.stopPropagation();
}

function landDragLeave(event, callingElement) {
    event.preventDefault();
    clearTimeout(islandTimer);

    callingElement.classList.remove("mouseOver");

    event.stopPropagation();
}

function hideIslands() {
    let x = document.getElementsByClassName("special_island3x3");
    let i;
    let islandPopped = false;
    for (i = 0; i < x.length; i++) {
        if (x[i].parentNode.getAttribute("data-islandPopped") === "true") {
            islandPopped = true;
        }
        x[i].style.display = "none";
        x[i].parentNode.style.zIndex = 10;  //10 is the default
        x[i].parentNode.setAttribute("data-islandPopped", "false");
    }
    if (!islandPopped) {
        clearHighlighted();
    }
}



function landClick(event, callingElement) {
    event.preventDefault();

    userFeedback("Double Click to show Adjacent Positions.");
    // hideIslands();
    clearHighlighted();

    if (gameBattleSection === "selectPos" && gameCurrentTeam === myTeam) {
        clearSelectedPos();
        callingElement.classList.add("selectedPos");
    }

    if (disableAirfieldHybridState === "true") {
        callingElement.classList.add("selectedPos");
        let positionId = callingElement.getAttribute("data-positionId");
        //check to see if the position is in a list
        let listairfields = [56, 57, 78, 83, 89, 113, 116, 66, 68];
        if (listairfields.includes(parseInt(positionId))) {
            randomTimer = setTimeout(function() {
                if (confirm("Is this the airfield you want to disable?")) {
                    let phpDeleteRequest = new XMLHttpRequest();
                    phpDeleteRequest.open("POST", "hybridDisableAirfield.php?positionId=" + positionId, true);
                    phpDeleteRequest.send();
                    callingElement.classList.remove("selectedPos");
                    document.getElementById("whole_game").style.backgroundColor = "black";
                    deleteHybridState = "false";
                    // document.getElementById("battle_button").disabled = false;
                    // document.getElementById("phase_button").disabled = false;
                } else {
                    callingElement.classList.remove("selectedPos");
                }}, 50);
        } else {
            callingElement.classList.remove("selectedPos");
            userFeedback("Not a valid airfield position.");
        }
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
    if (gameBattleSection === "selectPos" && gameCurrentTeam === myTeam) {
        clearSelectedPos();
        callingElement.classList.add("selectedPos");
    }
    userFeedback("Double Click to show Adjacent Positions.");

    event.stopPropagation();
}



function positionDrop(event, newContainerElement) {
    event.preventDefault();

    newContainerElement.classList.remove("mouseOver");

    clearHighlighted();
    //Already approved to move by pieceDragstart (same team and good phase)
    let placementId = event.dataTransfer.getData("placementId");
    let unitName = event.dataTransfer.getData("unitName");
    let unitId = event.dataTransfer.getData("unitId");
    let pieceDropped = document.querySelector("[data-placementId='" + placementId + "']");
    let positionType = newContainerElement.getAttribute("data-positionType");
    let unitTerrain = event.dataTransfer.getData("unitTerrain");
    let new_positionId = newContainerElement.getAttribute("data-positionId");
    if (new_positionId == null) {
        new_positionId = newContainerElement.parentNode.parentNode.getAttribute("data-positionId");
    }
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
    if ((old_positionId != "118" && (gamePhase != 5 && gamePhase != 2)) || (old_positionId == "118" && gamePhase == 5)) {
        if (movementCheck(unitName, unitTerrain, new_placementContainerId, positionType, new_positionId) === true) {
            let phpMoveCheck = new XMLHttpRequest();
            phpMoveCheck.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let movementCost = parseInt(this.responseText);
                    if (movementCost >= 0) {
                        //MANY OTHER CHECKS FOR MOVEMENT CAN HAPPEN HERE, JUST NEST MORE FUNCTIONS (see above)
                        let new_placementCurrentMoves = old_placementCurrentMoves - movementCost;

                        //Update the html by moving the piece and changing the piece's attributes
                        newContainerElement.appendChild(pieceDropped);
                        pieceDropped.setAttribute("data-placementCurrentMoves", new_placementCurrentMoves.toString());
                        pieceDropped.setAttribute("data-placementContainerId", new_placementContainerId);
                        // if (unitName === "Transport" || unitName === "AircraftCarrier") {
                        //     pieceDropped.firstChild.setAttribute("data-positionId", newContainerElement.getAttribute("data-positionId"));
                        // }

                        // title='".$unitName2."&#013;Moves: ".$placementCurrentMoves2."'
                        pieceDropped.setAttribute("title", unitName + "\n" +
                            "Moves: " + new_placementCurrentMoves);

                        //Update the placement in the database and add a movement to the database
                        let phpRequest = new XMLHttpRequest();
                        phpRequest.open("POST", "pieceMove.php?gameId=" + gameId + "&myTeam=" + myTeam + "&gameTurn=" + gameTurn + "&gamePhase=" + gamePhase + "&placementId=" + placementId + "&unitName=" + unitName + "&new_positionId=" + new_positionId + "&old_positionId=" + old_positionId + "&movementCost=" + movementCost  + "&new_placementCurrentMoves=" + new_placementCurrentMoves + "&old_placementContainerId=" + old_placementContainerId + "&new_placementContainerId=" + new_placementContainerId, true);
                        phpRequest.send();

                        let flagPositions = [55, 65, 75, 79, 85, 86, 90, 94, 97, 100, 103, 107, 111, 114];
                        let containerElement;
                        let capturePieces = ["ArmyCompany", "ArtilleryBattery", "TankPlatoon", "MarinePlatoon", "MarineConvoy"];
                        if (flagPositions.includes(parseInt(new_positionId)) && capturePieces.includes(unitName)) {
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
                                phpRequestTeamChange.open("POST", "gameIslandOwnerChange.php?gameId=" + gameId + "&islandToChange=" + islandNumber + "&newTeam=" + newTeam + "&myTeam=" + myTeam, true);
                                phpRequestTeamChange.send();

                                //if there is a missile there, change the team for it (db change is in above php call)
                                let missileIslandFlags = [79, 94, 97, 103];
                                if (missileIslandFlags.includes(parseInt(new_positionId))) {
                                    //check to see if missile is there
                                    let docId = "posM1";
                                    if (new_positionId == 94) {
                                        docId = "posM2";
                                    } else if (new_positionId == 97) {
                                        docId = "posM3";
                                    } else if (new_positionId == 103) {
                                        docId = "posM4";
                                    }
                                    let missileContainer = document.getElementById(docId);
                                    if (missileContainer.childNodes.length == 1) {
                                        let missile = missileContainer.childNodes[0];
                                        missile.classList.remove(parentTeam);
                                        missile.classList.add(newTeam);
                                        missile.setAttribute("data-placementTeamId", newTeam);
                                    }
                                }
                            }
                        }

                        //check to see if the piece was dropped at a missile target position
                        //then check if there is an 'enemy' missile at that island spot (missile container)
                        //if so, delete the piece and delete the missile and give user feedback about what happened
                        let missileTargets1 = [2, 3, 4, 10, 11, 15, 16];
                        let missileTargets2 = [16, 17, 18, 24, 25, 29, 30, 31];
                        let missileTargets3 = [19, 20, 21, 26, 27, 32, 33, 34];
                        let missileTargets4 = [28, 35, 36, 41, 42];
                        let acceptableTargets = ["Transport", "Destroyer", "AircraftCarrier"];

                        if (missileTargets1.includes(parseInt(new_positionId)) && acceptableTargets.includes(unitName)) {
                            //check if missile on this island
                            if (document.getElementById("posM1").childNodes.length == 1) {
                                if (!document.getElementById("posM1").childNodes[0].classList.contains(myTeam)) {
                                    //80% chance of hit here
                                    let randomNumber = Math.floor(Math.random() * 10);  //between 0 and 9, if = 8 or 9, thats 80%
                                    if (randomNumber >= 8) {
                                        //delete the piece that moved html
                                        document.querySelector("[data-placementId='" + placementId + "']").remove();
                                        //delete the piece that moved db
                                        let phpDeleteRequest = new XMLHttpRequest();
                                        phpDeleteRequest.open("POST", "pieceDelete.php?placementId=" + placementId, true);
                                        phpDeleteRequest.send();
                                        let missileId = document.getElementById("posM1").childNodes[0].getAttribute("data-placementId");
                                        //delete the missile html
                                        document.querySelector("[data-placementId='" + missileId + "']").remove();
                                        //delete the missile db
                                        let phpDeleteRequest2 = new XMLHttpRequest();
                                        phpDeleteRequest2.open("POST", "pieceDelete.php?placementId=" + missileId, true);
                                        phpDeleteRequest2.send();
                                        //user feedback
                                        userFeedback("Land Based Sea Missile destroyed a piece.");
                                    }
                                }
                            }
                        }
                        if (missileTargets2.includes(parseInt(new_positionId)) && acceptableTargets.includes(unitName)) {
                            if (document.getElementById("posM2").childNodes.length == 1) {
                                if (!document.getElementById("posM2").childNodes[0].classList.contains(myTeam)) {
                                    let randomNumber = Math.floor(Math.random() * 10);  //between 0 and 9, if = 8 or 9, thats 80%
                                    if (randomNumber >= 8) {
                                        document.querySelector("[data-placementId='" + placementId + "']").remove();
                                        let phpDeleteRequest = new XMLHttpRequest();
                                        phpDeleteRequest.open("POST", "pieceDelete.php?placementId=" + placementId, true);
                                        phpDeleteRequest.send();
                                        let missileId = document.getElementById("posM2").childNodes[0].getAttribute("data-placementId");
                                        document.querySelector("[data-placementId='" + missileId + "']").remove();
                                        let phpDeleteRequest2 = new XMLHttpRequest();
                                        phpDeleteRequest2.open("POST", "pieceDelete.php?placementId=" + missileId, true);
                                        phpDeleteRequest2.send();
                                        userFeedback("Land Based Sea Missile destroyed a piece.");
                                    }
                                }
                            }
                        }
                        if (missileTargets3.includes(parseInt(new_positionId)) && acceptableTargets.includes(unitName)) {
                            if (document.getElementById("posM3").childNodes.length == 1) {
                                if (!document.getElementById("posM3").childNodes[0].classList.contains(myTeam)) {
                                    let randomNumber = Math.floor(Math.random() * 10);  //between 0 and 9, if = 8 or 9, thats 80%
                                    if (randomNumber >= 8) {
                                        document.querySelector("[data-placementId='" + placementId + "']").remove();
                                        let phpDeleteRequest = new XMLHttpRequest();
                                        phpDeleteRequest.open("POST", "pieceDelete.php?placementId=" + placementId, true);
                                        phpDeleteRequest.send();
                                        let missileId = document.getElementById("posM3").childNodes[0].getAttribute("data-placementId");
                                        document.querySelector("[data-placementId='" + missileId + "']").remove();
                                        let phpDeleteRequest2 = new XMLHttpRequest();
                                        phpDeleteRequest2.open("POST", "pieceDelete.php?placementId=" + missileId, true);
                                        phpDeleteRequest2.send();
                                        userFeedback("Land Based Sea Missile destroyed a piece.");
                                    }
                                }
                            }
                        }
                        if (missileTargets4.includes(parseInt(new_positionId)) && acceptableTargets.includes(unitName)) {
                            if (document.getElementById("posM4").childNodes.length == 1) {
                                if (!document.getElementById("posM4").childNodes[0].classList.contains(myTeam)) {
                                    let randomNumber = Math.floor(Math.random() * 10);  //between 0 and 9, if = 8 or 9, thats 80%
                                    if (randomNumber >= 8) {
                                        document.querySelector("[data-placementId='" + placementId + "']").remove();
                                        let phpDeleteRequest = new XMLHttpRequest();
                                        phpDeleteRequest.open("POST", "pieceDelete.php?placementId=" + placementId, true);
                                        phpDeleteRequest.send();
                                        let missileId = document.getElementById("posM4").childNodes[0].getAttribute("data-placementId");
                                        document.querySelector("[data-placementId='" + missileId + "']").remove();
                                        let phpDeleteRequest2 = new XMLHttpRequest();
                                        phpDeleteRequest2.open("POST", "pieceDelete.php?placementId=" + missileId, true);
                                        phpDeleteRequest2.send();
                                        userFeedback("Land Based Sea Missile destroyed a piece.");
                                    }
                                }
                            }
                        }
                    } else{
                        // Piece was out of moves (-1)
                        if (movementCost == -2) {
                            userFeedback("News alert prevented that!");
                        } else {
                            if (movementCost == -3) {
                                userFeedback("Pieces must use moves one at a time.");
                            } else {
                                if (movementCost == -4) {
                                    userFeedback("Not a valid drop zone.")
                                } else {
                                    if (movementCost == -5){
                                        userFeedback("Enemy Team Prevented Drop.");
                                    } else {
                                        if (movementCost == -10) {
                                            userFeedback("SAM Destroyed that piece!");
                                        }
                                        else {
                                            userFeedback("This piece is out of moves!");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            };
            phpMoveCheck.open("POST", "pieceMoveValid.php?new_positionId=" + new_positionId + "&old_placementContainerId=" + old_placementContainerId + "&new_placementContainerId=" + new_placementContainerId + "&old_positionId=" + old_positionId + "&placementId=" + placementId + "&islandFrom=" + islandFrom + "&islandTo=" + islandTo + "&unitName=" + unitName + "&unitId=" + unitId, true);
            phpMoveCheck.send();
        }
    } else{
        userFeedback("This piece can't move here.");
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

function movementCheck(unitName, unitTerrain, new_placementContainerId, positionTerrain, new_positionId) {
    if (new_placementContainerId != "999999") {
        let containerParent = document.querySelector("[data-placementId='" + new_placementContainerId + "']");
        if (containerParent.getAttribute("data-unitName") === "Transport") {
            let listPeople = ["MarinePlatoon", "ArmyCompany"];
            let listMachines = ["TankPlatoon", "MarineConvoy", "AttackHelo", "SAM", "ArtilleryBattery"];
            if (!listPeople.includes(unitName) && !listMachines.includes(unitName)) {
                userFeedback("This piece does not belong in a Transport.");
                return false;  //piece does not belong in Transport container
            }
            if (containerParent.childNodes[0].childNodes.length === 0) {
                return true;  //valid piece can always go into empty Transport
            }
            if (containerParent.childNodes[0].childNodes.length === 3) {
                userFeedback("This container is full.");
                return false;  //already full of soldiers (max number)
            }
            if (listPeople.includes(unitName)) {  //piece dropping in is a person
                if (containerParent.childNodes[0].childNodes.length === 2) {  //both were people, allow a 3rd person
                    return listPeople.includes(containerParent.childNodes[0].childNodes[0].getAttribute("data-unitName"))
                        && listPeople.includes(containerParent.childNodes[0].childNodes[1].getAttribute("data-unitName"));
                }
                return true;  //person dropping into Transport with 1 piece in it (always allowed)
            } else {
                //machine can drop in with a single person, can't drop into a Transport with 2 pieces inside
                return    (containerParent.childNodes[0].childNodes.length === 1
                    && listPeople.includes(containerParent.childNodes[0].childNodes[0].getAttribute("data-unitName")));
            }
        } else {  //not Transport -> must be AircraftCarrier
            //TODO: could be specific with false return and userfeedback here
            return unitName === "FighterSquadron" && containerParent.childNodes[0].childNodes.length < 2;  // room for another fighter
        }
    } else {  //wasn't a container
        //if unit is a boat (carrier, destroyer, transport)
        //check if the new position has enemies in it (blockade code)
        let boats = ["Transport", "Destroyer", "AircraftCarrier"];
        if (boats.includes(unitName)){
            let newPosDiv = document.querySelector("[data-positionId='" + new_positionId + "']");
            for (let x = 0; x < newPosDiv.childNodes.length; x++) {
                if (newPosDiv.childNodes[x].getAttribute("data-placementTeamId") !== myTeam){
                    if (newPosDiv.childNodes[x].getAttribute("data-unitName") !== "Submarine") {
                        userFeedback("Blockade prevented movement.");
                        return false;
                    }
                }
            }
        }
        //TODO: could change this return to show userfeedback on false returns (specific reasons why failed)
        return unitTerrain === "air" || unitTerrain === positionTerrain; //air anywhere, or match terrain (missile = missile)
    }
}

function changePhase() {
    if (canNextPhase === "true") {
        if ((gamePhase == 5 && confirm("Any reinforcements not placed will get deleted, are you sure?")) || (gamePhase == 4 && confirm("Any aircraft not on carriers/airstrips or heli's not over land will get deleted.\nAre you sure you want to continue?")) || ((gamePhase != 4 && gamePhase != 5) && confirm("Are you sure you want to go to the next phase?"))) {
            let phpPhaseChange = new XMLHttpRequest();
            phpPhaseChange.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    //php file sent back a lot of variable, this is updating local js variables
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
                        // document.getElementById("battle_button").disabled = false;
                    } else {
                        document.getElementById("battle_button").disabled = true;
                    }
                    if (canUndo === "true") {
                        // document.getElementById("undo_button").disabled = false;
                    } else {
                        document.getElementById("undo_button").disabled = true;
                    }
                    if (canNextPhase === "true") {
                        // document.getElementById("phase_button").disabled = false;
                    } else {
                        document.getElementById("phase_button").disabled = true;
                    }

                    //SETTING THE CURRENT PHASE AND CURRENT TEAM DISPLAY
                    document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase - 1];
                    // document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
                    if (gameCurrentTeam === "Red") {
                        //red highlight
                        document.getElementById("red_team_indicator").classList.add("highlightedTeamRed");
                        //blue unhighlight
                        document.getElementById("blue_team_indicator").classList.remove("highlightedTeamBlue");
                    } else {
                        //blue highlight
                        document.getElementById("blue_team_indicator").classList.add("highlightedTeamBlue");
                        //red unhighlight
                        document.getElementById("red_team_indicator").classList.remove("highlightedTeamRed");
                    }
                    // NEWS ALERT PHASE
                    if (gamePhase === "1") {
                        document.getElementById("popupTitle").innerHTML = "News Alert";
                        document.getElementById("popupBodyHybridMenu").style.display = "none";
                        document.getElementById("popupBodyNews").style.display = "block";
                        document.getElementById("newsBodyText").innerHTML = newsText;
                        document.getElementById("newsBodySubText").innerHTML = newsEffectText;
                        document.getElementById("popup").style.display = "block";
                    } else {
                        document.getElementById("popup").style.display = "none";
                    }

                    // HYBRID WAR PHASE
                    if (gamePhase === "6") {
                        //convert the battle button to be a hybrid warfare shop button
                        document.getElementById("battle_button").innerHTML = "Hybrid Warfare";
                        // document.getElementById("battle_button").disabled = false;
                        document.getElementById("battle_button").onclick =function () {
                            if(document.getElementById("popup").style.display === "block"){
                                document.getElementById("popup").style.display = "none";
                            }
                            else{
                                document.getElementById("popuTitle").innerHTML = "Hybrid Warfare Menu";
                                document.getElementById("popupBodyNews").style.display = "none";
                                document.getElementById("popupBodyHybridMenu").style.display = "block";
                                document.getElementById("popup").style.display = "block";
                            }

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

                    if (gamePhase === "7") { //ROUND RECAP
                        let allPieces = document.querySelectorAll("[data-placementTeamId='" + myTeam + "']");
                        for (let x = 0; x < allPieces.length; x++) {
                            let currentPiece = allPieces[x];
                            let unitName = currentPiece.getAttribute("data-unitName");
                            let newMoves = unitsMoves[unitName];
                            currentPiece.setAttribute("data-placementCurrentMoves", newMoves);
                            currentPiece.setAttribute("data-placementBattleUsed", "0");
                        }
                    }

                    if (gamePhase === "1" && phaseText === "") {
                        userFeedback("News Phase. Click Next Phase to advance to next phase.");
                    } else if (gamePhase === "2" && phaseText === "") {
                        userFeedback("Buy Reinforcements Phase. Click Next Phase to advance to next phase.");
                    } else if (gamePhase === "3" && phaseText === "") {
                        userFeedback("Combat Phase. Click Next Phase to advance to next phase.");
                    } else if (gamePhase === "4" && phaseText === "") {
                        userFeedback("Fortify Phase. Click Next Phase to advance to next phase.");
                    } else if (gamePhase === "5" && phaseText === "") {
                        userFeedback("Place Reinforcements Phase. Click Next Phase to advance to next phase.");
                    } else if (gamePhase === "6" && phaseText === "") {
                        userFeedback("Hybrid Warfare Phase. Click Next Phase to advance to next phase.");
                    } else if (gamePhase === "7" && phaseText === "") {
                        userFeedback("Round Recap Phase. Click Next Phase to advance to next phase.");
                    }

                    if (phaseText !== "") {
                        userFeedback(phaseText);
                    }
                }
            };
            phpPhaseChange.open("GET", "gamePhaseChange.php", true);  // removes the element from the database
            phpPhaseChange.send();
        }
    } else {
        userFeedback("Unable to change phase.");
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

        document.getElementById("battle_button").onclick = function() {
            if (confirm("Are you sure you selected the right position? Click OK to select pieces.")) {
                battleSelectPosition();
            }
        };
        document.getElementById("battle_button").innerHTML = "Select Pieces";

        userFeedback("Now click on the zone that you want to attack. Then click the Select Pieces button.");
        //more visual indication of selecting position
        document.getElementById("whole_game").style.backgroundColor = "yellow";
    } else if (newSection === "selectPieces") {
        document.getElementById("battle_button").onclick = function() {
            if (confirm("Are you sure you selected the right pieces? Click OK to enter the battle.")) {
                battleSelectPieces();
            }
        };
        document.getElementById("battle_button").innerHTML = "Start Battle";
        hideIslands();
        gameBattleTurn = 0;
        userFeedback("Select the pieces you want to attack with. They must be adjacent to the zone being attacked. Then Start the Battle!");
        //more visual indication of selecting pieces
    } else if (newSection === "attack") {
        userFeedback("Attack the enemy by clicking on the unit you want to attack & the unit you want to attack with. Team's Pieces are color coded and placed into Attacker and Defender squares.");
        document.getElementById("whole_game").style.backgroundColor = "black";
        document.getElementById("battle_button").disabled = true;
        clearSelected();
        // clearSelectedPos();
        if (document.getElementById("center_defender").childNodes.length === 1 && document.getElementById("center_attacker").childNodes.length === 1) {
            // document.getElementById("attackButton").disabled = false;
            let upperBox = document.getElementById("battle_outcome");
            let defendPieceId = parseInt(document.getElementById("center_defender").childNodes[0].getAttribute("data-unitId"));
            let attackPieceId = parseInt(document.getElementById("center_attacker").childNodes[0].getAttribute("data-unitId"));
            let needToKill = 0;
            if (gameBattleSection === "attack") {
                needToKill = attackMatrix[attackPieceId][defendPieceId];
            } else {
                needToKill = attackMatrix[defendPieceId][attackPieceId];
            }
            upperBox.innerHTML = "You must roll a " + needToKill + " in order to kill.";
        } else {
            document.getElementById("attackButton").disabled = true;
        }
        document.getElementById("battleZonePopup").style.display = "block";
        document.getElementById("attackButton").innerHTML = "Attack!";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };

        if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {
            // document.getElementById("changeSectionButton").disabled = false;
        }
        // document.getElementById("changeSectionButton").disabled = false;
        document.getElementById("changeSectionButton").innerHTML = "End Attack/Start Counter";
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
        userFeedback("Attack the enemy by clicking on the unit you want to attack & the unit you want to attack with. Team's Pieces are color coded and placed into Attacker and Defender squares.");

        if (gameCurrentTeam === myTeam) {
            document.getElementById("changeSectionButton").disabled = true;
        } else {
            // document.getElementById("changeSectionButton").disabled = false;
        }

        // let centerDefend = document.getElementById("center_defender");
        // if (centerDefend.childNodes.length === 1) {
        //     document.getElementById("unused_defender").append(centerDefend.childNodes[0]);
        // }
        //
        // let centerAttack = document.getElementById("center_attacker");
        // if (centerAttack.childNodes.length === 1) {
        //     document.getElementById("unused_attacker").appendChild(centerAttack.childNodes[0]);
        // }

        document.getElementById("attackButton").disabled = true;
        document.getElementById("attackButton").innerHTML = "Counter Attack";
        document.getElementById("attackButton").onclick = function() { battleAttackCenter("defend"); };

        // document.getElementById("changeSectionButton").disabled = true;
        document.getElementById("changeSectionButton").innerHTML = "End Counter Attack";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("askRepeat"); };
    } else if (newSection === "askRepeat") {
        userFeedback("Click to continue the battle or leave early.");

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
        // document.getElementById("attackButton").disabled = false;
        document.getElementById("attackButton").onclick = function() { battleChangeSection("attack") };
        if ((gameCurrentTeam === myTeam && gameBattleSection === "askRepeat")) {
            // document.getElementById("attackButton").disabled = false;
        } else {
            document.getElementById("attackButton").disabled = true;
        }
        document.getElementById("changeSectionButton").disabled = true;
        document.getElementById("attackButton").disabled = true;

        document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
        document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };

        document.getElementById("actionPopupButton").disabled = true;
        document.getElementById("actionPopupButton").disabled = true;

        gameBattleTurn = parseInt(gameBattleTurn) + 1;

    } else if (newSection === "none") {
        userFeedback("Combat Phase. Click Next Phase to advance to next phase.");

        // document.getElementById("phase_button").disabled = false;
        // document.getElementById("undo_button").disabled = false;

        document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").classList.remove("selectedPos");

        let phpBattleEnding = new XMLHttpRequest();
        phpBattleEnding.open("POST", "battleEnding.php?gameId=" + gameId, true);
        phpBattleEnding.send();

        // gameTurn = parseInt(gameTurn) + 1;

        //clear out the divs for battle piece deletion
        document.getElementById("unused_attacker").innerHTML = null;
        document.getElementById("unused_defender").innerHTML = null;
        document.getElementById("used_attacker").innerHTML = null;
        document.getElementById("used_defender").innerHTML = null;
        document.getElementById("center_attacker").innerHTML = null;
        document.getElementById("center_defender").innerHTML = null;

        document.getElementById("battleZonePopup").style.display = "none";
        // document.getElementById("battle_button").disabled = false;
        document.getElementById("battle_button").innerHTML = "Select Battle";
        document.getElementById("battle_button").onclick = function() {
            if(confirm("Are you sure you want to battle?")) {
                battleChangeSection("selectPos");
            }
        };

        //check to see if flag ownership changed, if the position of battle was a flag position
        //TODO: doesn't check to see if the battle was won by a ground troop, or ground troop is available for capturing
        // let flagPositions = [55, 65, 75, 79, 83, 86, 90, 94, 97, 100, 103, 107, 111, 114];
        // let containerElement;
        // if (flagPositions.includes(parseInt(gameBattlePosSelected))) {
        //     containerElement = document.querySelector("[data-positionId='" + gameBattlePosSelected + "']");
        //     let parentTeam = containerElement.parentNode.classList[2];
        //     let newTeam;
        //     if (parentTeam === "Red") {
        //         newTeam = "Blue";
        //     } else {
        //         newTeam = "Red";
        //     }
        //     let changeOwnership = "true";
        //     let numChildren = containerElement.childElementCount;
        //     if (numChildren === 0) {
        //         changeOwnership = "false";
        //     }
        //     for (let x = 0; x < numChildren; x++) {
        //         if (containerElement.childNodes[x].getAttribute("data-placementTeamId") === parentTeam) {
        //             changeOwnership = "false";
        //         }
        //     }
        //     if (changeOwnership === "true") {
        //         //change css of parent
        //         let parent = containerElement.parentNode;
        //         parent.classList.remove(parentTeam);
        //         parent.classList.add(newTeam);
        //         //change css of parent parent
        //         let parentParent = parent.parentNode;
        //         parentParent.classList.remove(parentTeam);
        //         parentParent.classList.add(newTeam);
        //         //database change in games table
        //         let islandNumber = parentParent.id;
        //         let phpRequestTeamChange = new XMLHttpRequest();
        //         phpRequestTeamChange.open("POST", "gameIslandOwnerChange.php?gameId=" + gameId + "&islandToChange=" + islandNumber + "&newTeam=" + newTeam + "&myTeam=" + myTeam, true);
        //         phpRequestTeamChange.send();
        //     }
        // }
    }

    let posType = "defaultPos";
    if (gameBattlePosSelected != 999999) {
        posType = document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").getAttribute("data-positionType");
    }
    let phpBattleUpdate = new XMLHttpRequest();
    phpBattleUpdate.open("POST", "battleUpdateAttributes.php?gameBattleSection=" + gameBattleSection + "&gameBattleSubSection=" + gameBattleSubSection + "&gameBattleLastRoll=" + gameBattleLastRoll + "&gameBattleLastMessage=" + gameBattleLastMessage + "&gameBattlePosSelected=" + gameBattlePosSelected + "&gameBattleTurn=" + gameBattleTurn + "&posType=" + posType, true);
    phpBattleUpdate.send();
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
    // clearSelectedPos();
    clearSelected();
    battleChangeSection("attack");
}

function battleSelectPosition() {
    if (document.getElementsByClassName("selectedPos").length === 0) {
        alert("didn't select a position");
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

    document.getElementById("battle_outcome").innerHTML = "";

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
            // document.getElementById("attackButton").disabled = false;
            //show what is needed for a hit?
            let upperBox = document.getElementById("battle_outcome");
            let defendPieceId = parseInt(document.getElementById("center_defender").childNodes[0].getAttribute("data-unitId"));
            let attackPieceId = parseInt(document.getElementById("center_attacker").childNodes[0].getAttribute("data-unitId"));
            let needToKill = 0;
            if (gameBattleSection === "attack") {
                needToKill = attackMatrix[attackPieceId][defendPieceId];
            } else {
                needToKill = attackMatrix[defendPieceId][attackPieceId];
            }
            upperBox.innerHTML = "You must roll a " + needToKill + " in order to kill.";
            userFeedback("Click the attack button to roll!");
        } else {
            document.getElementById("attackButton").disabled = true;
            userFeedback("Click both attacker and defender pieces into the center to attack.");
        }
    } else {
        userFeedback("Unable to select battle pieces. Not your turn.");
    }

    event.stopPropagation();
}

function battleEndRoll() {
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

    document.getElementById("battle_outcome").innerHTML = "";

    if (parseInt(centerAttackPiece.getAttribute("data-battlePieceWasHit")) === 1) {
        let pieceId = centerAttackPiece.getAttribute("data-battlePieceId");
        // document.querySelector("[data-placementId='" + pieceId + "']").remove();  //mainboard
        // centerAttackPiece.remove();  //battlezone
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

    if (parseInt(centerDefendPiece.getAttribute("data-battlePieceWasHit")) === 1) {
        let pieceId = centerDefendPiece.getAttribute("data-battlePieceId");
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
    userFeedback("Attacked!");

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

    let attackUnitName = unitNames[attackUnitId];
    let defendUnitName = unitNames[defendUnitId];

    let posType = "defaultPos";
    if (gameBattlePosSelected != 999999) {
        posType = document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").getAttribute("data-positionType");
    }


    let boostedAttack = 0;
    //army company with artilley or marine platoon with heli
    let child;
    if (type === "attack") {
        for (let x = 0; x < document.getElementById("unused_attacker").childNodes.length; x++) {
            child = document.getElementById("unused_attacker").childNodes[x];
            if ((child.getAttribute("data-unitId") == 5 && attackUnitId == 4) || (child.getAttribute("data-unitId") == 7 && attackUnitId == 9)) {
                boostedAttack = 1;
            }
        }
        for (let x = 0; x < document.getElementById("used_attacker").childNodes.length; x++) {
            child = document.getElementById("used_attacker").childNodes[x];
            if ((child.getAttribute("data-unitId") == 5 && attackUnitId == 4) || (child.getAttribute("data-unitId") == 7 && attackUnitId == 9)) {
                boostedAttack = 1;
            }
        }
    } else {
        for (let x = 0; x < document.getElementById("unused_defender").childNodes.length; x++) {
            child = document.getElementById("unused_defender").childNodes[x];
            if ((child.getAttribute("data-unitId") == 5 && attackUnitId == 4) || (child.getAttribute("data-unitId") == 7 && attackUnitId == 9)) {
                boostedAttack = 1;
            }
        }
        for (let x = 0; x < document.getElementById("used_defender").childNodes.length; x++) {
            child = document.getElementById("used_defender").childNodes[x];
            if ((child.getAttribute("data-unitId") == 5 && attackUnitId == 4) || (child.getAttribute("data-unitId") == 7 && attackUnitId == 9)) {
                boostedAttack = 1;
            }
        }
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
                    // actionButton.disabled = false;
                } else {
                    actionButton.disabled = true;
                }

                actionButton.innerHTML = "click to go back to Choosing";
                actionButton.onclick = function() { battleEndRoll(); };
            } else if (new_gameBattleSubSection === "continue_choosing" && gameBattleSection === "counter") {
                if (myTeam === gameCurrentTeam) {
                    actionButton.disabled = true;
                } else {
                    // actionButton.disabled = false;
                }

                actionButton.innerHTML = "click to go back to Choosing";
                actionButton.onclick = function() { battleEndRoll(); };
            }

            if (decoded.wasHit === 2) {
                //set both pieces to wasHit = 0
                document.getElementById("center_attacker").childNodes[0].setAttribute("data-battlePieceWasHit", 0);
                document.getElementById("center_defender").childNodes[0].setAttribute("data-battlePieceWasHit", 0);
            } else {
                pieceAttacked.setAttribute("data-battlePieceWasHit", decoded.wasHit);
            }

            gameBattleLastRoll = decoded.lastRoll;
            gameBattleSubSection = decoded.new_gameBattleSubSection;
            gameBattleLastMessage = decoded.gameBattleLastMessage;
            document.getElementById("lastBattleMessage").innerHTML = gameBattleLastMessage;
            battleChangeSection(gameBattleSection);  //This call to change roll and subsection
            document.getElementById("actionPopupButton").style.display = "none";
            document.getElementById("lastBattleMessage").style.display = "none";
            document.getElementById("battleActionPopup").style.display = "block";
            rollDice();
        }
    };
    phpAttackCenter.open("GET", "battleAttackCenter.php?attackUnitId=" + attackUnitId + "&boostedAttack=" + boostedAttack + "&defendUnitId=" + defendUnitId + "&attackUnitName=" + attackUnitName + "&defendUnitName=" + defendUnitName + "&gameBattleSection=" + gameBattleSection + "&gameBattleSubSection=" + gameBattleSubSection + "&pieceId=" + pieceAttacked.getAttribute("data-battlePieceId") + "&posType=" + posType, true);
    phpAttackCenter.send();
}


let updateWait;
let waitTime = 10;

function waitForUpdate() {
    let phpUpdateBoard = new XMLHttpRequest();
    phpUpdateBoard.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let decoded = JSON.parse(this.responseText);

            if (decoded.updateType === "pieceMove") {
                updatePieceMove(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewPositionId), parseInt(decoded.updateNewContainerId), parseInt(decoded.updateNewMoves));
            } else if (decoded.updateType === "updateMoves2") {
                updateMovesAll();
            } else if (decoded.updateType === "logout") {
                logout2();
            } else if (decoded.updateType === "pieceDelete") {
                updatePieceDelete(decoded.updatePlacementId);
            } else if (decoded.updateType === "rollDie") {
                updateRollDie(decoded.updatePlacementId);
            } else if (decoded.updateType === "pieceTrash") {
                updatePieceTrash(decoded.updatePlacementId);
            } else if (decoded.updateType === "piecePurchase") {
                updatePiecePurchase(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewUnitId), decoded.updateTeam);
            } else if (decoded.updateType === "battlePieceMove") {
                updateBattlePieceMove(parseInt(decoded.updatePlacementId), decoded.updateBattlePieceState);
            } else if (decoded.updateType === "phaseChange") {
                updateNextPhase();
            } else if (decoded.updateType === "positionSelected") {
                updateBattlePositionSelected(decoded.updateBattlePositionSelectedPieces, parseInt(decoded.updateNewPositionId));
            } else if (decoded.updateType === "piecesSelected") {
                updateBattlePiecesSelected(decoded.updateBattlePiecesSelected);
            } else if (decoded.updateType === "battleAttacked") {
                updateBattleAttack(parseInt(decoded.updateNewMoves));
            } else if (decoded.updateType === "battleEnding") {
                updateBattleEnding();
            } else if (decoded.updateType === "battleSectionChange") {
                updateBattleSection();
            } else if (decoded.updateType === "islandChange") {
                updateIslandChange(decoded.updateIsland, decoded.updateIslandTeam);
            } else if (decoded.updateType === "battlePieceRemove") {
                updateBattlePieceRemove(decoded.updatePlacementId);
            } else if (decoded.updateType === "updateMoves") {
                updateMoves(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewMoves));
            } else if (decoded.updateType === "updateMissile") {
                updateMissileOwner(parseInt(decoded.updatePlacementId));
            }

            updateWait = window.setTimeout("waitForUpdate()", waitTime);
        }
    };
    phpUpdateBoard.open("GET", "updateBoardSpectator.php?gameId=" + gameId + "&myTeam=" + myTeam, true);  // removes the element from the database
    phpUpdateBoard.send();
}

function updateMissileOwner(placementId) {
    let missile = document.querySelector("[data-placementId='" + placementId + "']");
    let oldTeam = missile.getAttribute("data-placementTeamId");
    let newTeam = "Red";
    if (oldTeam === "Red") {
        newTeam = "Blue";
    }
    missile.classList.remove(oldTeam);
    missile.classList.add(newTeam);
    missile.setAttribute("data-placementTeamId", newTeam);
}

function updateBattlePieceRemove(placementId) {
    document.querySelector("[data-battlePieceId='" + placementId + "']").remove();  //battlezone
    // userFeedback("Piece(s) left battle.");
}

function updateRollDie(placementId) {
    document.querySelector("[data-placementId='" + placementId + "']").remove();  //mainboard
}

function updateIslandChange(islandIdentifier, newTeam) {
    let islandMain = document.getElementById(islandIdentifier);
    let islandPop = document.getElementById(islandIdentifier + "_pop");
    let oldTeam;
    if (islandMain.classList.contains("Red")) {
        oldTeam = "Red";
    } else {
        oldTeam = "Blue";
    }
    islandMain.classList.remove(oldTeam);
    islandMain.classList.add(newTeam);
    islandPop.classList.remove(oldTeam);
    islandPop.classList.add(newTeam);
    userFeedback("This island is now owned by " + newTeam + " Team! If there was a missile, it belongs to them now.");
}

function updateBattlePieceMove(battlePieceId, battlePieceState) {
    let battlePiece = document.querySelector("[data-battlePieceId='" + battlePieceId + "']");
    document.querySelector("[data-boxId='" + battlePieceState + "']").appendChild(battlePiece);
}

function updatePiecePurchase(placementId, unitId, updateTeam) {
    let purchaseContainer = document.getElementById("purchased_container");
    let echoString = "";
    echoString += "<div class='" + unitNames[unitId] + " gamePiece " + updateTeam + "' title='" + unitNames[unitId] + "&#013;Moves: " + unitsMoves[unitNames[unitId]] + "' data-placementId='" + placementId + "' data-placementBattleUsed='0' data-placementCurrentMoves='" + unitsMoves[unitNames[unitId]] + "' data-placementContainerId='999999' data-placementTeamId='" + updateTeam + "' data-unitName='" + unitNames[unitId] + "' data-unitId='" + unitId + "' draggable='true' ondragstart='pieceDragstart(event, this)' onclick='pieceClick(event, this);' ondragenter='pieceDragenter(event, this);' ondragleave='pieceDragleave(event, this);'>";
    if (unitNames[unitId] === "Transport" || unitNames[unitId] === "AircraftCarrier") {
        let classthing;
        if (unitNames[unitId] === "Transport") {
            classthing = "transportContainer";
        } else {
            classthing = "aircraftCarrierContainer";
        }
        echoString += "<div class='" + classthing + " " + updateTeam + "' data-containerPopped='false' data-positionContainerId='" + placementId + "' data-positionType='" + classthing + "' ondragleave='containerDragleave(event, this);'  ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);'></div>";
    }
    echoString += "</div>";  // end the overall piece
    purchaseContainer.innerHTML += echoString;
}

function updateMoves(placementId, newMoves) {
    let pieceToUpdate = document.querySelector("[data-placementId='" + placementId + "']");
    pieceToUpdate.setAttribute("data-placementCurrentMoves", newMoves);
    let unitName = pieceToUpdate.getAttribute("data-unitName");
    pieceToUpdate.setAttribute("title", unitName + "\n" +
        "Moves: " + newMoves);
}

function updateMovesAll() {
    let allPieces = document.querySelectorAll("[data-placementTeamId='" + gameCurrentTeam + "']");
    for (let x = 0; x < allPieces.length; x++) {
        let pieceToUpdate = allPieces[x];
        let pieceToUpdateName = pieceToUpdate.getAttribute("data-unitName");
        let newMoves = unitsMoves[pieceToUpdateName];
        pieceToUpdate.setAttribute("data-placementCurrentMoves", newMoves);
        pieceToUpdate.setAttribute("title", pieceToUpdateName + "\n" +
            "Moves: " + newMoves);
    }
}

function updatePieceMove(placementId, newPositionId, newContainerId, newMoves){
    let pieceToMove = document.querySelector("[data-placementId='" + placementId + "']");
    let theContainer;
    if (newContainerId !== 999999) {
        theContainer = document.querySelector("[data-placementId='" + newContainerId + "']").firstChild;
    } else {
        theContainer = document.querySelector("[data-positionId='" + newPositionId + "']");
    }
    theContainer.append(pieceToMove);
    let unitName = pieceToMove.getAttribute("data-unitName");
    pieceToMove.setAttribute("title", unitName + "\n" +
        "Moves: " + newMoves);
}

function updatePieceDelete(placementId) {
    document.querySelector("[data-placementId='" + placementId + "']").remove();  //mainboard
    document.querySelector("[data-battlePieceId='" + placementId + "']").remove();  //battlezone
}

function updatePieceTrash(placementId) {
    let pieceToTrash = document.querySelector("[data-placementId='" + placementId + "']");
    if (pieceToTrash != null) {
        pieceToTrash.remove();
    }
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

            document.getElementById("popupTitle").innerHTML = "News Alert";

            document.getElementById("newsBodyText").innerHTML = newsText;
            document.getElementById("newsBodySubText").innerHTML = newsEffectText;

            document.getElementById("red_rPoints_indicator").innerHTML = gameRedRpoints;
            document.getElementById("blue_rPoints_indicator").innerHTML = gameBlueRpoints;
            document.getElementById("red_hPoints_indicator").innerHTML = gameRedHpoints;
            document.getElementById("blue_hPoints_indicator").innerHTML = gameBlueHpoints;

            if (canAttack === "true") {
                // document.getElementById("battle_button").disabled = false;
            } else {
                document.getElementById("battle_button").disabled = true;
            }
            if (canUndo === "true") {
                // document.getElementById("undo_button").disabled = false;
            } else {
                document.getElementById("undo_button").disabled = true;
            }
            if (canNextPhase === "true") {
                // document.getElementById("phase_button").disabled = false;
            } else {
                document.getElementById("phase_button").disabled = true;
            }
            if (gamePhase === "1") {
                document.getElementById("popupTitle").innerHTML = "News Alert";
                document.getElementById("popupBodyNews").style.display = "block";
                document.getElementById("popupBodyHybridMenu").style.display = "none";
                document.getElementById("popup").style.display = "block";
            } else {
                document.getElementById("popup").style.display = "none";
            }

            // HYBRID WAR PHASE
            if (gamePhase === "6") {
                //convert the battle button to be a hybrid warfare shop button
                document.getElementById("battle_button").innerHTML = "Hybrid Warfare";
                if (myTeam === gameCurrentTeam) {
                    // document.getElementById("battle_button").disabled = false;
                } else {
                    document.getElementById("battle_button").disabled = true;
                }

                document.getElementById("battle_button").onclick =function () {
                    if(document.getElementById("popup").style.display === "block"){
                        document.getElementById("popup").style.display = "none";
                    }
                    else{
                        document.getElementById("popupTitle").innerHTML = "Hybrid Warfare Menu";
                        document.getElementById("popupBodyNews").style.display = "none";
                        document.getElementById("popupBodyHybridMenu").style.display = "block";
                        document.getElementById("popup").style.display = "block";
                    }
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

            document.getElementById("phase_indicator").innerHTML = "Current Phase = " + phaseNames[gamePhase - 1];
            // document.getElementById("team_indicator").innerHTML = "Current Team = " + gameCurrentTeam;
            if (gameCurrentTeam === "Red") {
                //red highlight
                document.getElementById("red_team_indicator").classList.add("highlightedTeamRed");
                //blue unhighlight
                document.getElementById("blue_team_indicator").classList.remove("highlightedTeamBlue");
            } else {
                //blue highlight
                document.getElementById("blue_team_indicator").classList.add("highlightedTeamBlue");
                //red unhighlight
                document.getElementById("red_team_indicator").classList.remove("highlightedTeamRed");
            }

            //TODO: could remove 'click' from the other player's screen by testing for currentteam and myteam
            if (gamePhase === "1") {
                userFeedback("News Phase. Click Next Phase to advance to next phase.");
            } else if (gamePhase === "2") {
                userFeedback("Buy Reinforcements Phase. Click Next Phase to advance to next phase.");
            } else if (gamePhase === "3") {
                userFeedback("Combat Phase. Click Next Phase to advance to next phase.");
            } else if (gamePhase === "4") {
                userFeedback("Fortify Phase. Click Next Phase to advance to next phase.");
            } else if (gamePhase === "5") {
                userFeedback("Place Reinforcements Phase. Click Next Phase to advance to next phase.");
            } else if (gamePhase === "6") {
                userFeedback("Hybrid Warfare Phase. Click Next Phase to advance to next phase.");
            } else if (gamePhase === "7") {
                userFeedback("Round Recap Phase. Click Next Phase to advance to next phase.");
            }
        }
    };
    phpPhaseChange.open("GET", "updateGetPhase.php", true);  // removes the element from the database
    phpPhaseChange.send();
}

function updateBattleAttack(wasHit) {

    //get everything from database again (subsection / lastroll / lastmessage)
    //display and make buttons disabled or not based upon the team or current team

    let phpBattleUpdate = new XMLHttpRequest();
    phpBattleUpdate.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let decoded = JSON.parse(this.responseText);

            let pieceAttacked;
            if (gameBattleSubSection === "defense_bonus") {
                pieceAttacked = document.getElementById("center_attacker").childNodes[0];
            } else {
                pieceAttacked = document.getElementById("center_defender").childNodes[0];
            }

            gameBattleSection = decoded.gameBattleSection;
            gameBattleSubSection = decoded.gameBattleSubSection;
            gameBattleLastRoll = decoded.gameBattleLastRoll;
            gameBattleLastMessage = decoded.gameBattleLastMessage;

            if (parseInt(wasHit) === 2) {
                document.getElementById("center_attacker").childNodes[0].setAttribute("data-battlePieceWasHit", 0);
                document.getElementById("center_defender").childNodes[0].setAttribute("data-battlePieceWasHit", 0);
            } else {
                pieceAttacked.setAttribute("data-battlePieceWasHit", wasHit);
            }

            document.getElementById("lastBattleMessage").innerHTML = gameBattleLastMessage;
            document.getElementById("lastBattleMessage").style.display = "none";
            document.getElementById("actionPopupButton").style.display = "none";
            document.getElementById("battleActionPopup").style.display = "block";
            if (gameBattleSubSection === "defense_bonus") {
                // document.getElementById("actionPopupButton").disabled = false;
                document.getElementById("actionPopupButton").innerHTML = "HIT! The Defender's unit was destroyed!! The Defender" +
                    "has the opportunity to knock out the Attacker's unit. Roll for defense bonus!";
                document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
            } else if (gameBattleSubSection === "continue_choosing") {
                document.getElementById("actionPopupButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
                document.getElementById("actionPopupButton").onclick = function() { battleEndRoll(); };
            }
            rollDice();
        }
    };
    phpBattleUpdate.open("GET", "updateGetBattle.php", true);  // removes the element from the database
    phpBattleUpdate.send();

}

function updateBattleEnding() {
    //mostly graphical stuff for end, next battle is completely re-do the innerhtml for stuff anyways
    document.getElementById("battleZonePopup").style.display = "none";
}

function updateBattlePositionSelected(positionPiecesHTML, positionSelected) {
    document.getElementById("unused_defender").innerHTML = positionPiecesHTML;
    let positionDiv = document.querySelector("[data-positionId='" + positionSelected + "']");
    positionDiv.classList.add("selectedPos");
    //Highlight the popups / regular if it is a land position (exclude big islands)
    if (positionSelected > 74) {
        positionDiv.parentNode.classList.add("selectedPos");
        positionDiv.parentNode.parentNode.classList.add("selectedPos");
    }
    userFeedback("Other team selected battle position.");
}

function updateBattlePiecesSelected(piecesSelectedHTML) {
    document.getElementById("unused_attacker").innerHTML = piecesSelectedHTML;

    hideContainers("transportContainer");
    hideContainers("aircraftCarrierContainer");
    clearHighlighted();
    // clearSelectedPos();
    clearSelected();

    document.getElementById("battleZonePopup").style.display = "block";
    document.getElementById("attackButton").innerHTML = "Attack!";  //already disabled by default?
    document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };
    document.getElementById("changeSectionButton").innerHTML = "End Attack/Start Counter";

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
    let phpBattleUpdate = new XMLHttpRequest();
    phpBattleUpdate.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let decoded = JSON.parse(this.responseText);
            gameBattleSection = decoded.gameBattleSection;
            gameBattleSubSection = decoded.gameBattleSubSection;
            gameBattleLastRoll = decoded.gameBattleLastRoll;

            gameBattleLastMessage = decoded.gameBattleLastMessage;
            document.getElementById("lastBattleMessage").innerHTML = gameBattleLastMessage;

            // let centerDefend = document.getElementById("center_defender");
            // if (centerDefend.childNodes.length === 1) {
            //     document.getElementById("unused_defender").append(centerDefend.childNodes[0]);
            // }
            //
            // let centerAttack = document.getElementById("center_attacker");
            // if (centerAttack.childNodes.length === 1) {
            //     document.getElementById("unused_attacker").appendChild(centerAttack.childNodes[0]);
            // }

            if (gameBattleSubSection !== "choosing_pieces") {
                document.getElementById("battleActionPopup").style.display = "block";
                if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "attack") {
                    if (myTeam !== gameCurrentTeam) {
                        // document.getElementById("actionPopupButton").disabled = false;
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
                        // document.getElementById("actionPopupButton").disabled = false;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "HIT!! The Defender's unit was destroyed!! The Defender " +
                        "has the opportunity to knock out the Attacker's unit. Defender: roll for defense bonus!";
                    document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
                } else if (gameBattleSubSection === "continue_choosing" && gameBattleSection === "attack") {
                    if (myTeam !== gameCurrentTeam) {
                        document.getElementById("actionPopupButton").disabled = true;
                    } else {
                        // document.getElementById("actionPopupButton").disabled = false;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "click to go back to Choosing";  //attack popup was open, and clicked to roll defense bonus, now click to go back
                    document.getElementById("actionPopupButton").onclick = function() { battleEndRoll(); };
                } else if (gameBattleSubSection === "continue_choosing" && gameBattleSection === "counter") {
                    if (myTeam !== gameCurrentTeam) {
                        // document.getElementById("actionPopupButton").disabled = false;
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
                document.getElementById("attackButton").innerHTML = "Attack!";
                document.getElementById("attackButton").onclick = function() { battleAttackCenter("attack"); };
                document.getElementById("changeSectionButton").innerHTML = "End Attack/Start Counter";
                document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("counter") };
            } else if (gameBattleSection === "counter") {
                document.getElementById("attackButton").innerHTML = "Counter Attack";
                document.getElementById("attackButton").onclick = function() { battleAttackCenter("defend"); };
                document.getElementById("changeSectionButton").innerHTML = "End Counter Attack";
                document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("askRepeat") };
            } else if (gameBattleSection === "askRepeat") {
                document.getElementById("attackButton").innerHTML = "Click to Repeat";
                document.getElementById("attackButton").onclick = function() { battleChangeSection("attack") };
                document.getElementById("changeSectionButton").innerHTML = "Click to Exit";
                document.getElementById("changeSectionButton").onclick = function() { battleChangeSection("none") };
            }

            if ((gameCurrentTeam === myTeam && gameBattleSection === "attack") || (gameCurrentTeam !== myTeam && gameBattleSection === "counter")) {

                if (document.getElementById("center_defender").childNodes.length === 1 && document.getElementById("center_attacker").childNodes.length === 1) {
                    // document.getElementById("attackButton").disabled = false;
                    let upperBox = document.getElementById("battle_outcome");
                    let defendPieceId = parseInt(document.getElementById("center_defender").childNodes[0].getAttribute("data-unitId"));
                    let attackPieceId = parseInt(document.getElementById("center_attacker").childNodes[0].getAttribute("data-unitId"));
                    let needToKill = 0;
                    if (gameBattleSection === "attack") {
                        needToKill = attackMatrix[attackPieceId][defendPieceId];
                    } else {
                        needToKill = attackMatrix[defendPieceId][attackPieceId];
                    }
                    upperBox.innerHTML = needToKill;
                } else {
                    document.getElementById("attackButton").disabled = true;
                }


                // document.getElementById("changeSectionButton").disabled = false;
            } else {
                document.getElementById("attackButton").disabled = true;
                document.getElementById("changeSectionButton").disabled = true;
            }

            if (gameBattleSection === "askRepeat" && myTeam === gameCurrentTeam) {
                // document.getElementById("attackButton").disabled = false;
                // document.getElementById("changeSectionButton").disabled = false;
            } else if (gameBattleSection === "askRepeat" && myTeam !== gameCurrentTeam) {
                document.getElementById("attackButton").disabled = true;
                document.getElementById("changeSectionButton").disabled = true;
            }

            if (gameBattleSection === "none") {
                document.getElementById("battleZonePopup").style.display = "none";

                //clear out the pieces from html(battlepieces)
                document.getElementById("unused_attacker").innerHTML = null;
                document.getElementById("unused_defender").innerHTML = null;
                document.getElementById("used_attacker").innerHTML = null;
                document.getElementById("used_defender").innerHTML = null;
                document.getElementById("center_attacker").innerHTML = null;
                document.getElementById("center_defender").innerHTML = null;
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

//--------------------------
function hybridDeletePiece() {
    //Rods From God
    let thisPoints = gameRedHpoints;
    if (myTeam === "Blue") {
        thisPoints = gameBlueHpoints;
    }
    if (thisPoints >= 6) {
        if (confirm("Are you sure you want to delete a piece?")) {
            document.getElementById("popup").style.display = "none";
            deleteHybridState = "true";
            document.getElementById("battle_button").disabled = true;
            document.getElementById("phase_button").disabled = true;
            document.getElementById("whole_game").style.backgroundColor = "yellow";
        }
    } else {
        userFeedback("Not enough Hybrid Points");
    }

}

function hybridAddMove() {
    //Advanced Remote Sensing
    let thisPoints = gameRedHpoints;
    if (myTeam === "Blue") {
        thisPoints = gameBlueHpoints;
    }
    if (thisPoints >= 8) {
        if (confirm("Are you sure you want to add +1 moves to your pieces next turn?")) {
            let phpAddMove = new XMLHttpRequest();
            phpAddMove.open("GET", "hybridAddMove.php", true);
            phpAddMove.send();
        }
    } else {
        userFeedback("Not enough Hybrid Points");
    }
}

function hybridHumanitary() {
    //Humanitarian Option
    let thisPoints = gameRedHpoints;
    if (myTeam === "Blue") {
        thisPoints = gameBlueHpoints;
    }
    if (thisPoints >= 3) {
        if (confirm("Are you sure you want to convert hybrid points to reinforcement points")) {
            let phpHumanitary = new XMLHttpRequest();
            phpHumanitary.open("GET", "hybridHumanitary.php", true);
            phpHumanitary.send();
        }
    } else {
        userFeedback("Not enough Hybrid Points");
    }
}

function hybridDisableAircraft() {
    //Goldeneye
    let thisPoints = gameRedHpoints;
    if (myTeam === "Blue") {
        thisPoints = gameBlueHpoints;
    }
    if (thisPoints >= 10) {
        if (confirm("Are you sure you want to disable all aircraft?")) {
            let phpDisableAircraft = new XMLHttpRequest();
            phpDisableAircraft.open("GET", "hybridDisableAircraft.php", true);
            phpDisableAircraft.send();
        }
    } else {
        userFeedback("Not enough Hybrid Points");
    }
}

function hybridDisableAirfield() {
    //Air Traffic Control Scramble
    let thisPoints = gameRedHpoints;
    if (myTeam === "Blue") {
        thisPoints = gameBlueHpoints;
    }
    if (thisPoints >= 3) {
        if (confirm("Are you sure you want to disable airfield?")) {
            //delete the points from this team? (how to deal with this (where))
            document.getElementById("popup").style.display = "none";
            disableAirfieldHybridState = "true";
            document.getElementById("battle_button").disabled = true;
            document.getElementById("phase_button").disabled = true;
            document.getElementById("whole_game").style.backgroundColor = "yellow";
        }
    } else {
        userFeedback("Not enough Hybrid Points");
    }

}

function hybridNuke() {
    //Nuclear Strike
    let thisPoints = gameRedHpoints;
    if (myTeam === "Blue") {
        thisPoints = gameBlueHpoints;
    }
    if (thisPoints >= 12) {
        if (confirm("Are you sure you want to nuke an island?")) {
            hideIslands();
            document.getElementById("popup").style.display = "none";
            nukeHybridState = "true";
            document.getElementById("battle_button").disabled = true;
            document.getElementById("phase_button").disabled = true;
            document.getElementById("whole_game").style.backgroundColor = "yellow";
        }
    } else {
        userFeedback("Not enough Hybrid Points");
    }

}

function hybridBank() {
    //Bank Drain
    //select an island to count towards your rpoints? (not big) (2 turns)
    let thisPoints = gameRedHpoints;
    if (myTeam === "Blue") {
        thisPoints = gameBlueHpoints;
    }
    if (thisPoints >= 4) {
        if (confirm("Are you sure you want use Bank?")) {
            hideIslands();
            document.getElementById("popup").style.display = "none";
            bankHybridState = "true";
            document.getElementById("battle_button").disabled = true;
            document.getElementById("phase_button").disabled = true;
            document.getElementById("whole_game").style.backgroundColor = "yellow";
        }
    } else {
        userFeedback("Not enough Hybrid Points");
    }

}
//--------------------------

function rollDice(){
    let timeBetween = 375;
    let numRolls = 12;
    let thingy;
    let i;
    for (i = 1; i < numRolls; i++) {
        let randomRoll = Math.floor(Math.random() * 6) + 1;
        thingy = setTimeout(function () {showDice(randomRoll)}, (i)*timeBetween);
    }
    thingy = setTimeout(function () {showDice(gameBattleLastRoll); document.getElementById("actionPopupButton").style.display = "block"; document.getElementById("lastBattleMessage").style.display = "block";}, (i)*timeBetween);
}

function showDice(diceNum){
    //hide the other dice
    document.getElementById("dice_image1").style.display = "none";
    document.getElementById("dice_image2").style.display = "none";
    document.getElementById("dice_image3").style.display = "none";
    document.getElementById("dice_image4").style.display = "none";
    document.getElementById("dice_image5").style.display = "none";
    document.getElementById("dice_image6").style.display = "none";
    //display the correct dice
    document.getElementById("dice_image" + diceNum).style.display = "block";
}


waitForUpdate();