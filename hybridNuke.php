<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$islandNum = (int) $_REQUEST['lastNumber'];

$query = 'SELECT * FROM games WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i",$gameId);
$query->execute();
$results = $query->get_result();
$r= $results->fetch_assoc();

$points = (int) $r['gameRedHpoints'];
if ($myTeam == "Blue") {
    $points = (int) $r['gameBlueHpoints'];
}

if ($points >= 12) {
    //TODO: include missile spots
    $islandSpots = [
        [0, 1, 9, 13, 14, 75, 76, 77, 78],
        [5],
        [5],
        [5],
        [5],
        [5],
        [5],
        [5],
        [5],
        [5],
        [5],
        [5]];

    $thisIslandSpots = $islandSpots[$islandNum - 1];

    for ($x = 0; $x < sizeof($thisIslandSpots); $x++) {
        //delete pieces from this position
        $positionId = $thisIslandSpots[$x];

        $query = 'SELECT * FROM placements WHERE placementPositionId = ? AND placementGameId = ?';
        $query = $db->prepare($query);
        $query->bind_param("ii", $positionId, $gameId);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        if ($num_results > 0) {
            for ($i = 0; $i < $num_results; $i++) {
                //delete the piece and send update about piece deletion to all 3 clients
                $b = $results->fetch_assoc();
                $placementId = $b['placementId'];

                $query2 = 'DELETE FROM placements WHERE placementId = ?';
                $query2= $db->prepare($query2);
                $query2->bind_param("i", $placementId);
                $query2->execute();

                $newValue = 0;
                $updateType = "pieceTrash";
                $Red = "Red";
                $Blue = "Blue";
                $Spec = "Spec";

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissi", $gameId, $newValue, $Red, $updateType, $placementId);
                $query->execute();

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissi", $gameId, $newValue, $Blue, $updateType, $placementId);
                $query->execute();

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $placementId);
                $query->execute();
            }
        }
    }
    //disable island with newsalert for 999999 turns
    $order = 0;
    $length = 989898;
    $activated = 1;
    $zone = $islandNum + 100;
    $disable = "disable";
    $team = "All";
    $allPieces = '{"transport":1, "submarine":1, "destroyer":1, "aircraftCarrier":1, "soldier":1, "artillery":1, "tank":1, "marine":1, "lav":1, "attackHeli":1, "sam":1, "fighter":1, "bomber":1, "stealthBomber":1, "tanker":1}';
    $query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsLength, newsActivated) VALUES(?,?,?,?,?,?,?,?)';
    $query = $db->prepare($query);
    $query->bind_param("iisssiii",$gameId, $order, $team, $allPieces, $disable, $zone, $length, $activated);
    $query->execute();

    //change ownership to nuked?
    $islandToChange = "";
    if ($islandNum == 1) {
        $islandToChange = "special_island1";
        $query = 'UPDATE games SET gameIsland1 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 2) {
        $islandToChange = "special_island2";
        $query = 'UPDATE games SET gameIsland2 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 3) {
        $islandToChange = "special_island3";
        $query = 'UPDATE games SET gameIsland3 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 4) {
        $islandToChange = "special_island4";
        $query = 'UPDATE games SET gameIsland4 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 5) {
        $islandToChange = "special_island5";
        $query = 'UPDATE games SET gameIsland5 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 6) {
        $islandToChange = "special_island6";
        $query = 'UPDATE games SET gameIsland6 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 7) {
        $islandToChange = "special_island7";
        $query = 'UPDATE games SET gameIsland7 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 8) {
        $islandToChange = "special_island8";
        $query = 'UPDATE games SET gameIsland8 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 9) {
        $islandToChange = "special_island9";
        $query = 'UPDATE games SET gameIsland9 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 10) {
        $islandToChange = "special_island10";
        $query = 'UPDATE games SET gameIsland10 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 11) {
        $islandToChange = "special_island11";
        $query = 'UPDATE games SET gameIsland11 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 12) {
        $islandToChange = "special_island12";
        $query = 'UPDATE games SET gameIsland12 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 13) {
        $islandToChange = "special_island13";
        $query = 'UPDATE games SET gameIsland13 = ? WHERE (gameId = ?)';
    } elseif ($islandNum == 14) {
        $islandToChange = "special_island14";
        $query = 'UPDATE games SET gameIsland14 = ? WHERE (gameId = ?)';
    }
    $newTeam = "Nuke";
    $query = $db->prepare($query);
    $query->bind_param("si", $newTeam, $gameId);
    $query->execute();

    $newValue = 0;
    $updateType = "islandChange";
    $Red = "Red";
    $Blue = "Blue";
    $Spec = "Spec";

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateIsland, updateIslandTeam) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissss", $gameId, $newValue, $Red, $updateType, $islandToChange, $newTeam);
    $query->execute();

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateIsland, updateIslandTeam) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissss", $gameId, $newValue, $Blue, $updateType, $islandToChange, $newTeam);
    $query->execute();

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updateIsland, updateIslandTeam) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissss", $gameId, $newValue, $Spec, $updateType, $islandToChange, $newTeam);
    $query->execute();

    //take away the hpoints
    $twelve = 12;
    $query = 'UPDATE games SET gameRedHpoints = gameRedHpoints - ? WHERE gameId = ?';
    if ($myTeam == "Blue") {
        $query = 'UPDATE games SET gameBlueHpoints = gameBlueHpoints - ? WHERE gameId = ?';
    }
    $query = $db->prepare($query);
    $query->bind_param("ii", $twelve, $gameId);
    $query->execute();
}


//might as well update the clients? (could put this inside the if statement)
$Blue = "Blue";
$Red = "Red";
$Spec = "Spec";
$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Blue, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Red, $updateType);
$query->execute();


$db->close();