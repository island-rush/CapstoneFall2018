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
$gameBattleTurn = $_REQUEST['gameBattleTurn'];

$increment = 0;
if ($gameBattleSection == "selectPos") {
    $increment = 1;
}

$query = 'UPDATE games SET gameBattleSection = ?, gameBattleSubSection = ?, gameBattleLastRoll = ?, gameBattleLastMessage = ?, gameBattlePosSelected = ?, gameBattleTurn = gameBattleTurn + ?, gameTurn = gameTurn + ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("ssisiiii", $gameBattleSection, $gameBattleSubSection, $gameBattleLastRoll, $gameBattleLastMessage, $gameBattlePosSelected, $increment, $increment, $gameId);
$query->execute();


//if new section == askRepeat, check turn = 2, then remove battlePiece aircraft
if ($gameBattleSection == "askRepeat" && $gameBattleTurn >= 2) {
    $fighter = "fighter";
    $bomber = "bomber";
    $stealthBomber = "stealthBomber";
    $tanker = "tanker";
    $wasNotHit = 0;
    $query = 'SELECT * FROM battlePieces NATURAL JOIN (SELECT * FROM placements NATURAL JOIN units WHERE unitId = placementUnitId) WHERE (placementId = battlePieceId) AND (unitName = ? OR unitName = ? OR unitName = ? OR unitName = ?) AND (battlePieceWasHit = ?) AND (placementGameId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ssssii", $fighter, $bomber, $stealthBomber, $tanker, $wasNotHit, $gameId);
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
