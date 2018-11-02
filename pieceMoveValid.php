<?php
session_start();
include("db.php");
//things
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$islandFrom = (int) $_REQUEST['islandFrom'];
$islandTo = (int) $_REQUEST['islandTo'];
$unitName = $_REQUEST['unitName'];
$unitId = $_REQUEST['unitId'];

$old_placementContainerId = (int) $_REQUEST['old_placementContainerId'];  //not used, don't care where come from, takes a move to board a container
$new_placementContainerId = (int) $_REQUEST['new_placementContainerId'];

$new_positionId = (int)$_REQUEST['new_positionId'];
$old_positionId = (int)$_REQUEST['old_positionId'];
$placementId = (int)$_REQUEST['placementId'];
$thingToEcho = 0;

$redPlaceValid = array(55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 0, 13, 21, 20, 19);
$bluePlaceValid = array(65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 8, 7, 6, 12, 18, 25, 31, 38, 45, 54);
$airfieldPosition = array(56, 57, 78, 83, 89, 113, 116, 66, 68);

$query = 'SELECT * FROM placements WHERE (placementId = ?)';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();
$results = $query->get_result();
$r = $results->fetch_assoc();
$placementCurrentMoves = $r['placementCurrentMoves'];


//purchase container
if ($islandFrom == -4) {
        //must have new postition id valid in list
        if (($myTeam == "Red" && in_array($new_positionId, $redPlaceValid)) ||
            (($myTeam == "Blue" && in_array($new_positionId, $bluePlaceValid)))) {
            //db query to see if there are any enemy team pieces there
            $goodPlace = 0;
            if ($unitId == 11 || $unitId == 12 || $unitId == 13 || $unitId == 14) {
                if (in_array($new_positionId, $airfieldPosition)) {
                    $goodPlace = 1;
                }
            }
            else {
                $goodPlace = 1;
            }
            if ($goodPlace == 1) {
                $query = 'SELECT * FROM placements WHERE (placementPositionId = ?) AND (placementTeamId != ?) AND (placementGameId = ?)';
                $query = $db->prepare($query);
                $query->bind_param("isi", $new_positionId, $myTeam, $gameId);
                $query->execute();
                $results = $query->get_result();
                $num_results = $results->num_rows;
                if ($num_results > 0) {
                    $thingToEcho = -5;
                } else {
                    $thingToEcho = 0;
                }
            }
            else {
                $thingToEcho = -4;
            }
        } else {
            $thingToEcho = -4;
        }
} else {
    if ($_SESSION['dist'][$old_positionId][$new_positionId] <= $placementCurrentMoves) {
        $thingToEcho = $_SESSION['dist'][$old_positionId][$new_positionId];
    } else {
        $thingToEcho = -1;
    }
}

//get any + all active news alerts for both teams

//loop through and see if any of the attributes for this match what is or isnt disabled

//echo -2 if match on any

//else echo regular echo value (-1 or movement cost)

$activated_value = 1;
$query = 'SELECT * FROM newsAlerts WHERE (newsGameId = ?) AND (newsActivated = ?) AND (newsLength >= ?)';
$query = $db->prepare($query);
$query->bind_param("iii", $gameId, $activated_value, $activated_value);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
if ($num_results > 0) {
    for ($i = 0; $i < $num_results; $i++) {
        $r = $results->fetch_assoc();
        $newsTeam = $r['newsTeam'];
        if ($newsTeam == $myTeam || $newsTeam == "All") {
            $newsEffect = $r['newsEffect'];

            //assume not dealing with other things for now
            if ($newsEffect == "disable") {

                $newsPieces = $r['newsPieces'];
                $newsZone = $r['newsZone'];
                //zone is 200, or zone matches position, or zone matches islandnum + 100
//                echo $newsZone;
                if ($newsZone == 200 ||
                    ($newsZone == $new_positionId && $new_positionId < 100) ||
                    ($newsZone == $old_positionId && $old_positionId < 100) ||
                    (($newsZone) == $islandFrom + 100) ||
                    (($newsZone) == $islandTo + 100) ||
                    (($newsZone > 1000) && (($newsZone - 1000 == $new_positionId) || ($newsZone - 1000 == $old_positionId)))) {
//                    echo "bitchin";
                    $decoded = json_decode($newsPieces, true);

                    if ((int) $decoded[$unitName] == 1) {
                        if ((int) $old_positionId != 118){  //purchased is exempt
                            $thingToEcho = -2;
                        }
                    }
                }
            }
        }
    }
}

//missile checks (could refactor above to not execute if missle, but this override is okay (since echo is below it))
if ($unitName == "LandBasedSeaMissile") {
    //old position == 118
    //new position must be 121, 122, 123, 124
    //new position must be in an island owned by this team
    //this position must be empty (prevented by making missiles 100% of container/position)
    $possibleMissilePositions = [121, 122, 123, 124];
    $thingToEcho = -3;
    if ($old_positionId == 118) {
        if (in_array($new_positionId, $possibleMissilePositions)) {
            $query = 'SELECT * FROM games WHERE gameId = ?';
            $query = $db->prepare($query);
            $query->bind_param("i", $gameId);
            $query->execute();
            $results = $query->get_result();
            $r = $results->fetch_assoc();
            $islandOwner = 'badDefaultValueInitialize';
            if ($islandTo == 2) {
                //121
                $islandOwner = $r['gameIsland2'];
            } elseif ($islandTo == 6) {
                //122
                $islandOwner = $r['gameIsland6'];
            } elseif ($islandTo == 7) {
                //123
                $islandOwner = $r['gameIsland7'];
            } else {
                //islandTo = 9, 124
                $islandOwner = $r['gameIsland9'];
            }
            if ($islandOwner == $myTeam) {
                //good to move there
                $thingToEcho = 0;
            }
        }
    }
}

if ($thingToEcho > 1) {
    //Force one move at a time
    echo -3;
}

if ($unitId == 9 || $unitId == 11 || $unitId == 12 || $unitId == 13 || $unitId == 14) {
    //if air unit
    $adjSam = array();
    for ($i = 0; $i < 117; $i++) {
        if ($_SESSION['dist'][$new_positionId][$i] <= 1) {
            array_push($adjSam, $i);
        }
    }
    for ($i = 0; $i < sizeof($adjSam); $i++) {
        $query = 'SELECT * FROM placements WHERE (placementPositionId = ?) AND (placementTeamId != ?) AND (placementUnitId = 10)';
        $query = $db->prepare($query);
        $query->bind_param("is", $adjSam[$i], $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        $diceRoll = rand(1,6);
//        $diceRoll = 6;
        $killed = 0;
        for ($k = 0; $k < $num_results; $k++) {
            if ($unitId != 13) {
                if ($diceRoll >= $_SESSION['attack'][10][$unitId]) {
                    $killed = 1;
                    break;
                }
            }
            else {
                if ($new_positionId > 55) {
                    if ($diceRoll >= $_SESSION['attack'][10][$unitId]) {
                        $killed = 1;
                        break;
                    }
                }
            }
        }
        if ($killed == 1) {
            //Know piece ID, delete pieceID from placements, update Red, update Blue, update Spec
            $query = 'DELETE FROM placements WHERE placementId = ?';
            $query = $db->prepare($query);
            $query->bind_param("i", $placementId);
            $query->execute();

            $newValue = 0;
            $updateType = "pieceTrash";
            $Blue = "Blue";
            $Red = "Red";

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Blue, $updateType, $placementId);
            $query->execute();

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Red, $updateType, $placementId);
            $query->execute();

            $Spec = "Spec";
            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $placementId);
            $query->execute();

            //prevent future undo
            $query = 'DELETE FROM movements WHERE movementGameId = ?';
            $query = $db->prepare($query);
            $query->bind_param("i", $gameId);
            $query->execute();
            $thingToEcho = -10;
        }
    }
}


echo $thingToEcho;

$db->close();
