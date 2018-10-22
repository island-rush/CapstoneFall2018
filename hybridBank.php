<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$lastNumber = (int) $_REQUEST['lastNumber'];

$zone = $lastNumber + 100;



$query = 'SELECT * FROM games WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i",$gameId);
$query->execute();
$results = $query->get_result();
$r= $results->fetch_assoc();

$points = (int) $r['gameRedHpoints'];
if ($myTeam == "Blue") {
    $points = (int) $r['gameBlueHpoints'];
}

if ($points >= 4) {
    //TODO: also check to see if a newsalert isnt already active for this island

    //insert a newsalert that is active for 1-2 length? that disables all enemy aircraft from moving
    $order = 0;
    $length = 5;
    $activated = 1;
    $bank = "bankAdd";
    $team = "Blue";
    if ($myTeam == "Red") {
        $team = "Red";
    }
    $query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsEffect, newsZone, newsLength, newsActivated) VALUES(?,?,?,?,?,?,?)';
    $query = $db->prepare($query);
    $query->bind_param("iissiii",$gameId, $order, $team, $bank, $zone, $length, $activated);
    $query->execute();


    //take away the hpoints
    $four = 4;
    $query = 'UPDATE games SET gameRedHpoints = gameRedHpoints - ? WHERE gameId = ?';
    if ($myTeam == "Blue") {
        $query = 'UPDATE games SET gameBlueHpoints = gameBlueHpoints - ? WHERE gameId = ?';
    }
    $query = $db->prepare($query);
    $query->bind_param("ii", $four, $gameId);
    $query->execute();
}


//might as well update the clients? (could put this inside the if statement)
$Blue = "Blue";
$Red = "Red";
$Spec = "Spec";
$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Blue, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Red, $updateType);
$query->execute();


$db->close();
