<?php
include("db.php");

$unitId = $_REQUEST['unitId'];
$unitName = $_REQUEST['unitName'];
$unitMoves = $_REQUEST['unitMoves'];
$unitTerrain = $_REQUEST['unitTerrain'];

$newPoints = $_REQUEST['newPoints'];
$costOfPiece = $_REQUEST['costOfPiece'];


$gameId = $_REQUEST['gameId'];
$myTeam = $_REQUEST['myTeam'];
$placementTeamId = $_REQUEST['placementTeamId'];
$placementContainerId = 999999;
$placementPositionId = 118;
$placementBattleUsed = 0;


$query = "";
if ($myTeam == "Red") {
    $query = 'UPDATE games SET gameRedRpoints = ? WHERE (gameId = ?)';
} else {
    $query = 'UPDATE games SET gameBlueRpoints = ? WHERE (gameId = ?)';
}
$query = $db->prepare($query);
$query->bind_param("ii", $newPoints, $gameId);
$query->execute();



$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $unitId, $placementTeamId, $placementContainerId, $unitMoves, $placementPositionId, $placementBattleUsed);
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

echo "<div class='".$unitName." gamePiece ".$placementTeamId."' title='".$unitName."&#013;Moves: ".$unitMoves."' data-unitCost='".$costOfPiece."' data-placementId='".$new_placementId."' data-placementBattleUsed='".$placementBattleUsed."' data-placementCurrentMoves='".$unitMoves."' data-placementContainerId='".$placementContainerId."' data-placementTeamId='".$placementTeamId."' data-unitTerrain='".$unitTerrain."' data-unitName='".$unitName."' data-unitId='".$unitId."' draggable='true' ondragstart='pieceDragstart(event, this)' onclick='pieceClick(event, this);' ondragenter='pieceDragenter(event, this);' ondragleave='pieceDragleave(event, this);'>";

if ($unitName == "transport" || $unitName == "aircraftCarrier") {
    if ($unitName == "transport") {
        $classthing = "transportContainer";
    } else {
        $classthing = "aircraftCarrierContainer";
    }
    echo "<div class='".$classthing."' data-containerPopped='false' data-positionContainerId='".$new_placementId."' data-positionType='".$classthing."' ondragenter='containerDragenter(event, this);' ondragleave='containerDragleave(event, this);'  ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);'></div>";
}

echo "</div>";  // end the overall piece


$db->close();
