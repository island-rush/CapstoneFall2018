<?php

include("db.php");

$gameId = $_REQUEST['gameId'];

$query = 'DELETE FROM battlePieces WHERE battleGameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();

//TODO: ajax call to delete the battlePieces in general? (html duplicate bug source?)

$db->close();
