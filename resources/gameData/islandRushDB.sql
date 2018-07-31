-- Database file for Island Rush Capstone (created 7/27/2018 by C1C Spencer Adolph)

-- -----------------------------------------------------------------------------
DROP DATABASE IF EXISTS islandRushDB;
CREATE DATABASE islandRushDB;
USE islandRushDB;

SET SQL_SAFE_UPDATES = 0;
-- -----------------------------------------------------------------------------


-- Table of Games
CREATE TABLE IF NOT EXISTS `games`(
	`gameId` int(5) NOT NULL AUTO_INCREMENT,
    `gameSection` varchar(10) NOT NULL,  -- 'M1A', 'T7C'
    `gameInstructor` varchar(50) NOT NULL,  -- "Lastname"
    `gameRedLeader`  varchar(50) NOT NULL, -- "Lastname" (cadet commander)
    `gameBlueLeader`  varchar(50) NOT NULL, -- "Lastname" (cadet commander)
    `gameCurrentTeam`  varchar(5) NOT NULL, -- 'Red' or 'Blue'
    `gameTurn` int(4) NOT NULL, -- 0, 1, 2, 3...
    `gamePhase`  int(1) NOT NULL, --  1 = news, 2 = reinforcements...
    `gameRedRpoints` int(5) NOT NULL,
    `gameBlueRpoints` int(5) NOT NULL,
    `gameRedHybridPoints` int(5) NOT NULL,
    `gameBlueHybridpoints` int(5) NOT NULL,
    `gameRedJoined` int(1) NOT NULL, -- 0 or 1 (1 = joined)
    `gameBlueJoined` int(1) NOT NULL,
    `gameBattleSection` varchar(20) NOT NULL,  -- "none" (no popup), "attack", "counter", "asktorepeat"......"selecting pos", "selecting pieces"?
    `gameBattleSubSection` varchar(20) NOT NULL, -- "choosing_pieces", "attacked_popup", "defense_popup"
    `gameBattleLastRoll` int(1) NOT NULL, -- 0 for default (or no roll to display anymore/reset), 1-6 for roll
    `gameBattleLastMessage` varchar(50), -- used for explaining what happened "red killed blue's fighter with fighter" ex...
    PRIMARY KEY(`gameId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
-- Insert games into the database
INSERT INTO `games` VALUES (1, 'M1A', 'Darcy', 'Jacobs', 'Brown', 'Red', 0, 1, 0, 0, 0, 0, 0, 0, 'none', 'choosing_pieces', 0, 'test message');
INSERT INTO `games` VALUES (2, 'T1A', 'Adolph', 'Jacobs', 'Brown', 'Red', 0, 1, 0, 0, 0, 0, 0, 0, 'none', 'choosing_pieces', 0, 'test message');


-- Table of Units (static)
CREATE TABLE IF NOT EXISTS `units`(
	`unitId` int(5) NOT NULL ,
    `unitName` varchar(20) NOT NULL,
    `unitTerrain` varchar(20) NOT NULL,
    `unitMoves` int(3) NOT NULL,
    PRIMARY KEY(`unitId`)
);
INSERT INTO `units` VALUES (0, 'transport', 'water', 5);
INSERT INTO `units` VALUES (1, 'submarine', 'water', 5);
INSERT INTO `units` VALUES (2, 'destroyer', 'water', 5);
INSERT INTO `units` VALUES (3, 'aircraftCarrier', 'water', 5);
INSERT INTO `units` VALUES (4, 'soldier', 'ground', 5);
INSERT INTO `units` VALUES (5, 'artillery', 'ground', 5);
INSERT INTO `units` VALUES (6, 'tank', 'ground', 5);
INSERT INTO `units` VALUES (7, 'marine', 'ground', 5);
INSERT INTO `units` VALUES (8, 'lav', 'ground', 5);
INSERT INTO `units` VALUES (9, 'attackHeli', 'air', 5);
INSERT INTO `units` VALUES (10, 'sam', 'ground', 5);
INSERT INTO `units` VALUES (11, 'fighter', 'air', 5);
INSERT INTO `units` VALUES (12, 'bomber', 'air', 5);
INSERT INTO `units` VALUES (13, 'stealthBomber', 'air', 5);
INSERT INTO `units` VALUES (14, 'tanker', 'air', 5);


-- Table of game pieces and where they are in each game
CREATE TABLE IF NOT EXISTS `placements`(
	`placementId` int(16) NOT NULL AUTO_INCREMENT,
    `placementGameId` int(5) NOT NULL,
    `placementUnitId` int(5) NOT NULL,
    `placementTeamId` varchar(10) NOT NULL,  -- "Red" or "Blue"
	`placementContainerId` int(16) NOT NULL,  -- placementId of the container its in (999999 used instead of null)
    `placementCurrentMoves` int(3) NOT NULL,
    `placementPositionId` int(4) NOT NULL,  -- references what spot its in on the board (map is available in resources / gameInfo)
    `placementBattleUsed` int(1) NOT NULL, -- 0 for not yet used, 1 for used
    PRIMARY KEY(`placementId`),
    FOREIGN KEY (placementUnitId) REFERENCES units(unitId),
    FOREIGN KEY (placementGameId) REFERENCES games(gameId)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- Table of Movements
CREATE TABLE IF NOT EXISTS `movements`(
	`movementId` int(16) NOT NULL AUTO_INCREMENT,
    `movementGameId` int(5) NOT NULL,
    `movementTurn` int(5) NOT NULL,  -- need what phase/turn movement was made (only undo current phase/turn)
    `movementPhase` varchar(20) NOT NULL,
    `movementFromPosition` int(4) NOT NULL,
    `movementFromContainer` int(16),
    `movementNowPlacement` int(16) NOT NULL,  -- placement contains current position/container
    `movementCost` int(3) NOT NULL,  -- cost of moves
    PRIMARY KEY(`movementId`),
    FOREIGN KEY (movementGameId) REFERENCES games(gameId),
    FOREIGN KEY (movementNowPlacement) REFERENCES placements(placementId)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- Table of pieces involved in battles (duplicate pieces with battle only info)
CREATE TABLE IF NOT EXISTS `battlePieces`(
	`battlePieceId` int(5) NOT NULL AUTO_INCREMENT,  -- piece must already exist, this refers to the placementId
    `battleGameId` int(5) NOT NULL,
	`battlePieceState` int(4) NOT NULL,  -- "unused_attacker" (0), "used_defender", "selected..." (in battle center), "destroyed?" (this maybe not used, piece will be deleted here and also from real board)
    `battlePieceWasHit` varchar(20) NOT NULL, -- false or true
    PRIMARY KEY(`battlePieceId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;





UPDATE games SET gameBlueJoined=1 WHERE gameId = 1;

-- SELECT * FROM games;




SELECT * FROM placements;

SELECT * FROM movements;

SELECT * FROM games;

SELECT * FROM battlePieces;




