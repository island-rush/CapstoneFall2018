<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];

// Get new point values from the POST
$gameActive = $_REQUEST['gameActive'];

$query = "UPDATE GAMES SET gameActive = ? WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("ii", $gameActive, $gameId);
$preparedQuery->execute();

$db->close();
