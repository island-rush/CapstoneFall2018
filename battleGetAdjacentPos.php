<?php
session_start();

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$positionSelected = $_REQUEST['positionSelected'];

$adjacentArray = [];


$n = sizeof($_SESSION['dist'][0]);
for ($j = 0; $j < $n; $j++) {
    if ($n != $positionSelected) {
        if ($_SESSION['dist'][$positionSelected][$j] <= 1) {
            array_push($adjacentArray, $j);
        }
    }
}


$arr = array('adjacentArray' => $adjacentArray);
echo json_encode($arr);


