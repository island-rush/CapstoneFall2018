<?php
include("db.php");

$pieceId = $_REQUEST['pieceId'];
$wasHit = 1;

$query = 'UPDATE battlePieces SET battlePieceWasHit = ? WHERE (battlePieceId = ?)';
$query = $db->prepare($query);
$query->bind_param("ii", $wasHit, $pieceId);
$query->execute();

$db->close();
