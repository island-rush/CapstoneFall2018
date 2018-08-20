<?php
include("db.php");

$placementId = $_REQUEST['placementId'];
$myTeam = $_REQUEST['myTeam'];
$gameId = $_REQUEST['gameId'];

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

$db->close();
