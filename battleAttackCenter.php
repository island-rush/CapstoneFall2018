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

$gameBattleLastMessage = "Test Game Battle Message";

$lastRoll = rand(1, 6);

if ($gameBattleSubSection == "choosing_pieces") {
    //regular attack
    if ($lastRoll >= $_SESSION['attack'][$attackUnitId][$defendUnitId] && $_SESSION['attack'][$attackUnitId][$defendUnitId] != 0) {
        $wasHit = 1;
        $gameBattleLastMessage = $attackUnitName." hit ".$defendUnitName;
    } else {
        $wasHit = 0;
        $gameBattleLastMessage = $attackUnitName." did not hit ".$defendUnitName;
    }
} else {
    //defense bonus
    if (($lastRoll >= $_SESSION['attack'][$defendUnitId][$attackUnitId] && $_SESSION['attack'][$defendUnitId][$attackUnitId] != 0) || ($_SESSION['attack'][$defendUnitId][$attackUnitId] == 0 && $lastRoll == 6)) {
        $wasHit = 1;
        $gameBattleLastMessage = $defendUnitName." hit ".$attackUnitName;
    } else {
        $wasHit = 0;
        $gameBattleLastMessage = $defendUnitName." did not hit ".$attackUnitName;
    }
}

if ($wasHit == 1 && $gameBattleSection == "attack" && $gameBattleSubSection == "choosing_pieces") {
    $nextThing = "defense_bonus";
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
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $myTeam, $updateType);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
$query->execute();


if ($wasHit == 1) {
    $pieceId = $_REQUEST['pieceId'];
    $hit = 1;
    //TODO: not for this gameId? (but every id is different so probably works)
    $query = 'UPDATE battlePieces SET battlePieceWasHit = ? WHERE (battlePieceId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $hit, $pieceId);
    $query->execute();
}


$db->close();