<?php
set_time_limit(0);  //to wait forever (default timeout is 30 seconds)
include("db.php");

//variables from the request
$gameId = (int) $_REQUEST['gameId'];
$myTeam = $_REQUEST['myTeam'];  //not used -> in the sql statement, looking for all
$lastUpdateId = (int) $_REQUEST['lastUpdateId'];
//$allTeam = "All";

$updateId = 0;
$updateTeam = "Spec";

while(true) {
    sleep(.25);
    //call to database to check for the update
    $valuecheck = 0;
    $query = 'SELECT * FROM updates WHERE (updateGameId = ?) AND (updateId > ?) AND (updateTeam = ?) ORDER BY updateId ASC';
    $query = $db->prepare($query);
    $query->bind_param("iis", $gameId, $lastUpdateId, $updateTeam);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;

    if ($num_results > 0) {
        $r= $results->fetch_assoc();
        $updateId = $r['updateId'];
        $lastUpdateId = $updateId;
        $arr = array('updateType' => (string) $r['updateType'],
            'lastUpdateId' => $lastUpdateId,
            'updateTeam' => (string) $r['updateTeam'],
            'updatePlacementId' => (string) $r['updatePlacementId'],
            'updateNewPositionId' => (string) $r['updateNewPositionId'],
            'updateNewContainerId' => (string) $r['updateNewContainerId'],
            'updateNewMoves' => (string) $r['updateNewMoves'],
            'updateNewUnitId' => (string) $r['updateNewUnitId'],
            'updateBattlePieceState' => (string) $r['updateBattlePieceState'],
            'updateBattlePositionSelectedPieces' => (string) $r['updateBattlePositionSelectedPieces'],
            'updateBattlePiecesSelected' => (string) $r['updateBattlePiecesSelected'],
            'updateIsland' => (string) $r['updateIsland'],
            'updateIslandTeam' => (string) $r['updateIslandTeam']);
        echo json_encode($arr);
        break;
    }
}

$results->free();
$db->close();