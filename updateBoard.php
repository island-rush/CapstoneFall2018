<?php
set_time_limit(0);  //to wait forever (default timeout is 30 seconds)
session_start();
include("db.php");

//variables from the request
$gameId = $_REQUEST['gameId'];
$myTeam = $_REQUEST['myTeam'];
$allTeam = "All";

$updateId = 0;

while(true) {
    sleep(.25);
    //call to database to check for the update
    $valuecheck = 0;
    $query = 'SELECT * FROM updates WHERE (updateGameId = ?) AND (updateValue = ?) AND (updateTeam != ?)';
    $query = $db->prepare($query);
    $query->bind_param("iis", $gameId, $valuecheck, $myTeam);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;

    if ($num_results > 0) {
        $r= $results->fetch_assoc();
        $updateId = $r['updateId'];
        $arr = array('updateType' => (string) $r['updateType'], 'updatePlacementId' => (string) $r['updatePlacementId'], 'updateNewPositionId' => (string) $r['updateNewPositionId'], 'updateNewContainerId' => (string) $r['updateNewContainerId'], 'updateNewUnitId' => (string) $r['updateNewUnitId'], 'updateBattlePieceState' => (string) $r['updateBattlePieceState'], 'updateBattlePositionSelectedPieces' => (string) $r['updateBattlePositionSelectedPieces'], 'updateBattlePiecesSelected' => (string) $r['updateBattlePiecesSelected'], 'updateIsland' => (string) $r['updateIsland'], 'updateIslandTeam' => (string) $r['updateIslandTeam']);
        echo json_encode($arr);
        break;
    }
}

$newValue = 1;
$query = 'UPDATE updates SET updateValue = ? WHERE (updateId = ?)';
$query = $db->prepare($query);
$query->bind_param("ii", $newValue, $updateId);
$query->execute();

$results->free();
$db->close();