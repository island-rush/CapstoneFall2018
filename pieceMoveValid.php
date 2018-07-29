<?php
session_start();

$new_positionId = (int)$_REQUEST['new_positionId'];
$old_positionId = (int)$_REQUEST['old_positionId'];
$placementCurrentMoves = (int)$_REQUEST['placementCurrentMoves'];

if ($_SESSION['dist'][$old_positionId][$new_positionId] <= $placementCurrentMoves) {
    echo $_SESSION['dist'][$old_positionId][$new_positionId];
} else {
    echo -1;
}
