<?php
session_start();
include("db.php");

///TODO: THIS FILE NOT YET USED

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$notJoined = 0;

$query = 'UPDATE games SET gameBlueJoined = ? WHERE gameId = ?';
if ($myTeam == "Red") {
    $query = 'UPDATE games SET gameRedJoined = ? WHERE gameId = ?';
}

$query = $db->prepare($query);
$joinedValue = 1;
$query->bind_param("ii", $joinedValue, $gameId);
$query->execute();



$db->close();
session_abort();
