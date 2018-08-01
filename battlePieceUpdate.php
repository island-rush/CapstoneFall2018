<?php
include("db.php");
$battlePieceId = $_REQUEST['battlePieceId'];
$new_battlePieceState = $_REQUEST['new_battlePieceState'];
$query = 'UPDATE battlePieces SET battlePieceState = ? WHERE (battlePieceId = ?)';
$query = $db->prepare($query);
$query->bind_param("ii", $new_battlePieceState, $battlePieceId);
$query->execute();
$db->close();