<?php
include("db.php");
$battlePieceId = $_REQUEST['battlePieceId'];
$new_battlePieceState = $_REQUEST['new_battlePieceState'];

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
}


$db->close();