2<?php

include("db.php");

$gameId = $_REQUEST['gameId'];

$query = 'DELETE FROM battlePieces WHERE battleGameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();

//TODO: insert into the updates table

$db->close();
