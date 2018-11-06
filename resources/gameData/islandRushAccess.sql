-- SQL Script for Island Rush Database Manipulation (created 10/14/2018 by C1C Spencer Adolph)

-- -----------------------------------------------------------------------------

USE islandRushDB;


UPDATE games SET gameRedRpoints = 1000 WHERE gameId = 1;

SELECT * FROM games WHERE gameId = 1;

SELECT * FROM units;

SELECT * FROM placements ORDER BY placementId DESC;

SELECT * FROM movements;

SELECT * FROM battlePieces;

SELECT * FROM updates order by updateId DESC;

SELECT * FROM newsAlerts;

-- SELECT * FROM updates WHERE (updateGameId = 1) AND (updateId > 137) AND (updateTeam = 'Spec') ORDER BY updateId ASC;

-- UPDATE games SET gameRedJoined = 0 WHERE gameId = 1;

-- INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (1, 0, 'Red', 'logout');

INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (1, 0, 'Red', 'phaseChange');
