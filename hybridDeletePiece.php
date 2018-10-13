<?php
session_start();
include("db.php");

$myTeam = $_SESSION['myTeam'];
$gameId = $_SESSION['gameId'];

$placementId = $_REQUEST['placementId'];


//if they have enough points
$query = 'SELECT * FROM games WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i",$gameId);
$query->execute();
$results = $query->get_result();
$r= $results->fetch_assoc();

$points = (int) $r['gameRedHpoints'];
if ($myTeam == "Blue") {
    $points = (int) $r['gameBlueHpoints'];
}

if ($points >= 6) {

    $six = 6;
    $query = 'UPDATE games SET gameRedHpoints = gameRedHpoints - ? WHERE gameId = ?';
    if ($myTeam == "Blue") {
        $query = 'UPDATE games SET gameBlueHpoints = gameBlueHpoints - ? WHERE gameId = ?';
    }
    $query = $db->prepare($query);
    $query->bind_param("ii", $six, $gameId);
    $query->execute();


    $query = 'DELETE FROM placements WHERE placementId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $placementId);
    $query->execute();

    $query = 'DELETE FROM placements WHERE placementContainerId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $placementId);
    $query->execute();

    $newValue = 0;
    $updateType = "pieceTrash";
    $Red = "Red";
    $Blue = "Blue";

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
}


//might as well update the clients? (could put this inside the if statement)
$Blue = "Blue";
$Red = "Red";
$Spec = "Spec";
$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Blue, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Red, $updateType);
$query->execute();


$db->close();
