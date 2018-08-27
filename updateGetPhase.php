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

$new_gamePhase = $gamePhase;
$new_gameTurn = $gameTurn;
$new_gameCurrentTeam = $gameCurrentTeam;

$_SESSION['gamePhase'] = $new_gamePhase;
$_SESSION['gameTurn'] = $new_gameTurn;
$_SESSION['gameCurrentTeam'] = $new_gameCurrentTeam;

if ($_SESSION['gameCurrentTeam'] != $_SESSION['myTeam']) {
    //not this team's turn, don't allow anything
    $canMove = "false";
    $canPurchase = "false";
    $canUndo = "false";
    $canNextPhase = "false";
    $canTrash = "false";
    $canAttack = "false";
} else {
    if ($_SESSION['gamePhase'] == 1) {
        //news alert
        $canMove = "false";
        $canPurchase = "false";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } elseif ($_SESSION['gamePhase'] == 2) {
        //reinforcement purchase
        $canMove = "false";
        $canPurchase = "true";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "true";
        $canAttack = "false";
    } elseif ($_SESSION['gamePhase'] == 3) {
        //combat
        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "true";
    } elseif ($_SESSION['gamePhase'] == 4) {
        //fortification movement
        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } elseif ($_SESSION['gamePhase'] == 5) {
        //reinforcement place
        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } elseif ($_SESSION['gamePhase'] == 6) {
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

//for testing purposes
//$canMove = "true";
//$canPurchase = "true";
//$canUndo = "true";
//$canNextPhase = "true";
//$canTrash = "true";
//$canAttack = "true";


$arr = array('gamePhase' => $new_gamePhase, 'gameTurn' => $new_gameTurn, 'gameCurrentTeam' => $new_gameCurrentTeam, 'canMove' => $canMove, 'canPurchase' => $canPurchase, 'canUndo' => $canUndo, 'canNextPhase' => $canNextPhase, 'canTrash' => $canTrash, 'canAttack' => $canAttack);
echo json_encode($arr);

$db->close();


