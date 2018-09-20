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
$gameRedHpoints = $r['gameRedHpoints'];
$gameBlueHpoints = $r['gameBlueHpoints'];

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

        if ($gameTurn > 3) {
            $addPoints = 0;

            //for each island ownership, know how many points each is worth and add to the current team's reinforcement points
            if ($r['gameIsland1'] == $gameCurrentTeam) {
                $addPoints += 4;
            }
            if ($r['gameIsland2'] == $gameCurrentTeam) {
                $addPoints += 6;
            }
            if ($r['gameIsland3'] == $gameCurrentTeam) {
                $addPoints += 4;
            }
            if ($r['gameIsland4'] == $gameCurrentTeam) {
                $addPoints += 3;
            }
            if ($r['gameIsland5'] == $gameCurrentTeam) {
                $addPoints += 8;
            }
            if ($r['gameIsland6'] == $gameCurrentTeam) {
                $addPoints += 7;
            }
            if ($r['gameIsland7'] == $gameCurrentTeam) {
                $addPoints += 7;
            }
            if ($r['gameIsland8'] == $gameCurrentTeam) {
                $addPoints += 8;
            }
            if ($r['gameIsland9'] == $gameCurrentTeam) {
                $addPoints += 8;
            }
            if ($r['gameIsland10'] == $gameCurrentTeam) {
                $addPoints += 5;
            }
            if ($r['gameIsland11'] == $gameCurrentTeam) {
                $addPoints += 5;
            }
            if ($r['gameIsland12'] == $gameCurrentTeam) {
                $addPoints += 5;
            }
            if ($r['gameIsland13'] == $gameCurrentTeam) {
                $addPoints += 15;
            }
            if ($r['gameIsland14'] == $gameCurrentTeam) {
                $addPoints += 25;
            }

            if ($gameCurrentTeam == "Red") {
                $gameRedRpoints += $addPoints;
            } else {
                $gameBlueRpoints += $addPoints;
            }

            //update games table with new rpoints
            $query = 'UPDATE games SET gameRedRpoints = ?, gameBlueRpoints = ? WHERE (gameId = ?)';
            $query = $db->prepare($query);
            $query->bind_param("iii", $gameRedRpoints, $gameBlueRpoints, $gameId);
            $query->execute();
        }


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

        //This marks the end of this player's turn
        //reset the moves of each piece for the team that just ended their turn / reset battle used
        $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementTeamId = ?) AND (unitId = placementUnitId)';
        $query = $db->prepare($query);
        $query->bind_param("is", $gameId, $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;

        for ($x = 0; $x < $num_results; $x++) {
            $r= $results->fetch_assoc();

            $placementId = $r['placementId'];
            $placementMovesReset = $r['unitMoves'];
            $battleUsed = 0;

            $query2 = 'UPDATE placements SET placementBattleUsed = ?, placementCurrentMoves = ? WHERE (placementId = ?)';
            $query2 = $db->prepare($query2);
            $query2->bind_param("iii", $battleUsed, $placementMovesReset, $placementId);
            $query2->execute();
        }
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
    'gameRedHpoints' => (string) $gameRedHpoints,
    'gameBlueHpoints' => (string) $gameBlueHpoints,
    'newsalertthing1' => $newsalertthing1,
    'newsalertthing2' => $newsalertthing2);
echo json_encode($arr);






$db->close();


