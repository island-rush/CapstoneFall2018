<?php
include("db.php");
session_start();
$battlePieceId = (int) $_REQUEST['battlePieceId'];
$new_battlePieceState = (int) $_REQUEST['new_battlePieceState'];
$myTeam = $_SESSION['myTeam'];
$gameId = $_SESSION['gameId'];

if ($new_battlePieceState != 9) {
    $query = 'UPDATE battlePieces SET battlePieceState = ? WHERE (battlePieceId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $new_battlePieceState, $battlePieceId);
    $query->execute();

    //tell other client about the battle piece moving
    $newValue = 0;
    $updateType = "battlePieceMove";
    $Red = "Red";
    $Blue = "Blue";

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateBattlePieceState) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissii", $gameId, $newValue, $Blue, $updateType, $battlePieceId, $new_battlePieceState);
    $query->execute();

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateBattlePieceState) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissii", $gameId, $newValue, $Red, $updateType, $battlePieceId, $new_battlePieceState);
    $query->execute();

    $Spec = "Spec";
    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateBattlePieceState) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissii", $gameId, $newValue, $Spec, $updateType, $battlePieceId, $new_battlePieceState);
    $query->execute();
} else {
    //delete the piece from database
    $query = 'DELETE FROM battlePieces WHERE battlePieceId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $battlePieceId);
    $query->execute();

    //do a selection for pieces inside of it
    //for each piece inside, move it, and send update for its movement (move into the same position but container = 999999
    $fighter = 11;
    $query = 'SELECT * FROM placements WHERE (placementContainerId = ?) AND (placementUnitId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $battlePieceId, $fighter);
    $query->execute();
    $results = $query->get_result();
    $numresults = $results->num_rows;
    if ($numresults > 0){
        for ($i = 0; $i < $numresults; $i++) {
            //set containerid to 999999
            $r = $results->fetch_assoc();
            $thisPlacementId = $r['placementId'];
            $thisPosition = $r['placementPositionId'];
            $thisMoves = $r['placementCurrentMoves'];

            $noContainer = 999999;
            $query = 'UPDATE placements SET placementContainerId = ? WHERE placementId = ?';
            $query = $db->prepare($query);
            $query->bind_param("ii", $noContainer, $thisPlacementId);
            $query->execute();

            $newValue = 0;
            $updateType = "pieceMove";
            $Red = "Red";
            $Blue = "Blue";
            $Spec = "Spec";

            //update all 3 clients with move out
            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissiiii", $gameId, $newValue, $Red, $updateType, $thisPlacementId, $thisPosition, $noContainer, $thisMoves);
            $query->execute();

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissiiii", $gameId, $newValue, $Blue, $updateType, $thisPlacementId, $thisPosition, $noContainer, $thisMoves);
            $query->execute();

            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iissiiii", $gameId, $newValue, $Spec, $updateType, $thisPlacementId, $thisPosition, $noContainer, $thisMoves);
            $query->execute();
        }
    }


    //delete the real piece from database
    $query = 'DELETE FROM placements WHERE placementId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $battlePieceId);
    $query->execute();

    //delete the stuff within the container (for transport kills)(carrier handled above)
    $query = 'DELETE FROM placements WHERE placementContainerId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $battlePieceId);
    $query->execute();

    //Tell other client about deletion
    $newValue = 0;
    $updateType = "pieceDelete";
    $Red = "Red";
    $Blue = "Blue";
    $Spec = "Spec";

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissi", $gameId, $newValue, $Red, $updateType, $battlePieceId);
    $query->execute();

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissi", $gameId, $newValue, $Blue, $updateType, $battlePieceId);
    $query->execute();

    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $battlePieceId);
    $query->execute();
}


$db->close();