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

//TODO: don't logout when activating (has no effect, but less db interaction)
$red = 'Red';
$blue = 'Blue';
$newValue = 0;
$updateType = "logout";

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $red, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $blue, $updateType);
$query->execute();


$db->close();
