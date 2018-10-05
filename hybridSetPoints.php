<?php

include("db.php");
$gameId = $_SESSION['gameId'];
// Get new point values from the POST
$newRedRpoints = $_REQUEST['newRedRpoints'];
$newBlueRpoints = $_REQUEST['newBlueRpoints'];
$newRedHpoints = $_REQUEST['newRedHpoints'];
$newBlueHpoints = $_REQUEST['newBlueHpoints'];


$query = "UPDATE GAMES SET gameRedRpoints = ?, gameRedHpoints = ?, gameBlueRpoints = ?, gameBlueHpoints = ? WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iiiii", $newRedRpoints, $newRedHpoints, $newBlueRpoints, $newBlueHpoints, $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r= $results->fetch_assoc();


$red = 'Red';
$blue = 'Blue';

$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $red, $updateType);
$query->execute();

$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $blue, $updateType);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
$query->execute();







$db->close();