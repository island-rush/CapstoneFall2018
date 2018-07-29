<?php
session_start();

$thisMoves = $_REQUEST['thisMoves'];
$thisPos = $_REQUEST['thisPos'];

$n = sizeof($_SESSION['dist'][0]);

$array1 = [];

for ($j = 0; $j < $n; $j++) {
    if ($n != $thisPos) {
        if ($_SESSION['dist'][$thisPos][$j] <= $thisMoves && $_SESSION['dist'][$thisPos][$j] != 0) {
            array_push($array1, $j);
        }
    }
}

echo json_encode($array1);