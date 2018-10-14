<?php
session_start();
include("db.php");

$myTeam = $_SESSION['myTeam'];
$gameId = $_SESSION['gameId'];

$placementId = (int) $_REQUEST['placementId'];

$query = 'DELETE FROM placements WHERE placementId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();

//delete stuff inside if it was a container
$query = 'DELETE FROM placements WHERE placementContainerId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();


//update the other client and spectators
$newValue = 0;
$updateType = "pieceTrash";

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissi", $gameId, $newValue, $myTeam, $updateType, $placementId);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $placementId);
$query->execute();

//prevent future undo
$query = 'DELETE FROM movements WHERE movementGameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();



$db->close();
