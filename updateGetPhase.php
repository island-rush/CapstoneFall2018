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
$gameRedHpoints = $r['gameRedHpoints'];
$gameBlueHpoints = $r['gameBlueHpoints'];




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

//grab latest newsalert
$zero = 0;
$one = 1;
$query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength != ? ORDER BY newsOrder DESC";
$preparedQuery4 = $db->prepare($query4);
$preparedQuery4->bind_param("iii", $gameId, $one, $zero);
$preparedQuery4->execute();
$results4 = $preparedQuery4->get_result();
$r4= $results4->fetch_assoc();

$newsEffect = $r4['newsEffect'];
$newsText = $r4['newsText'];
$newsEffectText = $r4['newsEffectText'];


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
    'gameRedHpoints' => (string) $gameRedHpoints,
    'gameBlueHpoints' => (string) $gameBlueHpoints,
    'newsEffect' => (string) $newsEffect,
    'newsText' => (string) $newsText,
    'newsEffectText' => (string) $newsEffectText);
echo json_encode($arr);

$db->close();


