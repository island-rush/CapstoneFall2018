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
	`gameAdminPassword` varchar(50) NOT NULL,  -- "password"
    `gameRedLeader`  varchar(50) NOT NULL, -- "Lastname" (cadet commander)
    `gameBlueLeader`  varchar(50) NOT NULL, -- "Lastname" (cadet commander)
    `gameCurrentTeam`  varchar(5) NOT NULL, -- 'Red' or 'Blue'
    `gameTurn` int(4) NOT NULL, -- 0, 1, 2, 3...
    `gamePhase`  int(1) NOT NULL, --  1 = news, 2 = reinforcements...
    `gameRedRpoints` int(5) NOT NULL,
    `gameBlueRpoints` int(5) NOT NULL,
    `gameRedHybridPoints` int(5) NOT NULL,
    `gameBlueHybridPoints` int(5) NOT NULL,
    `gameRedJoined` int(1) NOT NULL, -- 0 or 1 (1 = joined)
    `gameBlueJoined` int(1) NOT NULL,
    `gameBattleSection` varchar(20) NOT NULL,  -- "none" (no popup), "attack", "counter", "askRepeat"......"selectPos", "selectPieces"?
    `gameBattleSubSection` varchar(20) NOT NULL, -- "choosing_pieces", "attacked_popup", "defense_popup"
    `gameBattleLastRoll` int(1) NOT NULL, -- 0 for default (or no roll to display anymore/reset), 1-6 for roll
    `gameBattleLastMessage` varchar(50), -- used for explaining what happened "red killed blue's fighter with fighter" ex...
    `gameBattlePosSelected` int(4) NOT NULL, -- positionId chosen by attacker (999999 default)
    `gameIsland1` varchar(10) NOT NULL,
    `gameIsland2` varchar(10) NOT NULL,
    `gameIsland3` varchar(10) NOT NULL,
    `gameIsland4` varchar(10) NOT NULL,
    `gameIsland5` varchar(10) NOT NULL,
    `gameIsland6` varchar(10) NOT NULL,
    `gameIsland7` varchar(10) NOT NULL,
    `gameIsland8` varchar(10) NOT NULL,
    `gameIsland9` varchar(10) NOT NULL,
    `gameIsland10` varchar(10) NOT NULL,
    `gameIsland11` varchar(10) NOT NULL,
    `gameIsland12` varchar(10) NOT NULL,
    `gameIsland13` varchar(10) NOT NULL,
    `gameIsland14` varchar(10) NOT NULL,
    PRIMARY KEY(`gameId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
-- Insert games into the database
INSERT INTO `games` VALUES (1, 'M1A1', 'Adolph', 'password', 'Jacobs', 'Brown', 'Red', 0, 1, 200, 100, 10, 5, 0, 0, 'none', 'choosing_pieces', 0, 'test message', 999999, 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red');
INSERT INTO `games` VALUES (2, 'T1A1', 'Kulp', 'passowrd', 'Jacobs', 'Brown', 'Red', 0, 1, 100, 20, 0, 0, 0, 0, 'none', 'choosing_pieces', 0, 'test message', 999999, 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red', 'Red');


-- Table of Units (static)
CREATE TABLE IF NOT EXISTS `units`(
	`unitId` int(5) NOT NULL ,
    `unitName` varchar(20) NOT NULL,
    `unitTerrain` varchar(20) NOT NULL,
    `unitMoves` int(3) NOT NULL,
    `unitCost` int(3) NOT NULL,
    PRIMARY KEY(`unitId`)
);
INSERT INTO `units` VALUES (0, 'transport', 'water', 2, 8);
INSERT INTO `units` VALUES (1, 'submarine', 'water', 2, 8);
INSERT INTO `units` VALUES (2, 'destroyer', 'water', 2, 10);
INSERT INTO `units` VALUES (3, 'aircraftCarrier', 'water', 2, 15);
INSERT INTO `units` VALUES (4, 'soldier', 'ground', 1, 4);
INSERT INTO `units` VALUES (5, 'artillery', 'ground', 1, 5);
INSERT INTO `units` VALUES (6, 'tank', 'ground', 2, 6);
INSERT INTO `units` VALUES (7, 'marine', 'ground', 1, 5);
INSERT INTO `units` VALUES (8, 'lav', 'ground', 2, 8);
INSERT INTO `units` VALUES (9, 'attackHeli', 'air', 3, 7);
INSERT INTO `units` VALUES (10, 'sam', 'ground', 1, 8);
INSERT INTO `units` VALUES (11, 'fighter', 'air', 4, 12);
INSERT INTO `units` VALUES (12, 'bomber', 'air', 6, 12);
INSERT INTO `units` VALUES (13, 'stealthBomber', 'air', 5, 15);
INSERT INTO `units` VALUES (14, 'tanker', 'air', 5, 11);
INSERT INTO `units` VALUES (15, 'missile', 'missile', 0, 10);


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
    `updateBattlePieceState` int(2) DEFAULT 8,
    `updateBattlePositionSelectedPieces` varchar(16000) DEFAULT 'defaultString',
    `updateBattlePiecesSelected` varchar(16000) DEFAULT 'defaultString',
	PRIMARY KEY(`updateId`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
 
 -- Table of news alerts (not yet fully implemented)
CREATE TABLE IF NOT EXISTS `newsAlerts`(
	`newsId` int(5) NOT NULL AUTO_INCREMENT,
  `newsGameId` int(5) NOT NULL,
  `newsOrder` int(5) NOT NULL,  -- what index is this in the list of news alerts
  `newsTeam` varchar(10) NOT NULL DEFAULT 'nothing', -- 'Red', 'Blue', 'All'. Defaults to 'nothing' for effect=nothing
  `newsPieces` varchar(350) NOT NULL DEFAULT 'nothing', -- "{transport: 0, submarine: 1, destroyer: 0, ...}"  a JSON string. -access with  newsPieces->>'$.tank'. Defaults to 'nothing' for effect=nothing
  `newsEffect` varchar(20) NOT NULL, -- 'disable', 'rollDie', 'moveDie', 'nothing',  ...
  `newsRollValue` varchar(2) NOT NULL DEFAULT 0, -- {1,2,3,4,5,6} default 0 but it isnt looked at unless effect=rollDie
  `newsZone` int(10) NOT NULL DEFAULT 666, -- {0-54, 101-114, 200} for sea zones 0-54, whole island 1-14, or all zones. if effect=nothing, default to 666.
  `newsSurround` int(2) NOT NULL DEFAULT 0, -- 0 or 1, boolean if the sea zones surrounding an island are affected
  `newsLength` int(2) NOT NULL DEFAULT 1, -- 1,2,3 (amount of turns the effect lasts)
  `newsHumanitarian` int(2) NOT NULL DEFAULT 0,
  `newsText` varchar(200) NOT NULL DEFAULT 'default string', -- the message that displays with the alert
  `newsEffectText` varchar(200) NOT NULL DEFAULT 'default string', -- the message about the action of the effect
  `newsActivated` int(2) NOT NULL DEFAULT 0, -- if the news alert has been 'pulled' (activated) yet. defaults to unused=0.
  PRIMARY KEY(`newsId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO newsAlerts VALUES (1, 1, 0, 'All', "{'transport':1, 'submarine':1, 'destroyer':1, 'aircraftCarrier':1, 'soldier':1, 'artillery':1, 'tank':1, 'marine':1, 'lav':1, 'attackHeli':1, 'sam':1, 'fighter':1, 'bomber':1, 'stealthBomber':1, 'tanker':1}", 'rollDie', 5, 104, 0, 1, 0, 'CHAOS AND CALAMITY: Local partisans overthrow the leadership on Shrek Island', 'All units must roll a 5 or higher or will be destroyed.', 0);


-- SELECT * FROM newsAlerts;

-- SELECT * FROM updates;

-- SELECT * FROM placements;

-- SELECT * FROM movements;

SELECT * FROM games;

-- SELECT * FROM battlePieces;

