<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];

// Get new point values from the POST
$gameActive = $_REQUEST['gameActive'];

$zero = 0;

$query = "UPDATE GAMES SET gameActive = ?, gameRedJoined = ?, gameBlueJoined = ?  WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iiii", $gameActive, $zero, $zero, $gameId);
$preparedQuery->execute();

$db->close();
