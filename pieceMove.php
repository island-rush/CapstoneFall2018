<?php
include("db.php");

$gameId = $_REQUEST['gameId'];
$gameTurn = $_REQUEST['gameTurn'];
$gamePhase = $_REQUEST['gamePhase'];

$placementId = $_REQUEST['placementId'];
$unitName = $_REQUEST['unitName'];

$new_positionId = $_REQUEST['new_positionId'];
$old_positionId = $_REQUEST['old_positionId'];

$movementCost = $_REQUEST['movementCost'];
$new_placementCurrentMoves = $_REQUEST['new_placementCurrentMoves'];

$old_placementContainerId = $_REQUEST['old_placementContainerId'];
$new_placementContainerId = $_REQUEST['new_placementContainerId'];


if ($unitName == "transport" || $unitName == "aircraftCarrier" || $unitName == "lav") {
    $query = 'UPDATE placements SET placementPositionId = ? WHERE (placementContainerId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $new_positionId, $placementId);
    $query->execute();
}


$query = 'UPDATE placements SET placementPositionId = ?, placementCurrentMoves = ?, placementContainerId = ? WHERE (placementId = ?)';
$query = $db->prepare($query);
$query->bind_param("iiii", $new_positionId, $new_placementCurrentMoves, $new_placementContainerId,  $placementId);
$query->execute();


$query = 'INSERT INTO movements (movementGameId, movementTurn, movementPhase, movementFromPosition, movementFromContainer, movementNowPlacement, movementCost) VALUES (?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiiiiii", $gameId, $gameTurn, $gamePhase, $old_positionId, $old_placementContainerId, $placementId, $movementCost);
$query->execute();


$db->close();
