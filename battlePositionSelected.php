<?php
session_start();
include("db.php");

$positionSelected = $_REQUEST['positionSelected'];
$gameId = $_REQUEST['gameId'];
$defenseTeam = $_REQUEST['defenseTeam'];
$battleTerrain = $_REQUEST['battleTerrain'];
$myTeam = $_SESSION['myTeam'];

$notMyTeam = "Blue";
if ($myTeam == "Blue") {
    $notMyTeam = "Red";
}


if ($battleTerrain == "water") {
    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementPositionId = ?) AND (placementTeamId = ?) AND (unitTerrain != "land") AND (placementUnitId = unitId)';
} else {
    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementPositionId = ?) AND (placementTeamId = ?) AND (placementUnitId = unitId)';
}
$query = $db->prepare($query);
$query->bind_param("iis", $gameId, $positionSelected, $defenseTeam);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;

$htmlString = "";
$adjacentArray = [];

if ($num_results > 0) {
    for ($i=0; $i < $num_results; $i++) {
        //for each piece in the position....create a battle piece in the unused_defender state and echo the html for it
        $r= $results->fetch_assoc();
        $placementId = $r['placementId'];
        $unitId = $r['unitId'];
        $unitName = $r['unitName'];

        $wasHit = 0;
        $pieceState = 2;  // unused_defender boxId

        $query2 = 'INSERT INTO battlePieces (battlePieceId, battleGameId, battlePieceState, battlePieceWasHit) VALUES(?, ?, ?, ?)';
        $query2 = $db->prepare($query2);
        $query2->bind_param("iiii", $placementId, $gameId, $pieceState, $wasHit);
        $query2->execute();

        $htmlString = $htmlString."<div class='".$unitName." gamePiece ".$notMyTeam."' title='".$unitName."' data-battlePieceWasHit='".$wasHit."' data-unitId='".$unitId."' data-unitName='".$unitName."' data-battlePieceId='".$placementId."' onclick='battlePieceClick(event, this)'></div>";
    }
}


$n = sizeof($_SESSION['dist'][0]);
for ($j = 0; $j < $n; $j++) {
    if ($n != $positionSelected) {
        if ($_SESSION['dist'][$positionSelected][$j] <= 1) {
            array_push($adjacentArray, $j);
        }
    }
}


$arr = array('htmlString' => $htmlString, 'adjacentArray' => $adjacentArray);
echo json_encode($arr);


$newValue = 0;
$updateType = "positionSelected";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateBattlePositionSelectedPieces, updateNewPositionId) VALUES (?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisssi", $gameId, $newValue, $myTeam, $updateType, $htmlString, $positionSelected);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateBattlePositionSelectedPieces, updateNewPositionId) VALUES (?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisssi", $gameId, $newValue, $Spec, $updateType, $htmlString, $positionSelected);
$query->execute();


$db->close();
