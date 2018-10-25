<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

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

if ($points >= 10) {
    //insert a newsalert that is active for 1-2 length? that disables all enemy aircraft from moving
    $order = 0;
    $length = 2;
    $activated = 1;
    $zone = 200; //all zones everywhere
    $disable = "disable";
    $team = "Red";
    if ($myTeam == "Red") {
        $team = "Blue";
    }
    $allPieces = '{"Transport":0, "Submarine":0, "Destroyer":0, "AircraftCarrier":0, "ArmyCompany":0, "ArtilleryBattery":0, "TankPlatoon":0, "MarinePlatoon":0, "MarineConvoy":0, "AttackHelo":1, "SAM":0, "FighterSquadron":1, "BomberSquadron":1, "StealthBomberSquadron":1, "Tanker":1}';
    $query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsLength, newsActivated) VALUES(?,?,?,?,?,?,?,?)';
    $query = $db->prepare($query);
    $query->bind_param("iisssiii",$gameId, $order, $team, $allPieces, $disable, $zone, $length, $activated);
    $query->execute();

    //take away the hpoints
    $ten = 10;
    $query = 'UPDATE games SET gameRedHpoints = gameRedHpoints - ? WHERE gameId = ?';
    if ($myTeam == "Blue") {
        $query = 'UPDATE games SET gameBlueHpoints = gameBlueHpoints - ? WHERE gameId = ?';
    }
    $query = $db->prepare($query);
    $query->bind_param("ii", $ten, $gameId);
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
