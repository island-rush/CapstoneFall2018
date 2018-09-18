<?php
include("db.php");

$placementId = $_REQUEST['placementId'];
$myTeam = $_REQUEST['myTeam'];
$gameId = $_REQUEST['gameId'];
$newPoints = $_REQUEST['newPoints'];

$query = 'DELETE FROM placements WHERE placementId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();

$newValue = 0;
$updateType = "pieceTrash";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissi", $gameId, $newValue, $myTeam, $updateType, $placementId);
$query->execute();

$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $myTeam, $updateType);
$query->execute();


$query = "";
if ($myTeam == "Red") {
    $query = 'UPDATE games SET gameRedRpoints = ? WHERE (gameId = ?)';
} else {
    $query = 'UPDATE games SET gameBlueRpoints = ? WHERE (gameId = ?)';
}
$query = $db->prepare($query);
$query->bind_param("ii", $newPoints, $gameId);
$query->execute();


$db->close();
