<?php
session_start();
include("db.php");

$myTeam = $_SESSION['myTeam'];
$gameId = $_SESSION['gameId'];

$attackUnitId = $_REQUEST['attackUnitId'];
$defendUnitIt = $_REQUEST['defendUnitId'];

$gameBattleSection = $_REQUEST['gameBattleSection'];
$gameBattleSubSection = $_REQUEST['gameBattleSubSection'];

$lastRoll = rand(1, 6);

if ($gameBattleSubSection == "choosing_pieces") {
    //regular attack
    if ($lastRoll >= $_SESSION['attack'][$attackUnitId][$defendUnitIt] && $_SESSION['attack'][$attackUnitId][$defendUnitIt] != 0) {
        $wasHit = 1;
    } else {
        $wasHit = 0;
    }
} else {
    //defense bonus
    if (($lastRoll >= $_SESSION['attack'][$attackUnitId][$defendUnitIt] && $_SESSION['attack'][$attackUnitId][$defendUnitIt] != 0) || ($_SESSION['attack'][$attackUnitId][$defendUnitIt] == 0 && $lastRoll == 6)) {
        $wasHit = 1;
    } else {
        $wasHit = 0;
    }
}

if ($wasHit == 1 && $gameBattleSection == "attack" && $gameBattleSubSection == "choosing_pieces") {
    $nextThing = "defense_bonus";
} else {
    $nextThing = "continue_choosing";
}

$arr = array('lastRoll' => $lastRoll, 'wasHit' => $wasHit, 'new_gameBattleSubSection' => $nextThing);
echo json_encode($arr);


$query = 'UPDATE games SET gameBattleSubSection = ?, gameBattleLastRoll = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("sii",  $nextThing, $lastRoll, $gameId);
$query->execute();


$newValue = 0;
$updateType = "battleAttacked";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $myTeam, $updateType);
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