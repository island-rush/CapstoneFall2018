<?php
session_start();
include("db.php");

$myTeam = $_SESSION['myTeam'];
$gameId = $_SESSION['gameId'];


$islandToChange = $_REQUEST['islandToChange'];
$newTeam = $_REQUEST['newTeam'];

$query = "";
$missileCheck = 0;

if ($islandToChange == "special_island1") {
    $query = 'UPDATE games SET gameIsland1 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island2") {
    $missileCheck = 2;
    $query = 'UPDATE games SET gameIsland2 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island3") {
    $query = 'UPDATE games SET gameIsland3 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island4") {
    $query = 'UPDATE games SET gameIsland4 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island5") {
    $query = 'UPDATE games SET gameIsland5 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island6") {
    $missileCheck = 6;
    $query = 'UPDATE games SET gameIsland6 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island7") {
    $missileCheck = 7;
    $query = 'UPDATE games SET gameIsland7 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island8") {
    $query = 'UPDATE games SET gameIsland8 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island9") {
    $missileCheck = 9;
    $query = 'UPDATE games SET gameIsland9 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island10") {
    $query = 'UPDATE games SET gameIsland10 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island11") {
    $query = 'UPDATE games SET gameIsland11 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island12") {
    $query = 'UPDATE games SET gameIsland12 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island13") {
    $query = 'UPDATE games SET gameIsland13 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island14") {
    $query = 'UPDATE games SET gameIsland14 = ? WHERE (gameId = ?)';
}

$query = $db->prepare($query);
$query->bind_param("si", $newTeam, $gameId);
$query->execute();

$newValue = 0;
$updateType = "islandChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateIsland, updateIslandTeam) VALUES (?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissss", $gameId, $newValue, $myTeam, $updateType, $islandToChange, $newTeam);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateIsland, updateIslandTeam) VALUES (?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissss", $gameId, $newValue, $Spec, $updateType, $islandToChange, $newTeam);
$query->execute();


//is there a missile on that island, if so make sure it is this team...
if ($missileCheck != 0) {

    $myArray = array(
        2 => 121,
        6 => 122,
        7 => 123,
        9 => 124
    );

    $positionForMissile = $myArray[$missileCheck];

    $query = 'SELECT * FROM placements WHERE (placementPositionId = ?) AND (placementGameId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $positionForMissile, $gameId);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;

    if ($num_results == 1) {
        $r = $results->fetch_assoc();
        $placementId = $r['placementId'];
        $placementTeamId = $r['placementTeamId'];

        $newTeam = "Blue";
        if ($placementTeamId == "Blue") {
            $newTeam = "Red";
        }

        $query = 'UPDATE placements SET placementTeamId = ? WHERE placementId = ?';
        $query = $db->prepare($query);
        $query->bind_param("si", $newTeam, $placementId);
        $query->execute();
    }
}


$db->close();