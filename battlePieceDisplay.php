<?php
$query = 'SELECT * FROM battlePieces NATURAL JOIN placements NATURAL JOIN units WHERE (battlePieceState = ?) AND (battleGameId = ?) AND (battlePieceId = placementId)';
$query = $db->prepare($query);
$query->bind_param("ii", $boxId, $gameId);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
if ($num_results > 0) {
    for ($i = 0; $i < $num_results; $i++) {
        $r = $results->fetch_assoc();
        $battlePieceId = $r['battlePieceId'];
        $unitName = $r['unitName'];
        $unitId = $r['unitId'];
        $wasHit = $r['battlePieceWasHit'];

        echo "<div class='".$unitName." game_piece' data-wasHit='".$wasHit."' data-unitId='".$unitId."' data-unitName='".$unitName."' data-battlePieceId='".$battlePieceId."' onclick='battlePieceClick(event, this)'></div>";
    }
}
unset($boxId);