<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$query = 'UPDATE games SET gameBlueJoined = ? WHERE gameId = ?';
if ($myTeam == "Red") {
    $query = 'UPDATE games SET gameRedJoined = ? WHERE gameId = ?';
}

$notJoined = 0;
$query = $db->prepare($query);
$query->bind_param("ii", $NotJoined, $gameId);
$query->execute();

$db->close();


session_unset();  //not sure capabilities of this yet (or how to fully delete the session stuff)

if (isset($_REQUEST['reason'])) {
    header("location:login.php?err=4");
} else {
    header("location:login.php");
}

exit();
