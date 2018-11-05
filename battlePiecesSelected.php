<?php
include("db.php");
session_start();
$gameId = $_REQUEST['gameId'];
$sentArray = json_decode($_REQUEST['sentArray']);
$attackTeam = $_REQUEST['attackTeam'];

$myTeam = $_SESSION['myTeam'];

//find all pieces within the sentArray and echo the html for the battle pieces? and update them to used in the database as well

$piecesSelectedHTMLstring = "";

for ($i = 0; $i < sizeof($sentArray); $i++) {
    //get info about placement from database
    $unused = 0;
    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementId = ?) AND (placementUnitId = unitId) AND (placementBattleUsed = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $sentArray[$i], $unused);
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
    $piecesSelectedHTMLstring = $piecesSelectedHTMLstring."<div class='".$unitName." gamePiece ".$myTeam."' title='".$unitName."' data-battlePieceWasHit='".$wasHit."' data-unitId='".$unitId."' data-unitName='".$unitName."' data-battlePieceId='".$placementId."' onclick='battlePieceClick(event, this)'></div>";
}

echo $piecesSelectedHTMLstring;

$newValue = 0;
$updateType = "piecesSelected";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateBattlePiecesSelected) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisss", $gameId, $newValue, $myTeam, $updateType, $piecesSelectedHTMLstring);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateBattlePiecesSelected) VALUES (?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisss", $gameId, $newValue, $Spec, $updateType, $piecesSelectedHTMLstring);
$query->execute();


//$query = 'UPDATE games SET gameBattleTurn = ? WHERE gameId = ?';
//$query = $db->prepare($query);
//$query->bind_param("ii", $newValue,  $gameId);
//$query->execute();

$db->close();
