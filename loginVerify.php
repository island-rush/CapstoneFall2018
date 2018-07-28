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
        header("location:playGame.php");
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
    $query->bind_param("i", $joinedValue, $_SESSION['gameId']);
    $query->execute();

    $results->free();
    $db->close();

} else {
    header("location:login.php?err=1");
}
