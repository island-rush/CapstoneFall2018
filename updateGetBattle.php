<?php
session_start();  //needed to update session variables
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];


$query = "SELECT * FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r= $results->fetch_assoc();

//probably dont need this since this only called after an attack?
$gameBattleSection = $r['gameBattleSection'];
$gameBattleSubSection = $r['gameBattleSubSection'];
$gameBattleLastRoll = $r['gameBattleLastRoll'];
$gameBattleLastMessage = $r['gameBattleLastMessage'];
$gameBattleTurn = $r['gameBattleTurn'];

$arr = array('gameBattleSection' => $gameBattleSection,
    'gameBattleSubSection' => $gameBattleSubSection,
    'gameBattleLastRoll' => $gameBattleLastRoll,
    'gameBattleLastMessage' => $gameBattleLastMessage,
    'gameBattleTurn' => $gameBattleTurn);
echo json_encode($arr);

$db->close();

