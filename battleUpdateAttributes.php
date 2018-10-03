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

$increment = 0;
if ($gameBattleSection == "selectPos") {
    $increment = 1;
}

$query = 'UPDATE games SET gameBattleSection = ?, gameBattleSubSection = ?, gameBattleLastRoll = ?, gameBattleLastMessage = ?, gameBattlePosSelected = ?, gameTurn = gameTurn + ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("ssisiii", $gameBattleSection, $gameBattleSubSection, $gameBattleLastRoll, $gameBattleLastMessage, $gameBattlePosSelected, $increment, $gameId);
$query->execute();


$newValue = 0;
$updateType = "battleSectionChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $myTeam, $updateType);
$query->execute();


$db->close();
