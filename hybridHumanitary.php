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

if ($points >= 3) {
    $one = 1;
    $nuke = "nukeHuman";
    $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsEffect = ? AND newsLength >= ? ORDER BY newsOrder DESC";
    $preparedQuery4 = $db->prepare($query4);
    $preparedQuery4->bind_param("iisi", $gameId, $one, $nuke, $one);
    $preparedQuery4->execute();
    $results4 = $preparedQuery4->get_result();
    $number_results = $results4->num_rows;
    if ($number_results > 0) {
        $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength >= ? ORDER BY newsOrder DESC";
        $preparedQuery4 = $db->prepare($query4);
        $preparedQuery4->bind_param("iii", $gameId, $one, $one);
        $preparedQuery4->execute();
        $results4 = $preparedQuery4->get_result();
        $r4= $results4->fetch_assoc();
        $humanitary = $r4['newsHumanitarian'];
        if ($humanitary == 1) {
            //switch 3 hpoints for 10 rpoints
            $three = 3;
            $ten = 10;
            $query = 'UPDATE games SET gameRedHpoints = gameRedHpoints - ?, gameRedRpoints = gameRedRpoints + ? WHERE gameId = ?';
            if ($myTeam == "Blue") {
                $query = 'UPDATE games SET gameBlueHpoints = gameBlueHpoints - ?, gameBlueRpoints = gameBlueRpoints + ? WHERE gameId = ?';
            }
            $query = $db->prepare($query);
            $query->bind_param("iii",$three, $ten, $gameId);
            $query->execute();
        }
    }
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
