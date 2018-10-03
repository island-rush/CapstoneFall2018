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

$query = 'UPDATE games SET gameBattleSection = ?, gameBattleSubSection = ?, gameBattleLastRoll = ?, gameBattleLastMessage = ?, gameBattlePosSelected = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("ssisii", $gameBattleSection, $gameBattleSubSection, $gameBattleLastRoll, $gameBattleLastMessage, $gameBattlePosSelected, $gameId);
$query->execute();

//if newbattlesection == selectpos
//increment games


$newValue = 0;
$updateType = "battleSectionChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $myTeam, $updateType);
$query->execute();


$db->close();
