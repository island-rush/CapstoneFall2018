<?php

include("db.php");

$instructor = $_REQUEST['instructor'];
$section = $_REQUEST['section'];

$query = "SELECT * FROM GAMES WHERE gameInstructor = ? AND gameSection = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("ss", $instructor,$section);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r= $results->fetch_assoc();

$gameId = $r['gameId'];

$query = "DELETE FROM PLACEMENTS where placementGameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();

//teams
$red = "Red";
$blue = "Blue";

// troops
$transport = 0;
$submarine = 1;
$destroyer = 2;
$aircraftCarrier = 3;
$soldier = 4;
$artillery = 5;
$tank = 6;
$marine = 7;
$lav = 8;
$attackHeli = 9;
$sam = 10;
$fighter = 11;
$bomber = 12;
$stealthBomber = 13;
$tanker = 14;

$moves = array(2, 2, 2, 2, 1, 1, 2, 1, 2, 3, 1, 4, 6, 5, 5);
$noContainerId = 999999;
$container = 999999; // overwritten later when its used with airCarriers
$placementBattleUsed = 0;

// start island placements
$position = 55;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $artillery, $red, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 56;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $bomber, $red, $noContainerId, $moves[$bomber], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tanker, $red, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 57;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $stealthBomber, $red, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 60;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 61;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 62;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 63;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 64;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 78;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tanker, $red, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $stealthBomber, $red, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();

$position = 79;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 80;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 81;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $attackHeli, $red, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 82;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 83;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 85;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 87;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $bomber, $red, $noContainerId, $moves[$bomber], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tanker, $red, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();

$position = 88;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 89;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $attackHeli, $red, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 97;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $attackHeli, $red, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 98;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 99;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 90;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 91;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $artillery, $red, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();

$position = 92;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 93;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 100;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $marine, $red, $noContainerId, $moves[$marine], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 101;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $artillery, $red, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();

$position = 102;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 94;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 113;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $stealthBomber, $red, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tanker, $red, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();

$position = 103;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 105;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 116;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 117;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 107;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 110;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $attackHeli, $red, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 65;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $marine, $blue, $noContainerId, $moves[$marine], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 66;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tanker, $blue, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $bomber, $blue, $noContainerId, $moves[$bomber], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $stealthBomber, $blue, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $blue, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 67;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $blue, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 68;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tanker, $blue, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $bomber, $blue, $noContainerId, $moves[$bomber], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $stealthBomber, $blue, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $blue, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 69;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $attackHeli, $blue, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 70;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $blue, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $lav, $blue, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 71;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $attackHeli, $blue, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 72;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $attackHeli, $blue, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 73;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $artillery, $blue, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $lav, $blue, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 74;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $lav, $blue, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $tank, $blue, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $artillery, $blue, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

//start sea placements
$position = 19;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $transport, $red, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 26;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $aircraftCarrier, $red, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
//code to fetch the last placementId so we can use that as the containerId
$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r= $results->fetch_assoc();
$container = $r['LAST_INSERT_ID()'];
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 0;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $transport, $red, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 13;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $aircraftCarrier, $red, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
//code to fetch the last placementId so we can use that as the containerId
$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r= $results->fetch_assoc();
$container = $r['LAST_INSERT_ID()'];
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 34;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $transport, $red, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $submarine, $red, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 35;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $aircraftCarrier, $red, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
//code to fetch the last placementId so we can use that as the containerId
$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r= $results->fetch_assoc();
$container = $r['LAST_INSERT_ID()'];
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 41;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $destroyer, $red, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();

$position = 15;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $transport, $red, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 22;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $destroyer, $red, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();

$position = 42;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $submarine, $red, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 50;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $submarine, $red, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 3;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $aircraftCarrier, $red, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
//code to fetch the last placementId so we can use that as the containerId
$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r= $results->fetch_assoc();
$container = $r['LAST_INSERT_ID()'];
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 51;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $destroyer, $red, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();

$position = 16;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $destroyer, $red, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $submarine, $red, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 53;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $submarine, $blue, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 45;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $destroyer, $blue, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();

$position = 12;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $submarine, $blue, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $aircraftCarrier, $blue, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
//code to fetch the last placementId so we can use that as the containerId
$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r= $results->fetch_assoc();
$container = $r['LAST_INSERT_ID()'];
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 18;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $destroyer, $blue, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $transport, $blue, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 31;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $destroyer, $blue, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $transport, $blue, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $submarine, $blue, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $aircraftCarrier, $blue, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
//code to fetch the last placementId so we can use that as the containerId
$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r= $results->fetch_assoc();
$container = $r['LAST_INSERT_ID()'];
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 38;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $transport, $blue, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $transport, $blue, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 54;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $aircraftCarrier, $blue, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
//code to fetch the last placementId so we can use that as the containerId
$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r= $results->fetch_assoc();
$container = $r['LAST_INSERT_ID()'];
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisiiii", $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

// *********************************************************************************************************************
// INSERTING THE DEFAULT NEWS ALERTS INTO THE SAME GAME

//variables for newsAlert inserts
$allPieces = "{'transport':1, 'submarine':1, 'destroyer':1, 'aircraftCarrier':1, 'soldier':1, 'artillery':1, 'tank':1, 'marine':1, 'lav':1, 'attackHeli':1, 'sam':1, 'fighter':1, 'bomber':1, 'stealthBomber':1, 'tanker':1}";
$manualPieces = "{'transport':0, 'submarine':0, 'destroyer':0, 'aircraftCarrier':0, 'soldier':0, 'artillery':0, 'tank':0, 'marine':0, 'lav':0, 'attackHeli':0, 'sam':0, 'fighter':0, 'bomber':0, 'stealthBomber':0, 'tanker':0}";
$order = 1;
$all = "all";
$zone = 999999; //set before every insert. 0-54 = sea; 101-114 = islands; 200 = all
$true = 1;
$false = 0;
$rollValue = 1; // Default is 1. Not looked at unless effect=rollDie
$disable = "disable";
$rollDie = "rollDie";
$moveDie = "moveDie";
$nothing = "nothing";
$length = 1; //set before every insert but if not inserted, it table defaults to 1
$text = ""; //set before every insert
$effectText = ""; //set before every insert

// Start doing all the inserts for ALL news alerts.
$text = "Canada wins ping pong gold medal during Olympics";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

//next one, and so on
$order = 2;
$text = "International Surf Contest performance plummets as Zmar Island runs out of tequila";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 3;
$rollValue = 5;
$zone =  104; // island 4
$text = "CHAOS AND CALAMITY: Local partisans overthrow the leadership on Shrek Island";
$effectText = "All units on the island must roll a 5 or higher or will be destroyed";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsRollValue, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiiss",$gameId, $order, $all, $allPieces, $rollDie, $rollValue, $zone, $text, $effectText );
$query->execute();

$order = 4;
$text = "International sugar free gummy bear shortage leaves millions constipated";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 5;
$zone = 200; //all
$text = "SCANDAL! Alarming Reports come out of Zuun Air Force HQ";
$effectText = "All Zuun Air assets are grounded for one turn";
$manualPieces = "{'transport':0, 'submarine':0, 'destroyer':0, 'aircraftCarrier':0, 'soldier':0, 'artillery':0, 'tank':0, 'marine':0, 'lav':0, 'attackHeli':0, 'sam':0, 'fighter':1, 'bomber':1, 'stealthBomber':1, 'tanker':1}";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiss",$gameId, $order, $red, $manualPieces, $disable, $zone, $text, $effectText );
$query->execute();

$order = 6;
$zone = 112; //island 12
$text = "BOOM! Local Volcano on Sito Island Erupts";
$effectText = "Humanitarian Option";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText, newsHumanitarian) VALUES(?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssi",$gameId, $order, $nothing, $text, $effectText, $true );
$query->execute();

$order = 7;
$text = "Messy Situation: Yahuda faces paper towel shortage";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 8;
$zone = 106; //island 6
$text = "Ogaden Measles strikes unsuspecting troops";
$effectText = "All Vesterland soldiers and marines on Shor Island have fallen ill and cannot move";
$manualPieces = "{'transport':0, 'submarine':0, 'destroyer':0, 'aircraftCarrier':0, 'soldier':1, 'artillery':0, 'tank':0, 'marine':1, 'lav':0, 'attackHeli':0, 'sam':0, 'fighter':0, 'bomber':0, 'stealthBomber':0, 'tanker':0}";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiss",$gameId, $order, $blue, $manualPieces, $disable, $zone, $text, $effectText );
$query->execute();

$order = 9;
$zone = 200; //all
$text = "Oil tanker sinks! Oil Crisis arises as countries are conserving all resources";
$effectText = "All Naval and Aircraft units are unable to move for the next turn";
$manualPieces = "{'transport':1, 'submarine':1, 'destroyer':1, 'aircraftCarrier':1, 'soldier':0, 'artillery':0, 'tank':0, 'marine':0, 'lav':0, 'attackHeli':0, 'sam':0, 'fighter':1, 'bomber':1, 'stealthBomber':1, 'tanker':1}";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiss",$gameId, $order, $all, $manualPieces, $disable, $zone, $text, $effectText );
$query->execute();

$order = 10;
$text = "Breaking News: The sun came up and the sky is a lovely shade of blue!";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 11;
$text = "Shrek Island runner wins world Marathon";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 12;
$zone = 111;
$text = "Keoni facing destruction and terror after local extremist group strikes. Island closes borders";
$effectText = "Island locked down for one turn. Units cannot move on island. Humanitarian option";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText, newsHumanitarian) VALUES(?,?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssissi",$gameId, $order, $all, $allPieces, $disable, $zone, $text, $effectText, $true );
$query->execute();

$order = 13;
$text = "Temba Pop Star finds long lost father after running into him in grocery store";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 14;
$text = "Kenoi Island opens first Cat Cafe in its capital city";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 15;
$zone = 119;
$text = "BREAKING: Fuller Island has deadly outbreak of the plague. Military assets already on island have been vaccinated and are not in danger";
$effectText = "No units can land on, attack, or sail through Fuller Island waters for one turn. ";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiss",$gameId, $order, $all, $allPieces, $disable, $zone, $text, $effectText );
$query->execute();

$order = 15;
$text = "Bountiful, Beautiful, Bouncing Babies! Keoni island has a surge of births";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 16;
$text = "BREAKING: MASSIVE HURRICANE HITS ISLAND. Isle of Zehain underwater, hundreds stranded";
$effectText = "Humanitarian Option";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText, newsHumanitarian) VALUES(?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssi",$gameId, $order, $nothing, $text, $effectText, $true );
$query->execute();

$order = 17;
$zone = 105;
$text = "IZA END OF THE WORLD: Freak blizzard sweeps through Iza Island. All ports are closed and all land assets are covered in snow";
$effectText = "All forces on Iza Island cannot move or attack for one turn";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiss",$gameId, $order, $all, $allPieces, $disable, $zone, $text, $effectText );
$query->execute();

$order = 18;
$text = "IZA Ink: Youth on Iza Island show increasing interest in tattoos";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 19;
$text = "Dragon Island officially bans all commercial diesel vehicles";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 20;
$rollValue = 2;
$zone =  102;
$text = "Mystery flu sweeps Tenba Island, wreaking havoc";
$effectText = "All units on the island must roll a 2 or higher or will be destroyed";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsRollValue, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiiss",$gameId, $order, $all, $allPieces, $rollDie, $rollValue, $zone, $text, $effectText );
$query->execute();

$order = 21;
$zone = 28; // sea zone F5
$length = 2;
$text = "Zuun Marine biologists discover a new species of coral and convince government to close oceanway until species can be cataloged";
$effectText = "Zone F5 is closed to Zuun naval traffic for two turns";
$manualPieces = "{'transport':1, 'submarine':1, 'destroyer':1, 'aircraftCarrier':1, 'soldier':0, 'artillery':0, 'tank':0, 'marine':0, 'lav':0, 'attackHeli':0, 'sam':0, 'fighter':0, 'bomber':0, 'stealthBomber':0, 'tanker':0}";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText, newsLength) VALUES(?,?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssissi",$gameId, $order, $red, $manualPieces, $disable, $zone, $text, $effectText, $length);
$query->execute();

$order = 22;
$text = "BONE ZONE: The inhabitants of Yehuda find a massive fossil reserve and work to preserve area";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 23;
$text = "Tempukah Island pug proclaimed 'Worlds Happiest Dog' ";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 24;
$rollValue = 3;
$zone =  106;
$text = "Typhoon headed strait for Shor. Ground forces in for a rough ride";
$effectText = "All units on the island must roll a 3 or higher or will be destroyed";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsRollValue, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiiss",$gameId, $order, $all, $allPieces, $rollDie, $rollValue, $zone, $text, $effectText );
$query->execute();

$order = 25;
$text = "Tepukah actor Giles Dallaire dies at age 104";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 26;
$text = "The country of Zuun censors Willie the Booh";
$effectText = "No effect on game play";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsEffect, newsText, newsEffectText) VALUES(?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisss",$gameId, $order, $nothing, $text, $effectText );
$query->execute();

$order = 27;
$zone = 104;
$text = "No, you didnt imagine it, the island actually shook. Shrek Island gets hit by an 8.5 Earthquake";
$effectText = "All ground vehicles have been damaged and are unsuable for one turn. Soliders and Marines are still good to fight";
$manualPieces = "{'transport':0, 'submarine':0, 'destroyer':0, 'aircraftCarrier':0, 'soldier':0, 'artillery':1, 'tank':1, 'marine':0, 'lav':1, 'attackHeli':1, 'sam':1, 'fighter':0, 'bomber':0, 'stealthBomber':0, 'tanker':0}";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText, newsHumanitarian) VALUES(?,?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssissi",$gameId, $order, $all, $manualPieces, $disable, $zone, $text, $effectText, $true );
$query->execute();

$order = 28;
$text = "SCANDAL!!! Alarming reports come out of Vesterland Navy";
$effectText = "All Vesterland (Blue) Naval assets grounded for one turn";
$manualPieces = "{'transport':1, 'submarine':1, 'destroyer':1, 'aircraftCarrier':1, 'soldier':0, 'artillery':0, 'tank':0, 'marine':0, 'lav':0, 'attackHeli':0, 'sam':0, 'fighter':0, 'bomber':0, 'stealthBomber':0, 'tanker':0}";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsText, newsEffectText) VALUES(?,?,?,?,?,?,?,?)';
$query = $db->prepare($query);
$query->bind_param("iisssiss",$gameId, $order, $blue, $manualPieces, $disable, $all, $text, $effectText);
$query->execute();
