<?php
session_start();
include("db.php");
//things
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$islandFrom = (int) $_REQUEST['islandFrom'];
$islandTo = (int) $_REQUEST['islandTo'];
$unitName = $_REQUEST['unitName'];

$old_placementContainerId = (int) $_REQUEST['old_placementContainerId'];  //not used, don't care where come from, takes a move to board a container
$new_placementContainerId = (int) $_REQUEST['new_placementContainerId'];

$new_positionId = (int)$_REQUEST['new_positionId'];
$old_positionId = (int)$_REQUEST['old_positionId'];
$placementId = (int)$_REQUEST['placementId'];
$thingToEcho = 0;

$redPlaceValid = array(55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 0, 13, 21, 20, 19);
$bluePlaceValid = array(65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 8, 7, 6, 12, 18, 25, 31, 38, 45, 54);

$query = 'SELECT * FROM placements WHERE (placementId = ?)';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();
$results = $query->get_result();
$r = $results->fetch_assoc();
$placementCurrentMoves = $r['placementCurrentMoves'];


//purchase container
if ($islandFrom == -4) {
    if ($myTeam == "Red") {
        //must have new postition id valid in list
        if (in_array($new_positionId, $redPlaceValid)) {
            $thingToEcho = 0;
            if ($new_placementContainerId != 999999) {
                $thingToEcho++;
            }
        } else {
            $thingToEcho = -1;
        }
    } else {
        //same as red
        if (in_array($new_positionId, $bluePlaceValid)) {
            $thingToEcho = 0;
            if ($new_placementContainerId != 999999) {
                $thingToEcho++;
            }
        } else {
            $thingToEcho = -1;
        }
    }
} else {
    if ($_SESSION['dist'][$old_positionId][$new_positionId] <= $placementCurrentMoves) {
        $thingToEcho = $_SESSION['dist'][$old_positionId][$new_positionId];
        //if moving into a container, 1 extra move
        if ($new_placementContainerId != 999999) {
            $thingToEcho++;
        }
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
if ($unitName == "missile") {
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



echo $thingToEcho;

$db->close();
