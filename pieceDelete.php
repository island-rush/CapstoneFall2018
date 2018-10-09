<?php
session_start();
include("db.php");

$myTeam = $_SESSION['myTeam'];
$gameId = $_SESSION['gameId'];

$placementId = $_REQUEST['placementId'];

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


//this needed to stop further undo after any piece is deleted
$one = 1;
$query = 'UPDATE games SET gameTurn = gameTurn + ? WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("ii", $gameId, $one);
$query->execute();



$db->close();
