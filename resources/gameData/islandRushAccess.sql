-- SQL Script for Island Rush Database Manipulation (created 10/14/2018 by C1C Spencer Adolph)

-- -----------------------------------------------------------------------------

USE islandRushDB;




SELECT * FROM games;

SELECT * FROM units;

SELECT * FROM placements;

SELECT * FROM movements;

SELECT * FROM battlePieces;

SELECT * FROM updates order by updateId DESC;

SELECT * FROM newsAlerts;

-- UPDATE games SET gameRedJoined = 0 WHERE gameId = 1;

INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (1, 0, 'Red', 'logout');