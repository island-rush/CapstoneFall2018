<?php
session_start();




//TODO: more checks to make sure user isn't already logged in?
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


    if ($team == "Spectator") {
        //unlimited spectators, just go there and get updates?
        header("location:gameSpectator.php");
        exit;
    } else {

        $active = $r['gameActive'];
        if ($active == 0) {
            header("location:login.php?err2=1");
            exit;
        }

        //Go straight to game, don't wait for other player
        header("location:game.php");

        //Update the Database to say this team has joined
        if ($team == "Red") {
            if ($r['gameRedJoined'] == 1) {
                header("location:login.php?err=1");
                exit;
            }
            $query = 'UPDATE games SET gameRedJoined = ? WHERE (gameId = ?)';
        } else {
            if ($r['gameBlueJoined'] == 1) {
                header("location:login.php?err=1");
                exit;
            }
            $query = 'UPDATE games SET gameBlueJoined = ? WHERE (gameId = ?)';
        }
        $query = $db->prepare($query);
        $joinedValue = 1;
        $query->bind_param("ii", $joinedValue, $_SESSION['gameId']);
        $query->execute();

        $one = 1;
        $query = 'UPDATE updates SET updateValue = ? WHERE updateGameId = ?';
        $query = $db->prepare($query);
        $query->bind_param("ii", $one, $_SESSION['gameId']);
        $query->execute();

        $results->free();
        $db->close();


        $filename = 'resources/gameData/adjMatrix.csv';
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $counter = 0;
            while(($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $arraySize = count($data);
                for ($i=0; $i < $arraySize; $i++) {
                    $_SESSION['dist'][$counter][$i] = $data[$i];
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
    }

} else {
    header("location:login.php?err=1");
}
