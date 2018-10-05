<?php
session_start();
include("db.php");

$myTeam = $_SESSION['myTeam'];
$gameId = $_SESSION['gameId'];


$islandToChange = $_REQUEST['islandToChange'];
$newTeam = $_REQUEST['newTeam'];

$query = "";

if ($islandToChange == "special_island1") {
    $query = 'UPDATE games SET gameIsland1 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island2") {
    $query = 'UPDATE games SET gameIsland2 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island3") {
    $query = 'UPDATE games SET gameIsland3 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island4") {
    $query = 'UPDATE games SET gameIsland4 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island5") {
    $query = 'UPDATE games SET gameIsland5 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island6") {
    $query = 'UPDATE games SET gameIsland6 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island7") {
    $query = 'UPDATE games SET gameIsland7 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island8") {
    $query = 'UPDATE games SET gameIsland8 = ? WHERE (gameId = ?)';
} elseif ($islandToChange == "special_island9") {
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

$db->close();