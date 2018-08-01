<?php
session_start();

$attackUnitId = $_REQUEST['attackUnitId'];
$defendUnitIt = $_REQUEST['defendUnitId'];

$gameBattleSection = $_REQUEST['gameBattleSection'];
$gameBattleSubSection = $_REQUEST['gameBattleSubSection'];

$lastRoll = rand(1, 6);

if ($gameBattleSubSection == "choosing_pieces") {
    //regular attack
    if ($lastRoll >= $_SESSION['attack'][$attackUnitId][$defendUnitIt] && $_SESSION['attack'][$attackUnitId][$defendUnitIt] != 0) {
        $wasHit = "true";
    } else {
        $wasHit = "false";
    }
} else {
    //defense bonus
    if (($lastRoll >= $_SESSION['attack'][$attackUnitId][$defendUnitIt] && $_SESSION['attack'][$attackUnitId][$defendUnitIt] != 0) || ($_SESSION['attack'][$attackUnitId][$defendUnitIt] == 0 && $lastRoll == 6)) {
        $wasHit = "true";
    } else {
        $wasHit = "false";
    }
}

if ($wasHit == "true" && $gameBattleSection == "attack" && $gameBattleSubSection == "choosing_pieces") {
    $nextThing = "defense_bonus";
} else {
    $nextThing = "continue_choosing";
}


$arr = array('lastRoll' => $lastRoll, 'wasHit' => $wasHit, 'new_gameBattleSubSection' => $nextThing);
echo json_encode($arr);
