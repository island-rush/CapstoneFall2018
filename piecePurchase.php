<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$unitId = (int) $_REQUEST['unitId'];

$query = 'SELECT * FROM units WHERE unitId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $unitId);
$query->execute();
$results = $query->get_result();
$unitInfo = $results->fetch_assoc();

$unitCost = (int) $unitInfo['unitCost'];
$unitMoves = $unitInfo['unitMoves'];
$unitName = $unitInfo['unitName'];
$unitTerrain = $unitInfo['unitTerrain'];


$placementContainerId = 999999;
$placementPositionId = 118;
$placementBattleUsed = 0;


$query = 'SELECT * FROM games WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
$results = $query->get_result();
//TODO: should do error checking if game doesn't exist? (always better to check for this? (but don't know how to handle))
$r= $results->fetch_assoc();
$currentDBpoints = (int) $r['gameRedRpoints'];
if ($myTeam == "Blue") {
    $currentDBpoints = (int) $r['gameBlueRpoints'];
}

if ($currentDBpoints >= $unitCost) {

    $thisTeamNewPoints = $currentDBpoints - $unitCost;

    $query = 'UPDATE games SET gameBlueRpoints = ? WHERE (gameId = ?)';
    if ($myTeam == "Red") {
        $query = 'UPDATE games SET gameRedRpoints = ? WHERE (gameId = ?)';
    }
    $query = $db->prepare($query);
    $query->bind_param("ii", $thisTeamNewPoints, $gameId);
    $query->execute();


    $query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iisiiii", $gameId, $unitId, $myTeam, $placementContainerId, $unitMoves, $placementPositionId, $placementBattleUsed);
    $query->execute();

    $query = 'SELECT LAST_INSERT_ID()';
    $query = $db->prepare($query);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;
    $r= $results->fetch_assoc();
    $new_placementId = $r['LAST_INSERT_ID()'];

    $newValue = 0;
    $updateType = "piecePurchase";
    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewUnitId) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissii", $gameId, $newValue, $myTeam, $updateType, $new_placementId, $unitId);
    $query->execute();

    $Spec = "Spec";
    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewUnitId) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissii", $gameId, $newValue, $Spec, $updateType, $new_placementId, $unitId);
    $query->execute();

    $newValue = 0;
    $updateType = "phaseChange";
    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iiss", $gameId, $newValue, $myTeam, $updateType);
    $query->execute();

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
    $query->execute();

    echo "<div class='".$unitName." gamePiece ".$myTeam."' title='".$unitName."&#013;Moves: ".$unitMoves."' data-unitCost='".$unitCost."' data-placementId='".$new_placementId."' data-placementBattleUsed='".$placementBattleUsed."' data-placementCurrentMoves='".$unitMoves."' data-placementContainerId='".$placementContainerId."' data-placementTeamId='".$myTeam."' data-unitTerrain='".$unitTerrain."' data-unitName='".$unitName."' data-unitId='".$unitId."' draggable='true' ondragstart='pieceDragstart(event, this)' onclick='pieceClick(event, this);' ondragenter='pieceDragenter(event, this);' ondragleave='pieceDragleave(event, this);'>";

    if ($unitName == "Transport" || $unitName == "AircraftCarrier") {
        if ($unitName == "Transport") {
            $classthing = "transportContainer";
        } else {
            $classthing = "aircraftCarrierContainer";
        }
        echo "<div class='".$classthing."' data-containerPopped='false' data-positionContainerId='".$new_placementId."' data-positionType='".$classthing."' ondragenter='containerDragenter(event, this);' ondragleave='containerDragleave(event, this);'  ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);'></div>";
    }

    echo "</div>";  // end the overall piece


} else {
    //didn't have enough points to purchase this
    //assume don't get here with client side checks?
    echo 'INSUFFICIENT_POINTS';
}






$db->close();
