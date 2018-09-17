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
$myTeam = $_SESSION['myTeam'];

$gameRedRpoints = $r['gameRedRpoints'];
$gameBlueRpoints = $r['gameBlueRpoints'];
$gameRedHybridPoints = $r['gameRedHybridPoints'];
$gameBlueHybridPoints = $r['gameBlueHybridPoints'];


$new_gamePhase = $gamePhase;
$new_gameTurn = $gameTurn;
$new_gameCurrentTeam = $gameCurrentTeam;

if ($new_gameCurrentTeam != $_SESSION['myTeam']) {
    //not this team's turn, don't allow anything
    $canMove = "false";
    $canPurchase = "false";
    $canUndo = "false";
    $canNextPhase = "false";
    $canTrash = "false";
    $canAttack = "false";
} else {
    if ($new_gamePhase == 1) {
        //news alert
        $canMove = "false";
        $canPurchase = "false";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
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

$arr = array('gamePhase' => (string) $new_gamePhase, 'gameTurn' => (string) $new_gameTurn, 'gameCurrentTeam' => (string) $new_gameCurrentTeam, 'canMove' => (string) $canMove, 'canPurchase' => (string) $canPurchase, 'canUndo' => (string) $canUndo, 'canNextPhase' => (string) $canNextPhase, 'canTrash' => (string) $canTrash, 'canAttack' => (string) $canAttack, 'gameRedRpoints' => (string) $gameRedRpoints, 'gameBlueRpoints' => (string) $gameBlueRpoints, 'gameRedHybridPoints' => (string) $gameRedHybridPoints, 'gameBlueHybridPoints' => (string) $gameBlueHybridPoints);
echo json_encode($arr);

$db->close();


