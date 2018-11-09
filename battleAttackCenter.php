<?php
session_start();
include("db.php");

$myTeam = $_SESSION['myTeam'];
$gameId = $_SESSION['gameId'];

$attackUnitId = $_REQUEST['attackUnitId'];
$defendUnitId = $_REQUEST['defendUnitId'];

$attackUnitName = $_REQUEST['attackUnitName'];
$defendUnitName = $_REQUEST['defendUnitName'];

$gameBattleSection = $_REQUEST['gameBattleSection'];
$gameBattleSubSection = $_REQUEST['gameBattleSubSection'];

$posType = $_REQUEST['posType'];

$boostedAttack = $_REQUEST['boostedAttack'];

$gameBattleLastMessage = "Test Game Battle Message";

//$lastRoll = rand(1, 6);
$lastRoll = 6;
//$lastRoll = 1;

if ($gameBattleSubSection == "choosing_pieces") {
    //regular attack
    if (($lastRoll + $boostedAttack) >= $_SESSION['attack'][$attackUnitId][$defendUnitId] && $_SESSION['attack'][$attackUnitId][$defendUnitId] != 0) {
        $wasHit = 1;
        $gameBattleLastMessage = $attackUnitName." hit ".$defendUnitName;
    } else {
        $wasHit = 0;
        $gameBattleLastMessage = $attackUnitName." did not hit ".$defendUnitName;
        if ($_SESSION['attack'][$attackUnitId][$defendUnitId] == 0) {
            $gameBattleLastMessage = $attackUnitName." can not hit ".$defendUnitName;
        }
    }
} else {
    //defense bonus
    if (($lastRoll >= $_SESSION['attack'][$attackUnitId][$defendUnitId] && $_SESSION['attack'][$attackUnitId][$defendUnitId] != 0) || ($_SESSION['attack'][$attackUnitId][$defendUnitId] == 0 && $lastRoll == 6)) {
        //if the attack value needed was 0, washit = 2, deal with saving both pieces
        if ($_SESSION['attack'][$attackUnitId][$defendUnitId] == 0 && $lastRoll == 6) {
            $wasHit = 2;
            //db for changing the washit value of the other gamebattlepiece
            $gameBattleLastMessage = $attackUnitName." defense bonus survived ".$defendUnitName;
            $notHit = 0;
            $five = 5;
            $six = 6;
            $query = 'UPDATE battlePieces SET battlePieceWasHit = ? WHERE (battleGameId = ?) AND (battlePieceState = ? OR battlePieceState = ?)';
            $query = $db->prepare($query);
            $query->bind_param("iiii", $notHit, $gameId, $five, $six);
            $query->execute();
        } else {
            $wasHit = 1;
            $gameBattleLastMessage = $attackUnitName." hit ".$defendUnitName;
        }
    } else {
        $wasHit = 0;
        $gameBattleLastMessage = $attackUnitName." did not hit ".$defendUnitName;
    }
}

if ($wasHit == 1 && $gameBattleSection == "attack" && $gameBattleSubSection == "choosing_pieces") {
    if ($attackUnitName == "Destroyer" && $posType == "land"){
        $nextThing = "continue_choosing";
    } else {
        $nextThing = "defense_bonus";
    }
} else {
    $nextThing = "continue_choosing";
}


//$gameBattleLastMessage = "Test Game Battle Message";


$arr = array('lastRoll' => $lastRoll, 'wasHit' => $wasHit, 'new_gameBattleSubSection' => $nextThing, 'gameBattleLastMessage' => $gameBattleLastMessage);
echo json_encode($arr);


$query = 'UPDATE games SET gameBattleSubSection = ?, gameBattleLastRoll = ?, gameBattleLastMessage = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("sisi",  $nextThing, $lastRoll, $gameBattleLastMessage, $gameId);
$query->execute();


$newValue = 0;
$updateType = "battleAttacked";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateNewMoves) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissi", $gameId, $newValue, $myTeam, $updateType, $wasHit);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateNewMoves) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $wasHit);
$query->execute();


if ($wasHit == 1) {
    $pieceId = $_REQUEST['pieceId'];
    $hit = 1;
    $query = 'UPDATE battlePieces SET battlePieceWasHit = ? WHERE (battlePieceId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $hit, $pieceId);
    $query->execute();
}




$db->close();