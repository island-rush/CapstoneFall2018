<?php
session_start();
include("db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$islandFrom = (int) $_REQUEST['islandFrom'];
$islandTo = (int) $_REQUEST['islandTo'];
$unitName = $_REQUEST['unitName'];

$old_placementContainerId = (int) $_REQUEST['old_placementContainerId'];  //not used, don't care where come from, takes a move to board a container
$new_placementContainerId = (int) $_REQUEST['new_placementContainerId'];

$new_positionId = (int)$_REQUEST['new_positionId'];
$old_positionId = (int)$_REQUEST['old_positionId'];
$placementCurrentMoves = (int)$_REQUEST['placementCurrentMoves'];
$thingToEcho = 0;
if ($_SESSION['dist'][$old_positionId][$new_positionId] <= $placementCurrentMoves) {
    $thingToEcho = $_SESSION['dist'][$old_positionId][$new_positionId];
    //if moving into a container, 1 extra move
    if ($new_placementContainerId != 999999) {
        $thingToEcho++;
    }
} else {
    $thingToEcho = -1;
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
