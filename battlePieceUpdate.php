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
    //delete the real piece from database
    //delete the container children from database if applicable?
}


$db->close();