<?php
include("db.php");

$placementId = (int) $_REQUEST['placementId'];
$myTeam = $_REQUEST['myTeam'];
$gameId = $_REQUEST['gameId'];
$newPoints = $_REQUEST['newPoints'];

$query = 'DELETE FROM placements WHERE placementId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();

$newValue = 0;
$updateType = "pieceTrash";
$Blue = "Blue";
$Red = "Red";

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissi", $gameId, $newValue, $Red, $updateType, $placementId);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissi", $gameId, $newValue, $Blue, $updateType, $placementId);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $placementId);
$query->execute();

$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Blue, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Red, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
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
