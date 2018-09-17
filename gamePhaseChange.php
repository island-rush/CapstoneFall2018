<?php
session_start();  //needed to update session variables
include("db.php");
$gameId = $_SESSION['gameId'];
$query = "SELECT * FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r= $results->fetch_assoc();


$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameTurn = $r['gameTurn'];

$gameRedRpoints = $r['gameRedRpoints'];
$gameBlueRpoints = $r['gameBlueRpoints'];
$gameRedHybridPoints = $r['gameRedHybridPoints'];
$gameBlueHybridPoints = $r['gameBlueHybridPoints'];

$myTeam = $_SESSION['myTeam'];

$new_gamePhase = ($gamePhase % 7) + 1;
$new_gameTurn = $gameTurn + 1;

if ($new_gamePhase == 1) {
    if ($gameCurrentTeam == "Red") {
        $new_gameCurrentTeam = "Blue";
    } else {
        $new_gameCurrentTeam = "Red";
    }
} else {
    $new_gameCurrentTeam = $gameCurrentTeam;
}

$query = 'UPDATE games SET gamePhase = ?, gameTurn = ?, gameCurrentTeam = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("iisi", $new_gamePhase, $new_gameTurn, $new_gameCurrentTeam, $gameId);
$query->execute();

$newsalertthing1 = "newsalertthing1Default";
$newsalertthing2 = "newsalertthing2Default";
//.....etc for as many needed for news table / updating process

if ($new_gameCurrentTeam != $_SESSION['myTeam']) {
    //not this team's turn, don't allow anything
    $canMove = "false";
    $canPurchase = "false";
    $canUndo = "false";
    $canNextPhase = "false";
    $canTrash = "false";
    $canAttack = "false";

    //copy this code from below (or fix the if statements to make better (not as important to be efficient here))
    if ($new_gamePhase == 1) {
        $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ?";
        $preparedQuery4 = $db->prepare($query4);
        $preparedQuery4->bind_param("i", $gameId);
        $preparedQuery4->execute();
        $results4 = $preparedQuery4->get_result();
        $r4= $results4->fetch_assoc();

        $newsalertthing1 = $r4['newsThing1'];
        $newsalertthing2 = $r4['newsThing2'];
    }
} else {
    if ($new_gamePhase == 1) {
        //news alert
        $canMove = "false";
        $canPurchase = "false";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";

        //grab all news things from the database (table not yet defined)
        //make sure to get the next alert in order! (or have something to identify that its been used already like updates used to be)
        $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ?";
        $preparedQuery4 = $db->prepare($query4);
        $preparedQuery4->bind_param("i", $gameId);
        $preparedQuery4->execute();
        $results4 = $preparedQuery4->get_result();
        $r4= $results4->fetch_assoc();

        $newsalertthing1 = $r4['newsThing1'];
        $newsalertthing2 = $r4['newsThing2'];


    } elseif ($new_gamePhase == 2) {
        //reinforcement purchase
        $canMove = "false";
        $canPurchase = "true";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "true";
        $canAttack = "false";
    } elseif ($new_gamePhase == 3) {
        //combat
        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "true";
    } elseif ($new_gamePhase == 4) {
        //fortification movement
        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } elseif ($new_gamePhase == 5) {
        //reinforcement place
        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } elseif ($new_gamePhase == 6) {
        //hybrid warfare
        $canMove = "false";
        $canPurchase = "false";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } else {
        //tally points (7)
        $canMove = "false";
        $canPurchase = "false";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";

        //TODO: reset the stuff like battle used = 0
    }
}

$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $myTeam, $updateType);
$query->execute();



$arr = array('gamePhase' => (string) $new_gamePhase,
    'gameTurn' => (string) $new_gameTurn,
    'gameCurrentTeam' => (string) $new_gameCurrentTeam,
    'canMove' => (string) $canMove,
    'canPurchase' => (string) $canPurchase,
    'canUndo' => (string) $canUndo,
    'canNextPhase' => (string) $canNextPhase,
    'canTrash' => (string) $canTrash,
    'canAttack' => (string) $canAttack,
    'gameRedRpoints' => (string) $gameRedRpoints,
    'gameBlueRpoints' => (string) $gameBlueRpoints,
    'gameRedHybridPoints' => (string) $gameRedHybridPoints,
    'gameBlueHybridPoints' => (string) $gameBlueHybridPoints,
    'newsalertthing1' => $newsalertthing1,
    'newsalertthing2' => $newsalertthing2);
echo json_encode($arr);

$db->close();


