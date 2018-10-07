<?php

if (isset($positionId)) {

    //Get all pieces from this game and this position
    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementPositionId = ?) AND (placementUnitId = unitId)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $gameId, $positionId);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;

    if ($num_results > 0) {
        for ($i=0; $i < $num_results; $i++) {
            $r= $results->fetch_assoc();
            $placementId = $r['placementId'];
            $placementCurrentMoves = $r['placementCurrentMoves'];
            $placementPositionId = $r['placementPositionId'];
            $placementContainerId = $r['placementContainerId'];
            $placementTeamId = $r['placementTeamId'];
            $placementBattleUsed = $r['placementBattleUsed'];
            $unitId = $r['unitId'];
            $unitName = $r['unitName'];
            $unitTerrain = $r['unitTerrain'];
            $unitCost = $r['unitCost'];

            if ($placementContainerId == 999999) {

                //opening for overall piece
                echo "<div class='".$unitName." gamePiece ".$placementTeamId."' title='".$unitName."&#013;Moves: ".$placementCurrentMoves."' data-placementId='".$placementId."' data-unitCost='".$unitCost."' data-placementBattleUsed='".$placementBattleUsed."' data-placementCurrentMoves='".$placementCurrentMoves."' data-placementContainerId='".$placementContainerId."' data-placementTeamId='".$placementTeamId."' data-unitTerrain='".$unitTerrain."' data-unitName='".$unitName."' data-unitId='".$unitId."' draggable='true' ondragstart='pieceDragstart(event, this);' onclick='pieceClick(event, this);' ondragenter='pieceDragenter(event, this);' ondragleave='pieceDragleave(event, this);'>";

                //build containers for container pieces + pieces inside of them
                if ($unitName == "transport" || $unitName == "aircraftCarrier") {
                    if ($unitName == "transport") {
                        $classthing = "transportContainer";
                    } elseif ($unitName == "aircraftCarrier") {
                        $classthing = "aircraftCarrierContainer";
                    }

                    //open the container
                    echo "<div class='".$classthing."' data-containerPopped='false' data-positionContainerId='".$placementId."' data-positionType='".$classthing."' data-positionId='".$placementPositionId."' ondragenter='containerDragenter(event, this);' ondragleave='containerDragleave(event, this);' ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);'>";

                    $query2 = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementContainerId = ?) AND (placementUnitId = unitId)';
                    $query2 = $db->prepare($query2);
                    $query2->bind_param("ii", $gameId, $placementId);
                    $query2->execute();
                    $results2 = $query2->get_result();
                    $num_results2 = $results2->num_rows;
                    if ($num_results2 > 0) {
                        for ($b=0; $b < $num_results2; $b++) {
                            $x = $results2->fetch_assoc();
                            $placementId2 = $x['placementId'];
                            $placementCurrentMoves2 = $x['placementCurrentMoves'];
                            $placementPositionId2 = $x['placementPositionId'];
                            $placementContainerId2 = $x['placementContainerId'];
                            $placementTeamId2 = $x['placementTeamId'];
                            $placementBattleUsed2 = $x['placementBattleUsed'];
                            $unitId2 = $x['unitId'];
                            $unitName2 = $x['unitName'];
                            $unitTerrain2 = $x['unitTerrain'];
                            $unitCost2 = $x['unitCost'];

                            //assume only non-containers within a container (opening for piece within container)
                            echo "<div class='".$unitName2." gamePiece ".$placementTeamId2."' title='".$unitName2."&#013;Moves: ".$placementCurrentMoves2."' data-placementId='".$placementId2."' data-unitCost='".$unitCost2."' data-placementBattleUsed='".$placementBattleUsed2."' data-placementCurrentMoves='".$placementCurrentMoves2."' data-placementContainerId='".$placementContainerId2."' data-placementTeamId='".$placementTeamId2."' data-unitTerrain='".$unitTerrain2."' data-unitName='".$unitName2."' data-unitId='".$unitId2."' draggable='true' ondragstart='pieceDragstart(event, this)' onclick='pieceClick(event, this);' ondragenter='pieceDragenter(event, this);' ondragleave='pieceDragleave(event, this);'></div>";
                        }
                    }
                    echo "</div>";  // end the container
                }
                echo "</div>";  // end the overall piece
            }
        }
    }
}

unset($positionId);
