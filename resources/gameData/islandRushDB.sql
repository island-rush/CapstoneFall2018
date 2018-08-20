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
    `gameBattleSection` varchar(20) NOT NULL,  -- "none" (no popup), "attack", "counter", "askRepeat"......"selectPos", "selectPieces"?
    `gameBattleSubSection` varchar(20) NOT NULL, -- "choosing_pieces", "attacked_popup", "defense_popup"
    `gameBattleLastRoll` int(1) NOT NULL, -- 0 for default (or no roll to display anymore/reset), 1-6 for roll
    `gameBattleLastMessage` varchar(50), -- used for explaining what happened "red killed blue's fighter with fighter" ex...
    `gameBattlePosSelected` int(4) NOT NULL, -- positionId chosen by attacker (999999 default)
    PRIMARY KEY(`gameId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
-- Insert games into the database
INSERT INTO `games` VALUES (1, 'M1A', 'Adolph', 'Jacobs', 'Brown', 'Red', 0, 1, 0, 0, 0, 0, 0, 0, 'none', 'choosing_pieces', 0, 'test message', 999999);
INSERT INTO `games` VALUES (2, 'T1A', 'Kulp', 'Jacobs', 'Brown', 'Red', 0, 1, 0, 0, 0, 0, 0, 0, 'none', 'choosing_pieces', 0, 'test message', 999999);


-- Table of Units (static)
CREATE TABLE IF NOT EXISTS `units`(
	`unitId` int(5) NOT NULL ,
    `unitName` varchar(20) NOT NULL,
    `unitTerrain` varchar(20) NOT NULL,
    `unitMoves` int(3) NOT NULL,
    PRIMARY KEY(`unitId`)
);
INSERT INTO `units` VALUES (0, 'transport', 'water', 2);
INSERT INTO `units` VALUES (1, 'submarine', 'water', 2);
INSERT INTO `units` VALUES (2, 'destroyer', 'water', 2);
INSERT INTO `units` VALUES (3, 'aircraftCarrier', 'water', 2);
INSERT INTO `units` VALUES (4, 'soldier', 'ground', 1);
INSERT INTO `units` VALUES (5, 'artillery', 'ground', 1);
INSERT INTO `units` VALUES (6, 'tank', 'ground', 2);
INSERT INTO `units` VALUES (7, 'marine', 'ground', 1);
INSERT INTO `units` VALUES (8, 'lav', 'ground', 2);
INSERT INTO `units` VALUES (9, 'attackHeli', 'air', 3);
INSERT INTO `units` VALUES (10, 'sam', 'ground', 1);
INSERT INTO `units` VALUES (11, 'fighter', 'air', 4);
INSERT INTO `units` VALUES (12, 'bomber', 'air', 6);
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
	`battlePieceId` int(5) NOT NULL,  -- piece must already exist, this refers to the placementId
    `battleGameId` int(5) NOT NULL,
	`battlePieceState` int(4) NOT NULL,  -- "unused_attacker" (0), "used_defender", "selected..." (in battle center), "destroyed?" (this maybe not used, piece will be deleted here and also from real board)
    `battlePieceWasHit` int(1) NOT NULL, -- 0 for false, 1 for true
    PRIMARY KEY(`battlePieceId`)
);

-- Table of board updates to send to other client (piece stuff mostly)
CREATE TABLE IF NOT EXISTS `updates`(  
	`updateId` int(16) NOT NULL AUTO_INCREMENT,    
	`updateGameId` int(5) NOT NULL,     
	`updateValue` int(5) NOT NULL,  -- has the update been processed / changed / null? (0 = not been processed) (1 = processed)  
	`updateTeam` varchar(10),     
	`updateType` varchar(30),     
	`updatePlacementId` int(4) DEFAULT 0,     
	`updateNewPositionId` int(4) DEFAULT 0,     
	`updateNewContainerId` int(4) DEFAULT 0,     
	`updateNewUnitId` int(4) DEFAULT 16,     
	PRIMARY KEY(`updateId`) 
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


INSERT INTO placements VALUES (1, 1, 0, 'Red', 999999, 5, 0, 0);
INSERT INTO placements VALUES (2, 1, 1, 'Red', 999999, 5, 1, 0);
INSERT INTO placements VALUES (3, 1, 2, 'Red', 999999, 5, 2, 0);
INSERT INTO placements VALUES (4, 1, 3, 'Red', 999999, 5, 3, 0);
INSERT INTO placements VALUES (5, 1, 4, 'Red', 999999, 5, 4, 0);
INSERT INTO placements VALUES (6, 1, 5, 'Red', 999999, 5, 5, 0);
INSERT INTO placements VALUES (7, 1, 6, 'Red', 999999, 5, 6, 0);
INSERT INTO placements VALUES (8, 1, 7, 'Red', 999999, 5, 7, 0);
INSERT INTO placements VALUES (9, 1, 8, 'Red', 999999, 5, 8, 0);
INSERT INTO placements VALUES (10, 1, 9, 'Red', 999999, 5, 9, 0);
INSERT INTO placements VALUES (11, 1, 10, 'Red', 999999, 5, 10, 0);
INSERT INTO placements VALUES (12, 1, 11, 'Red', 999999, 5, 11, 0);
INSERT INTO placements VALUES (13, 1, 12, 'Red', 999999, 5, 12, 0);
INSERT INTO placements VALUES (14, 1, 13, 'Red', 999999, 5, 13, 0);
INSERT INTO placements VALUES (15, 1, 14, 'Red', 999999, 5, 14, 0);
INSERT INTO placements VALUES (16, 1, 14, 'Blue', 999999, 5, 15, 0);





SELECT * FROM updates;




-- SELECT * FROM placements;

-- SELECT * FROM movements;

-- SELECT * FROM games;

-- SELECT * FROM battlePieces;

