<?php

include("db.php");

$gameId = $_REQUEST['gameId'];
$gameId = 2;

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
$placementId = 1;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();
$placementId = 2;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $artillery, $red, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();
$placementId = 3;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 56;
$placementId = 4;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $bomber, $red, $noContainerId, $moves[$bomber], $position, $placementBattleUsed);
$query->execute();
$placementId = 5;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tanker, $red, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();
$placementId = 6;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 57;
$placementId = 7;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $stealthBomber, $red, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();
$placementId = 8;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 60;
$placementId = 9;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 61;
$placementId = 10;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 62;
$placementId = 11;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 63;
$placementId = 12;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 64;
$placementId = 13;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 78;
$placementId = 14;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tanker, $red, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();
$placementId = 15;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();
$placementId = 16;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $stealthBomber, $red, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();

$position = 79;
$placementId = 17;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 80;
$placementId = 18;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 81;
$placementId = 19;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $attackHeli, $red, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 82;
$placementId = 20;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 83;
$placementId = 21;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 85;
$placementId = 22;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 87;
$placementId = 23;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $bomber, $red, $noContainerId, $moves[$bomber], $position, $placementBattleUsed);
$query->execute();
$placementId = 24;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tanker, $red, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();

$position = 88;
$placementId = 25;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 89;
$placementId = 26;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $attackHeli, $red, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 97;
$placementId = 27;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $attackHeli, $red, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 98;
$placementId = 28;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 99;
$placementId = 29;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 90;
$placementId = 30;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 91;
$placementId = 31;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $artillery, $red, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();

$position = 92;
$placementId = 32;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $red, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();

$position = 93;
$placementId = 33;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 100;
$placementId = 34;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $marine, $red, $noContainerId, $moves[$marine], $position, $placementBattleUsed);
$query->execute();
$placementId = 35;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $lav, $red, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 101;
$placementId = 36;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $artillery, $red, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();

$position = 102;
$placementId = 37;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 94;
$placementId = 38;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();
$placementId = 39;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 113;
$placementId = 40;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $stealthBomber, $red, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();
$placementId = 41;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tanker, $red, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();

$position = 103;
$placementId = 42;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();
$placementId = 43;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 105;
$placementId = 44;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();
$placementId = 45;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $red, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 116;
$placementId = 46;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 117;
$placementId = 47;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 107;
$placementId = 48;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $red, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();

$position = 110;
$placementId = 49;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $attackHeli, $red, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 65;
$placementId = 50;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$placementId = 51;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$placementId = 52;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $marine, $blue, $noContainerId, $moves[$marine], $position, $placementBattleUsed);
$query->execute();
$placementId = 53;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 66;
$placementId = 54;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tanker, $blue, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();
$placementId = 55;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $bomber, $blue, $noContainerId, $moves[$bomber], $position, $placementBattleUsed);
$query->execute();
$placementId = 56;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $stealthBomber, $blue, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();
$placementId = 57;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $blue, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 67;
$placementId = 58;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $blue, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();
$placementId = 59;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 68;
$placementId = 60;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tanker, $blue, $noContainerId, $moves[$tanker], $position, $placementBattleUsed);
$query->execute();
$placementId = 61;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $bomber, $blue, $noContainerId, $moves[$bomber], $position, $placementBattleUsed);
$query->execute();
$placementId = 62;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $stealthBomber, $blue, $noContainerId, $moves[$stealthBomber], $position, $placementBattleUsed);
$query->execute();
$placementId = 63;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $blue, $noContainerId, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 69;
$placementId = 64;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $attackHeli, $blue, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();

$position = 70;
$placementId = 65;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$placementId = 66;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$placementId = 67;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $blue, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();
$placementId = 68;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();
$placementId = 69;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $lav, $blue, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 71;
$placementId = 70;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $attackHeli, $blue, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();
$placementId = 71;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 72;
$placementId = 72;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $attackHeli, $blue, $noContainerId, $moves[$attackHeli], $position, $placementBattleUsed);
$query->execute();
$placementId = 73;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

$position = 73;
$placementId = 74;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $sam, $blue, $noContainerId, $moves[$sam], $position, $placementBattleUsed);
$query->execute();
$placementId = 75;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $artillery, $blue, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();
$placementId = 76;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $lav, $blue, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();

$position = 74;
$placementId = 77;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $lav, $blue, $noContainerId, $moves[$lav], $position, $placementBattleUsed);
$query->execute();
$placementId = 78;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $tank, $blue, $noContainerId, $moves[$tank], $position, $placementBattleUsed);
$query->execute();
$placementId = 79;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $artillery, $blue, $noContainerId, $moves[$artillery], $position, $placementBattleUsed);
$query->execute();
$placementId = 80;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $soldier, $blue, $noContainerId, $moves[$soldier], $position, $placementBattleUsed);
$query->execute();

//start sea placements
$position = 19;
$placementId = 81;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $transport, $red, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 26;
$placementId = 82;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $aircraftCarrier, $red, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
$container = 82;
$placementId = 83;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 0;
$placementId = 84;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $transport, $red, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 13;
$placementId = 85;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $aircraftCarrier, $red, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
$container = 85;
$placementId = 86;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();
$placementId = 87;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 34;
$placementId = 88;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $transport, $red, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();
$placementId = 89;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $submarine, $red, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 35;
$placementId = 90;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $aircraftCarrier, $red, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
$container = 90;
$placementId = 91;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 41;
$placementId = 92;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $destroyer, $red, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();

$position = 15;
$placementId = 93;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $transport, $red, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 22;
$placementId = 94;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $destroyer, $red, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();

$position = 42;
$placementId = 95;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $submarine, $red, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 50;
$placementId = 96;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $submarine, $red, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 3;
$placementId = 97;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $aircraftCarrier, $red, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
$container = 97;
$placementId = 98;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $red, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 51;
$placementId = 99;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $destroyer, $red, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();

$position = 16;
$placementId = 100;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $destroyer, $red, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();
$placementId = 101;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $submarine, $red, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 53;
$placementId = 102;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $submarine, $blue, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();

$position = 45;
$placementId = 103;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $destroyer, $blue, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();

$position = 12;
$placementId = 102;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $submarine, $blue, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();
$placementId = 103;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $aircraftCarrier, $blue, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
$container = 103;
$placementId = 104;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 18;
$placementId = 105;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $destroyer, $blue, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();
$placementId = 106;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $transport, $blue, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 31;
$placementId = 107;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $destroyer, $blue, $noContainerId, $moves[$destroyer], $position, $placementBattleUsed);
$query->execute();
$placementId = 108;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $transport, $blue, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();
$placementId = 109;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $submarine, $blue, $noContainerId, $moves[$submarine], $position, $placementBattleUsed);
$query->execute();
$placementId = 110;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $aircraftCarrier, $blue, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
$container = 110;
$placementId = 111;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();
$placementId = 112;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();

$position = 38;
$placementId = 113;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $transport, $blue, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();
$placementId = 114;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $transport, $blue, $noContainerId, $moves[$transport], $position, $placementBattleUsed);
$query->execute();

$position = 54;
$placementId = 115;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $aircraftCarrier, $blue, $noContainerId, $moves[$aircraftCarrier], $position, $placementBattleUsed);
$query->execute();
$container = 115;
$placementId = 116;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();
$placementId = 117;
$query = 'INSERT INTO placements (placementId, placementGameId, placementUnitId, placementTeamId, placementContainerId, placementCurrentMoves, placementPositionId, placementBattleUsed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiisiiii", $placementId, $gameId, $fighter, $blue, $container, $moves[$fighter], $position, $placementBattleUsed);
$query->execute();





