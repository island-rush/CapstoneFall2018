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
    $_SESSION['gameBattlePosSelected'] = $r['gameBattlePosSelected'];

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


    $filename = 'resources/gameData/adjMatrix.csv';
    if (($handle = fopen($filename, "r")) !== FALSE) {
        $counter = 0;
        while(($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            $arraySize = count($data);
            for ($i=0; $i < $arraySize; $i++) {
                $_SESSION['dist'][$counter][$i] = $data[$i];
                $_SESSION['adjacency'][$counter][$i] = $data[$i];
            }
            $counter++;
        }
    }
    fclose($handle);

    for ($k = 0; $k < $arraySize; ++$k) {
        for ($i = 0; $i < $arraySize; ++$i) {
            for ($j = 0; $j < $arraySize; ++$j) {
                if (($_SESSION['dist'][$i][$k] * $_SESSION['dist'][$k][$j] != 0) && ($i != $j)) {
                    if (($_SESSION['dist'][$i][$k] + $_SESSION['dist'][$k][$j] < $_SESSION['dist'][$i][$j]) || ($_SESSION['dist'][$i][$j] == 0)) {
                        $_SESSION['dist'][$i][$j] = $_SESSION['dist'][$i][$k] + $_SESSION['dist'][$k][$j];
                    }
                }
            }
        }
    }

    $filename2 = 'resources/gameData/attackMatrix.csv';
    if (($handle = fopen($filename2, "r")) !== FALSE) {
        $counter = 0;
        while(($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            $arraySize = count($data);
            for ($i=0; $i < $arraySize; $i++) {
                $_SESSION['attack'][$counter][$i] = $data[$i];
            }
            $counter++;
        }
    }
    fclose($handle);

} else {
    header("location:login.php?err=1");
}
