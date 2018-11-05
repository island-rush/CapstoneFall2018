<?php
include("db.php");
// Get the news alerts to swap from the POST
$gameId = $_REQUEST['gameID'];
$old1order = $_REQUEST['swap1order'];
$old2order = $_REQUEST['swap2order'];

$tempOrder = 999;

$new1order = $swap2order;
$new2order = $swap1order;

$query = "UPDATE  newsAlerts SET newsOrder = ? WHERE gameId = ?, newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $tempOrder, $gameId, $old1order);
$preparedQuery->execute();

$query = "UPDATE  newsAlerts SET newsOrder = ? WHERE gameId = ?, newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $new2order, $gameId, $old2order);
$preparedQuery->execute();

$query = "UPDATE  newsAlerts SET newsOrder = ? WHERE gameId = ?, newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $new1order, $gameId, $tempOrder);
$preparedQuery->execute();
