<?php
include("db.php");

$gameId = $_REQUEST['gameId'];
$sentArray = json_decode($_REQUEST['sentArray']);
$attackTeam = $_REQUEST['attackTeam'];

//find all pieces within the sentArray and echo the html for the battle pieces? and update them to used in the database as well




for ($i = 0; $i < sizeof($sentArray); $i++) {
    //get info about placement from database
    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementId = ?) AND (placementUnitId = unitId)';
    $query = $db->prepare($query);
    $query->bind_param("i", $sentArray[$i]);
    $query->execute();
    $results = $query->get_result();
    $r= $results->fetch_assoc();
    $placementId = $r['placementId'];
    $unitId = $r['unitId'];
    $unitName = $r['unitName'];
    $wasHit = 0;
    $pieceState = 1;  // unused_attacker boxId

    //create the battle piece in the database
    $query2 = 'INSERT INTO battlePieces (battlePieceId, battleGameId, battlePieceState, battlePieceWasHit) VALUES(?, ?, ?, ?)';
    $query2 = $db->prepare($query2);
    $query2->bind_param("iiii", $placementId, $gameId, $pieceState, $wasHit);
    $query2->execute();

    //update the placement as 'used' in the database
    $used = 1;
    $query = 'UPDATE placements SET placementBattleUsed = ? WHERE (placementId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $used,$placementId);
    $query->execute();

    //echo the html for the battlepiece
    echo "<div class='".$unitName." gamePiece' data-battlePieceWasHit='".$wasHit."' data-unitId='".$unitId."' data-unitName='".$unitName."' data-battlePieceId='".$placementId."' onclick='battlePieceClick(event, this)'></div>";
}



$db->close();
