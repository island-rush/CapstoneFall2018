<?php
include("db.php");
$battlePieceId = $_REQUEST['battlePieceId'];
$new_battlePieceState = $_REQUEST['new_battlePieceState'];
$myTeam = $_REQUEST['myTeam'];
$gameId = $_REQUEST['gameId'];

if ($new_battlePieceState != 9) {
    $query = 'UPDATE battlePieces SET battlePieceState = ? WHERE (battlePieceId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $new_battlePieceState, $battlePieceId);
    $query->execute();
} else {
    //delete the piece from database
    $query = 'DELETE FROM battlePieces WHERE battlePieceId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $battlePieceId);
    $query->execute();

    //delete the real piece from database
    $query = 'DELETE FROM placements WHERE placementId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $battlePieceId);
    $query->execute();

    //delete the stuff within the container
    $query = 'DELETE FROM placements WHERE placementContainerId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $battlePieceId);
    $query->execute();

    //Tell other client about deletion
    $newValue = 0;
    $updateType = "pieceDelete";
    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissi", $gameId, $newValue, $myTeam, $updateType, $battlePieceId);
    $query->execute();
}


$db->close();