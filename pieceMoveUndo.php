<?php
include("db.php");

$movementGameId = $_REQUEST['gameId'];
//$movementTurn = $_REQUEST['gameTurn'];
//$movementPhase = $_REQUEST['gamePhase'];
$myTeam = $_REQUEST['myTeam'];

$query = 'SELECT * FROM games WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $movementGameId);
$query->execute();
$results = $query->get_result();
$r= $results->fetch_assoc();

$movementTurn = $r['gameTurn'];
$movementPhase = $r['gamePhase'];



//Get the last movement made
$query = 'SELECT * FROM movements WHERE movementGameId = ? AND movementTurn = ? AND movementPhase = ? ORDER BY movementId DESC LIMIT 0, 1';
$query = $db->prepare($query);
$query->bind_param("iii", $movementGameId, $movementTurn, $movementPhase);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
if ($num_results > 0) {
    $r= $results->fetch_assoc();

    $movementId = $r['movementId'];
    $movementCost = $r['movementCost'];
    $movementFromPosition = $r['movementFromPosition'];
    $movementNowPlacement = $r['movementNowPlacement'];
    $movementFromContainer = $r['movementFromContainer'];

    //Get the pieces current information
    $query = 'SELECT * FROM placements WHERE (placementId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("i", $movementNowPlacement);
    $query->execute();
    $results = $query->get_result();
    $r= $results->fetch_assoc();

    $old_placementPositionId = $r['placementPositionId'];
    $old_placementContainerId = $r['placementContainerId'];
    $old_placementCurrentMoves = $r['placementCurrentMoves'];
    $new_placementCurrentMoves = $old_placementCurrentMoves + $movementCost;

    //Put the piece back
    $query = 'UPDATE placements SET placementPositionId = ?, placementCurrentMoves = ?, placementContainerId = ? WHERE (placementId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("iiii", $movementFromPosition, $new_placementCurrentMoves, $movementFromContainer, $movementNowPlacement);
    $query->execute();

    //Delete the Movement
    $query = 'DELETE FROM movements WHERE movementId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $movementId);
    $query->execute();

    //Update the other client's gameboard
    $newValue = 0;
    $updateType = "pieceMove";
    $Blue = "Blue";
    $Red = "Red";

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissiiii", $movementGameId, $newValue, $Blue, $updateType, $movementNowPlacement, $movementFromPosition, $movementFromContainer, $new_placementCurrentMoves);
    $query->execute();

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissiiii", $movementGameId, $newValue, $Red, $updateType, $movementNowPlacement, $movementFromPosition, $movementFromContainer, $new_placementCurrentMoves);
    $query->execute();

    $Spec = "Spec";
    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissiiii", $movementGameId, $newValue, $Spec, $updateType, $movementNowPlacement, $movementFromPosition, $movementFromContainer, $new_placementCurrentMoves);
    $query->execute();

    //Return information about how to undo the movement
    $arr = array('placementId' => $movementNowPlacement,
        'old_placementContainerId' => $old_placementContainerId,
        'old_placementPositionId' => $old_placementPositionId,
        'new_placementPositionId' => $movementFromPosition,
        'new_placementCurrentMoves' => $new_placementCurrentMoves,
        'new_placementContainerId' => $movementFromContainer);
    echo json_encode($arr);
} else {
    echo 3;
}


$db->close();
