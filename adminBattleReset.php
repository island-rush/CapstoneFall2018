<?php
session_start();
include("db.php");

//$instructor = $_REQUEST['instructor'];
//$section = $_REQUEST['section'];
$gameId = (int) $_SESSION['gameId'];

$newGameBattleSection = "none";

$query = 'UPDATE games SET gameBattleSection = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("si", $newGameBattleSection, $gameId);
$query->execute();

