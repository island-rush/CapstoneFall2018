<?php
session_start();

if ( (isset($_POST['section'])) && (isset($_POST['instructor'])) && (isset($_POST['team'])) ){
    include("db.php");

    $section = $_POST['section'];
    $instructor = $_POST['instructor'];
    $team = $_POST['team'];

    $query = "SELECT * FROM GAMES WHERE gameInstructor = ? AND gameSection = ?";
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("ss", $instructor,$section);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $r= $results->fetch_assoc();

    $_SESSION['myTeam'] = $team;
    $_SESSION['gameId'] = $r['gameId'];
    $_SESSION['gameCurrentTeam'] = $r['gameCurrentTeam'];
    $_SESSION['gameTurn'] = $r['gameTurn'];
    $_SESSION['gamePhase'] = $r['gamePhase'];
    $_SESSION['gameBattleSection'] = $r['gameBattleSection'];
    $_SESSION['gameBattleSubSection'] = $r['gameBattleSubSection'];
    $_SESSION['gameBattleLastRoll'] = $r['gameBattleLastRoll'];
    $_SESSION['gameBattleLastMessage'] = $r['gameBattleLastMessage'];

    //If other team has joined, one of these values will be 1...go directly to playGame
    if ($r['gameRedJoined'] == 1 || $r['gameBlueJoined'] == 1) {
        header("location:game.php");
    } else {
        header("location:loginWaiting.php");
    }

    //Update the Database to say this team has joined
    if ($team == "Red") {
        $query = 'UPDATE games SET gameRedJoined = ? WHERE (gameId = ?)';
    } else {
        $query = 'UPDATE games SET gameBlueJoined = ? WHERE (gameId = ?)';
    }
    $query = $db->prepare($query);
    $joinedValue = 1;
    $query->bind_param("ii", $joinedValue, $_SESSION['gameId']);
    $query->execute();

    $results->free();
    $db->close();

    //Set the Phase of the game
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
            $canAttack = "false";
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
        }
    }

    //for testing purposes (always allow everything)
    $canMove = "true";
    $canPurchase = "true";
    $canUndo = "true";
    $canNextPhase = "true";
    $canTrash = "true";
    $canAttack = "true";

    $_SESSION['canMove'] = $canMove;
    $_SESSION['canPurchase'] = $canPurchase;
    $_SESSION['canUndo'] = $canUndo;
    $_SESSION['canNextPhase'] = $canNextPhase;
    $_SESSION['canTrash'] = $canTrash;
    $_SESSION['canAttack'] = $canAttack;

} else {
    header("location:login.php?err=1");
}
