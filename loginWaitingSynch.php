<?php
include("db.php");

$gameId = $_REQUEST['gameId'];

$query = 'SELECT * FROM games WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
$results = $query->get_result();
$r= $results->fetch_assoc();

if ($r['gameBlueJoined'] == 1 && $r['gameRedJoined'] == 1) {
    echo "start_game";
} else {
    echo "keep_waiting";
}

$results->free();
$db->close();
