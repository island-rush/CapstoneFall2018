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
$query = 'SELECT * FROM newsAlerts WHERE (newsGameId = ?) AND (newsActivated = ?)';
$query = $db->prepare($query);
$query->bind_param("ii", $gameId, $activated_value);
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
                if ($newsZone == 200 || $newsZone == $new_positionId || $newsZone == $old_positionId || ($newsZone + 100) == $islandFrom || ($newsZone + 100) == $islandTo) {
                    //if piece name is marked 1 in the pieces json?
                    $decoded = json_decode($newsPieces, true);
//                    echo $decoded;
//                    echo $decoded['destroyer'];
                    if ($decoded[$unitName] == 1) {
                        $thingToEcho = -2;
                    }
                }
            }
        }
    }
}

echo $thingToEcho;

$db->close();
