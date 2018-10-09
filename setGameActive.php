<?php
/**
 * Created by PhpStorm.
 * User: C19Eric.Yandura
 * Date: 10/4/2018
 * Time: 10:51 AM
 */

session_start();
include("db.php");

$gameId = $_SESSION['gameId'];

// Get new point values from the POST
$gameActive = $_REQUEST['gameActive'];


$query = "UPDATE GAMES SET gameActive = ? WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("ii", $gameActive, $gameId);
$preparedQuery->execute();
