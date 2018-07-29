<?php
include("db.php");

$unitId = $_REQUEST['unitId'];
$unitName = $_REQUEST['unitName'];
$unitMoves = $_REQUEST['unitMoves'];
$unitTerrain = $_REQUEST['unitTerrain'];

$gameId = $_REQUEST['gameId'];
$placementTeamId = $_REQUEST['placementTeamId'];
$placementContainerId = 999999;
$placementPositionId = 118;

$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId) VALUES(?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiii", $gameId, $unitId, $placementTeamId, $placementContainerId, $unitMoves, $placementPositionId);
$query->execute();

$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r= $results->fetch_assoc();
$new_placementId = $r['LAST_INSERT_ID()'];


//opening for overall piece
echo "<div class='".$unitName." gamePiece' data-placementId='".$new_placementId."' data-placementCurrentMoves='".$unitMoves."' data-placementContainerId='".$placementContainerId."' data-placementTeamId='".$placementTeamId."' data-unitTerrain='".$unitTerrain."' data-unitName='".$unitName."' data-unitId='".$unitId."' ";

//functions for all pieces (container/non-container)
echo "draggable='true' ondragstart='pieceDragstart(event, this)' ";

if ($unitName == "transport" || $unitName == "aircraftCarrier" || $unitName == "lav") {
    //functions for containers (click to open / show moves)
    echo "";
} else {
    //functions for non-containers (click to open)
    echo "";
}

echo ">";  // end of opening for overall piece

//build containers for container pieces + pieces inside of them
if ($unitName == "transport" || $unitName == "aircraftCarrier" || $unitName == "lav") {
    if ($unitName == "transport") {
        $classthing = "transportContainer";
    } elseif ($unitName == "aircraftCarrier") {
        $classthing = "aircraftCarrierContainer";
    } else {
        $classthing = "lavContainer";
    }
    //build container
    echo "<div class='".$classthing."' data-positionContainerId='".$new_placementId."' data-positionType='".$classthing."' data-positionId='".$placementPositionId."'></div>";
}

echo "</div>";  // end the overall piece


$db->close();
