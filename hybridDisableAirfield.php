<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$positionDisabled = (int) $_REQUEST['positionId'] + 1000;


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

if ($points >= 3) {
    //insert a newsalert that is active for 1-2 length? that disables all enemy aircraft from moving
    $order = 0;
    $length = 2;
    $activated = 1;
    $zone = $positionDisabled;
    $disable = "disable";
    $team = "Red";
    if ($myTeam == "Red") {
        $team = "Blue";
    }
    $allPieces = '{"transport":0, "submarine":0, "destroyer":0, "aircraftCarrier":0, "soldier":0, "artillery":0, "tank":0, "marine":0, "lav":0, "attackHeli":1, "sam":0, "fighter":1, "bomber":1, "stealthBomber":1, "tanker":1}';
    $query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsLength, newsActivated) VALUES(?,?,?,?,?,?,?,?)';
    $query = $db->prepare($query);
    $query->bind_param("iisssiii",$gameId, $order, $team, $allPieces, $disable, $zone, $length, $activated);
    $query->execute();

    //take away the hpoints
    $three = 3;
    $query = 'UPDATE games SET gameRedHpoints = gameRedHpoints - ? WHERE gameId = ?';
    if ($myTeam == "Blue") {
        $query = 'UPDATE games SET gameBlueHpoints = gameBlueHpoints - ? WHERE gameId = ?';
    }
    $query = $db->prepare($query);
    $query->bind_param("ii", $three, $gameId);
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
