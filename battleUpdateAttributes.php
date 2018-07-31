<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];

$gameBattleSection = $_REQUEST['gameBattleSection'];
$gameBattleSubSection = $_REQUEST['gameBattleSubSection'];
$gameBattleLastRoll = $_REQUEST['gameBattleLastRoll'];
$gameBattleLastMessage = $_REQUEST['gameBattleLastMessage'];
$gameBattlePosSelected = $_REQUEST['gameBattlePosSelected'];

$query = 'UPDATE games SET gameBattleSection = ?, gameBattleSubSection = ?, gameBattleLastRoll = ?, gameBattleLastMessage = ?, gameBattlePosSelected = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("ssisii", $gameBattleSection, $gameBattleSubSection, $gameBattleLastRoll, $gameBattleLastMessage, $gameBattlePosSelected, $gameId);
$query->execute();

$_SESSION['gameBattleSection'] = $gameBattleSection;
$_SESSION['gameBattleSubSection'] = $gameBattleSubSection;
$_SESSION['gameBattleLastRoll'] = $gameBattleLastRoll;
$_SESSION['gameBattleLastMessage'] = $gameBattleLastMessage;
$_SESSION['gameBattlePosSelected'] = $gameBattlePosSelected;


$db->close();
