<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$gameBattleSection = $_REQUEST['gameBattleSection'];
$gameBattleSubSection = $_REQUEST['gameBattleSubSection'];
$gameBattleLastRoll = $_REQUEST['gameBattleLastRoll'];
$gameBattleLastMessage = $_REQUEST['gameBattleLastMessage'];
$gameBattlePosSelected = $_REQUEST['gameBattlePosSelected'];
$gameBattleTurn = (int) $_REQUEST['gameBattleTurn'];
$posType = $_REQUEST['posType'];

if ($gameBattleSection == "selectPos") {
    //prevent future undo
    $query = 'DELETE FROM movements WHERE movementGameId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $gameId);
    $query->execute();
}

$query = 'UPDATE games SET gameBattleSection = ?, gameBattleSubSection = ?, gameBattleLastRoll = ?, gameBattleLastMessage = ?, gameBattlePosSelected = ?, gameBattleTurn = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("ssisiii", $gameBattleSection, $gameBattleSubSection, $gameBattleLastRoll, $gameBattleLastMessage, $gameBattlePosSelected, $gameBattleTurn, $gameId);
$query->execute();


//this is the code to get rid of aircraft after 2 turns / destoyers after 1 turn of bombardment

//if new section == askRepeat, check turn = 2, then remove battlePiece aircraft
if ($gameBattleSection == "askRepeat" && $gameBattleTurn > 1) {
    $fighter = "FighterSquadron";
    $bomber = "BomberSquadron";
    $stealthBomber = "StealthBomberSquadron";
    $tanker = "Tanker";
    $wasNotHit = 0;
    $query = 'SELECT * FROM battlePieces NATURAL JOIN (SELECT * FROM placements NATURAL JOIN units WHERE unitId = placementUnitId) a WHERE (placementId = battlePieceId) AND (placementTeamId = ?) AND (unitName = ? OR unitName = ? OR unitName = ? OR unitName = ?) AND (battlePieceWasHit = ?) AND (placementGameId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("sssssii", $myTeam, $fighter, $bomber, $stealthBomber, $tanker, $wasNotHit, $gameId);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;
    if ($num_results > 0) {
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $battlePieceId = $r['battlePieceId'];

            $query = 'DELETE FROM battlePieces WHERE battlePieceId = ?';
            $query = $db->prepare($query);
            $query->bind_param("i", $battlePieceId);
            $query->execute();

            //update to all players and spec
            $Red = "Red";
            $Blue = "Blue";
            $Spec = "Spec";
            $newValue = 0;
            $updateType = "battlePieceRemove";

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Red, $updateType, $battlePieceId);
            $query->execute();

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Blue, $updateType, $battlePieceId);
            $query->execute();

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $battlePieceId);
            $query->execute();
        }
    }
}


if ($gameBattleSection == "counter" && $posType == "land") {
    $destroyer = "Destroyer";
    $wasNotHit = 0;
    $query = 'SELECT * FROM battlePieces NATURAL JOIN (SELECT * FROM placements NATURAL JOIN units WHERE unitId = placementUnitId) a WHERE (placementId = battlePieceId) AND (placementTeamId = ?) AND (unitName = ?) AND (battlePieceWasHit = ?) AND (placementGameId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ssii", $myTeam, $destroyer, $wasNotHit, $gameId);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;
    if ($num_results > 0) {
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $battlePieceId = $r['battlePieceId'];

            $query = 'DELETE FROM battlePieces WHERE battlePieceId = ?';
            $query = $db->prepare($query);
            $query->bind_param("i", $battlePieceId);
            $query->execute();

            //update to all players and spec
            $Red = "Red";
            $Blue = "Blue";
            $Spec = "Spec";
            $newValue = 0;
            $updateType = "battlePieceRemove";

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Red, $updateType, $battlePieceId);
            $query->execute();

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Blue, $updateType, $battlePieceId);
            $query->execute();

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $battlePieceId);
            $query->execute();
        }
    }
}



$newValue = 0;
$updateType = "battleSectionChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $myTeam, $updateType);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
$query->execute();


$db->close();
