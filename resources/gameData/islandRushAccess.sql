-- SQL Script for Island Rush Database Manipulation (created 10/14/2018 by C1C Spencer Adolph)

-- -----------------------------------------------------------------------------

USE islandRushDB;




SELECT * FROM games WHERE gameId = 1;

SELECT * FROM units;

SELECT * FROM placements;

SELECT * FROM movements;

SELECT * FROM battlePieces;

SELECT * FROM updates order by updateId DESC;

SELECT * FROM newsAlerts;

-- UPDATE games SET gameRedJoined = 0 WHERE gameId = 1;

