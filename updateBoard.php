<?php
set_time_limit(0);  //to wait forever (default timeout is 30 seconds)

include("db.php");

//variables from the request
$gameId = $_REQUEST['gameId'];
$myTeam = $_REQUEST['myTeam'];

while(true) {
    sleep(.25);

    //call to database to check for the update
    $query = 'SELECT * FROM updates WHERE (updateGameId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("i", $gameId);
    $query->execute();
    $results = $query->get_result();
    $r= $results->fetch_assoc();

    if ($r['updateValue'] == 0 && $r['updateTeam'] != $myTeam) {

        $arr = array('updateType' => $r['updateType'], 'updatePlacementId' => $r['updatePlacementId'], 'updateNewPositionId' => $r['updateNewPositionId'], 'updateNewContainerId' => $r['updateNewContainerId']);
        echo json_encode($arr);

        break;
    }
}


$newValue = 1;
$query = 'UPDATE updates SET updateValue = ? WHERE (updateGameId = ?)';
$query = $db->prepare($query);
$query->bind_param("ii", $newValue, $gameId);
$query->execute();

$results->free();
$db->close();