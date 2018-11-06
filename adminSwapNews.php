<?php
include("db.php");
// Get the news alerts to swap from the POST
$gameId = (int) $_POST['gameId'];
$old1order = (int) $_POST['swap1order'];   //2
$old2order = (int) $_POST['swap2order'];   //3

$tempOrder = 999;

$query = "UPDATE newsAlerts SET newsOrder = ? WHERE newsGameId = ? AND newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $tempOrder, $gameId, $old1order);
$preparedQuery->execute();

$query = "UPDATE newsAlerts SET newsOrder = ? WHERE newsGameId = ? AND newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $old1order, $gameId, $old2order);
$preparedQuery->execute();

$query = "UPDATE newsAlerts SET newsOrder = ? WHERE newsGameId = ? AND newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $old2order, $gameId, $tempOrder);
$preparedQuery->execute();

$db->close();

header("location:admin.php");
