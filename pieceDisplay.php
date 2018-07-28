<?php
$gameId = $_SESSION['gameId'];

if (isset($positionId)) {

    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementPositionId = ?)';
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
            $unitId = $r['unitId'];
            $unitName = $r['unitName'];
            $unitTerrain = $r['unitTerrain'];


            if ($placementContainerId == 999999) {

                //opening for overall piece
                echo "<div class='".$unitName." game_piece' data-placementId='".$placementId."' data-unitTerrain='".$unitTerrain."' data-container='".$placementContainerId."' data-team='".$placementTeamId."' data-unitName='".$unitName."' data-unitId='".$unitId."' data-moves='".$placementCurrentMoves."' ";

                //functions for all pieces (container/non-container)
                echo "";

                if ($unitName == "transport" || $unitName == "aircraftCarrier" || $unitName == "lav") {
                    //functions for containers
                    echo "";
                } else {
                    //functions for non-containers
                    echo "";
                }

                echo ">";  // end of opening for overall piece

                //build containers for container pieces + pieces inside of them
                if ($unitName == "transport" || $unitName == "aircraftCarrier" || $unitName == "lav") {
                    if ($unitName == "transport") {
                        $classthing = "transportContainer";
                    } elseif ($unitName == "aircraftCarrier") {
                        $classthing = "aircraftCarrierContainer";
                    } else {
                        $classthing = "lavContainer";
                    }

                    //open the container
                    echo "<div class='".$classthing."' data-groundtype='".$classthing."' data-positionId='".$placementPositionId."'>";

                    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementContainerId = ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("ii", $gameId, $placementId);
                    $query->execute();
                    $results2 = $query->get_result();
                    $num_results2 = $results2->num_rows;
                    if ($num_results2 > 0) {
                        for ($b=0; $b < $num_results2; $b++) {
                            $x = $results2->fetch_assoc();
                            $placementId2 = $x['placementId'];
                            $placementCurrentMoves2 = $x['placementCurrentMoves'];
                            $placementPositionId2 = $x['placementPositionId'];
                            $placementContainerId2 = $x['placementContainerId'];
                            $placementTeamId2 = $x['placementTeamId'];
                            $unitId2 = $x['unitId'];
                            $unitName2 = $x['unitName'];
                            $unitTerrain2 = $x['unitTerrain'];

                            //assume only non-containers within a container (opening for piece within container)
                            echo "<div class='".$unitName2." game_piece' data-placementId='".$placementId2."' data-unitTerrain='".$unitTerrain2."' data-container='".$placementContainerId."' data-team='".$placementTeamId2."' data-unitName='".$unitName2."' data-unitId='".$unitId2."' data-moves='".$placementCurrentMoves2."' ";

                            //functions for all pieces (but only non-container)

                            echo "></div>";  // end the piece within container
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