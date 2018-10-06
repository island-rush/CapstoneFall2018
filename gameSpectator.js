//Javascript Functions used by the Island Rush Game Spectator Mode
//Created by C1C Spencer Adolph (9/28/2018)

//First function called to load the game...
function bodyLoader() {
    // let phpPositionGet = new XMLHttpRequest();
    // phpPositionGet.onreadystatechange = function () {
    //     if (this.readyState === 4 && this.status === 200) {
    //         let decoded = JSON.parse(this.responseText);
    //         gameBattleAdjacentArray = decoded.adjacentArray;
    //     }
    // };
    // phpPositionGet.open("POST", "battleGetAdjacentPos.php?positionSelected=" + gameBattlePosSelected, true);
    // phpPositionGet.send();


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
        // document.getElementById("phase_button").disabled = false;
        // document.getElementById("battle_button").disabled = false;
        // document.getElementById("battle_button").innerHTML = "Select Battle";
        // document.getElementById("battle_button").onclick = function() {
        //     if (confirm("Are you sure you want to battle?")) {
        //         battleChangeSection("selectPos");
        //     }
        // };
    } else if (gameBattleSection === "selectPos") {
        // document.getElementById("phase_button").disabled = true;
        // document.getElementById("battle_button").disabled = false;
        // document.getElementById("battle_button").innerHTML = "Select Pieces";
        // document.getElementById("battle_button").onclick = function() { battleSelectPosition(); };
        // userFeedback("Now click on the zone that you want to attack. Then click the Select Pieces button, where the Battle button used to be.");
        //more visual indication of selecting position
        document.getElementById("whole_game").style.backgroundColor = "yellow";
    } else if (gameBattleSection === "selectPieces") {
        // document.getElementById("phase_button").disabled = true;
        // document.getElementById("battle_button").disabled = false;
        // document.getElementById("battle_button").innerHTML = "Start Battle";
        // document.getElementById("battle_button").onclick = function() { battleSelectPieces(); };
        document.getElementById("whole_game").style.backgroundColor = "yellow";
        document.querySelector("[data-positionId='" + gameBattlePosSelected + "']").classList.add("selectedPos");

        // userFeedback("Select the pieces you want to attack with. They must be adjacent to the zone being attacked. Then Start the Battle!");
    } else {
        // userFeedback("Disable phase button?");
        // document.getElementById("battle_button").disabled = true;
        // document.getElementById("battle_button").innerHTML = "Select Battle";
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
            document.getElementById("actionPopupButton").innerHTML = "click to roll for defense bonus";
            document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
        } else if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "counter") {
            if (myTeam !== gameCurrentTeam) {
                document.getElementById("actionPopupButton").disabled = true;
            } else {
                // document.getElementById("actionPopupButton").disabled = false;
            }
            document.getElementById("actionPopupButton").innerHTML = "click to roll for defense bonus";
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
            // document.getElementById("/button").disabled = false;
        } else {
            // document.getElementById("battle_button").disabled = true;
        }
    } else {
        // document.getElementById("battle_button").disabled = true;
    }
    if (canUndo === "true") {
        if (gameBattleSection === "none") {
            // document.getElementById("undo_button").disabled = false;
        }
    } else {
        // document.getElementById("undo_button").disabled = true;
    }
    if (canNextPhase === "true") {
        if (gameBattleSection === "none") {
            // document.getElementById("phase_button").disabled = false;
        } else {
            // document.getElementById("phase_button").disabled = true;
        }
    } else {
        // document.getElementById("phase_button").disabled = true;
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
        // userFeedback("Click Next Phase to advance to next phase.");
    } else {
        // Hide the popup because it shouldnt be showing.
        document.getElementById("popup").style.display = "none";
    }
    // HYBRID WAR PHASE
    if (gamePhase === "6") {
        // if they refresh, close the popup. they can press button again
        document.getElementById("popup").style.display = "none";
        //convert the battle button to be a hybrid warfare shop button
        // document.getElementById("battle_button").innerHTML = "Hybrid Warfare";
        // document.getElementById("battle_button").disabled = false;
        // document.getElementById("battle_button").onclick =function () {
        //     document.getElementById("popupTitle").innerHTML = "Hybrid Warfare Tool";
        //     document.getElementById("popupBodyNews").style.display = "none";
        //     document.getElementById("popupBodyHybrid").style.display = "block";
        //     document.getElementById("setRedRpoints").value = gameRedRpoints;
        //     document.getElementById("setRedHpoints").value = gameRedHpoints;
        //     document.getElementById("setBlueRpoints").value = gameBlueRpoints;
        //     document.getElementById("setBlueHpoints").value = gameBlueHpoints;
        //     document.getElementById("popup").style.display = "block";
        // };
    }

    if (document.getElementById("battleActionPopup").style.display == "block") {
        showDice(gameBattleLastRoll);
    }
    //access the battle popup
    //change the dice image to the last roll

}

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
            if (unitName === "transport" || unitName === "aircraftCarrier") {
                hideContainers("transportContainer");
                hideContainers("aircraftCarrierContainer");
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

function hideContainers(containerType) {
    let s = document.getElementsByClassName(containerType);
    let r;
    for (r = 0; r < s.length; r++) {
        s[r].style.display = "none";
        s[r].parentNode.style.zIndex = 15;
        s[r].setAttribute("data-containerPopped", "false");
    }
}

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

function hideIslands() {
    let x = document.getElementsByClassName("special_island3x3");
    let i;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
        x[i].parentNode.style.zIndex = 10;  //10 is the default
        x[i].parentNode.setAttribute("data-islandPopped", "false");
    }
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
    if (gameBattleSection === "selectPos") {
        clearSelectedPos();
        callingElement.classList.add("selectedPos");
    }

    event.stopPropagation();
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

let updateWait;
let waitTime = 10;

function waitForUpdate() {
    let phpUpdateBoard = new XMLHttpRequest();
    phpUpdateBoard.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            // alert(this.responseText);
            let decoded = JSON.parse(this.responseText);

            lastUpdateId = parseInt(decoded.lastUpdateId);  //returned as an int?

            if (decoded.updateType === "pieceMove") {
                updatePieceMove(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewPositionId), parseInt(decoded.updateNewContainerId));
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
            }

            // alert("gotback");

            updateWait = window.setTimeout("waitForUpdate()", waitTime);
        }
    };
    phpUpdateBoard.open("GET", "updateBoardSpectator.php?gameId=" + gameId + "&myTeam=" + myTeam + "&lastUpdateId=" + lastUpdateId, true);
    phpUpdateBoard.send();
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

function updatePiecePurchase(placementId, unitId, updateTeam) {
    // alert("purchasing");
    let purchaseContainer = document.getElementById("purchased_container");

    let echoString = "";
    echoString += "<div class='" + unitNames[unitId] + " gamePiece " + updateTeam + "' data-placementId='" + placementId + "' data-placementBattleUsed='0' data-placementCurrentMoves='" + unitsMoves[unitId] + "' data-placementContainerId='999999' data-placementTeamId='" + notMyTeam + "' data-unitName='" + unitNames[unitId] + "' data-unitId='" + unitId + "' draggable='true' ondragstart='pieceDragstart(event, this)' onclick='pieceClick(event, this);' ondragenter='pieceDragenter(event, this);' ondragleave='pieceDragleave(event, this);'>";
    if (unitNames[unitId] === "transport" || unitNames[unitId] === "aircraftCarrier") {
        let classthing;
        if (unitNames[unitId] === "transport") {
            classthing = "transportContainer";
        } else {
            classthing = "aircraftCarrierContainer";
        }
        echoString += "<div class='" + classthing + " " + updateTeam + "' data-containerPopped='false' data-positionContainerId='" + placementId + "' data-positionType='" + classthing + "' data-positionId='118' ondragleave='containerDragleave(event, this);'  ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);'></div>";
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
    if (newContainerId != "999999") {
        theContainer = document.querySelector("[data-placementId='" + newContainerId + "']").childNodes[0];
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
                // document.getElementById("battle_button").disabled = false;
            } else {
                // document.getElementById("battle_button").disabled = true;
            }
            if (canUndo === "true") {
                // document.getElementById("undo_button").disabled = false;
            } else {
                // document.getElementById("undo_button").disabled = true;
            }
            if (canNextPhase === "true") {
                // document.getElementById("phase_button").disabled = false;
            } else {
                // document.getElementById("phase_button").disabled = true;
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
                        // document.getElementById("actionPopupButton").disabled = false;
                    } else {
                        document.getElementById("actionPopupButton").disabled = true;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "click to roll for defense bonus";
                    document.getElementById("actionPopupButton").onclick = function() { battleAttackCenter("defend"); };
                } else if (gameBattleSubSection === "defense_bonus" && gameBattleSection === "counter") {
                    if (myTeam !== gameCurrentTeam) {
                        document.getElementById("actionPopupButton").disabled = true;
                    } else {
                        // document.getElementById("actionPopupButton").disabled = false;
                    }
                    document.getElementById("actionPopupButton").innerHTML = "click to roll for defense bonus";
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
                    // document.getElementById("attackButton").disabled = false;
                } else {
                    document.getElementById("attackButton").disabled = true;
                }


                // document.getElementById("changeSectionButton").disabled = false;
            }

            // alert("changing section to something");
            if (gameBattleSection === "askRepeat" && myTeam === gameCurrentTeam) {
                // alert("myteam = current team enable");
                // document.getElementById("attackButton").disabled = false;
                // document.getElementById("changeSectionButton").disabled = false;
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
// function userFeedback(text){
//     document.getElementById("user_feedback").innerHTML = text;
// }

function rollDice(){
    let randomRoll = Math.floor(Math.random() * 6) + 1 ;
    let numRolls = Math.floor(Math.random() * 11) + 10  ;
    for (let i = 1; i < numRolls; i++) {
        (function (i) {
            setTimeout(function () {showDice(randomRoll)}, 100 * i);
        })(i);
    }
    showDice(gameBattleLastRoll);
}

function showDice(diceNum){
    // document.getElementById("dice_image").classList[1] = "dice" + diceNum;
    document.getElementById("dice_image").classList[0].style.backgroundImage = "url(resources/diceImages/die-" + diceNum + ".gif)";
}

waitForUpdate();