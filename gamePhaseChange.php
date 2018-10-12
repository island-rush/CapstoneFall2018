<?php
session_start();  //needed to update session variables
include("db.php");
$gameId = $_SESSION['gameId'];
$query = "SELECT * FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r= $results->fetch_assoc();


$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameTurn = $r['gameTurn'];

$gameRedRpoints = $r['gameRedRpoints'];
$gameBlueRpoints = $r['gameBlueRpoints'];
$gameRedHpoints = $r['gameRedHpoints'];
$gameBlueHpoints = $r['gameBlueHpoints'];

$newsText = "Default Text";
$newsEffectText = "Default Effect Text";
$newsEffect = "Default Effect";

$phaseText = "";

$myTeam = $_SESSION['myTeam'];

$new_gamePhase = ($gamePhase % 7) + 1;
$new_gameTurn = $gameTurn + 1;

if ($new_gamePhase == 1) {
    if ($gameCurrentTeam == "Red") {
        $new_gameCurrentTeam = "Blue";
    } else {
        $new_gameCurrentTeam = "Red";
    }
} else {
    $new_gameCurrentTeam = $gameCurrentTeam;
}

$nowActivated = 1;

$query = 'UPDATE games SET gamePhase = ?, gameTurn = ?, gameCurrentTeam = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("iisi", $new_gamePhase, $new_gameTurn, $new_gameCurrentTeam, $gameId);
$query->execute();

//.....etc for as many needed for news table / updating process

if ($new_gameCurrentTeam != $_SESSION['myTeam']) {
    //not this team's turn, don't allow anything
    $canMove = "false";
    $canPurchase = "false";
    $canUndo = "false";
    $canNextPhase = "false";
    $canTrash = "false";
    $canAttack = "false";

    //copy this code from below (or fix the if statements to make better (not as important to be efficient here))
    if ($new_gamePhase == 1) {
        $zero = 0;
        $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength != ? ORDER BY newsOrder";
        $preparedQuery4 = $db->prepare($query4);
        $preparedQuery4->bind_param("iii", $gameId, $zero, $zero);
        $preparedQuery4->execute();
        $results4 = $preparedQuery4->get_result();
        $r4= $results4->fetch_assoc();

        $newsId = $r4['newsId'];
        $newsEffect = $r4['newsEffect'];
        $newsText = $r4['newsText'];
        $newsEffectText = $r4['newsEffectText'];

        //decrement -1 for all activated length != 0
        $decrementValue = 1;
        $query = 'UPDATE newsAlerts SET newsLength = newsLength - ? WHERE (newsGameId = ?) AND (newsActivated = ?) AND (newsLength != ?)';
        $query = $db->prepare($query);
        $query->bind_param("iiii", $decrementValue, $gameId, $nowActivated, $zero);
        $query->execute();

        //activate this newsalert
        $query = 'UPDATE newsAlerts SET newsActivated = ? WHERE (newsId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("ii", $nowActivated, $newsId);
        $query->execute();


        //check to see if roll or die, if so run logic for deleting pieces
        if ($newsEffect == "rollDie") {
            $zone = $r4['newsZone'];
            $rollValueNeeded = $r4['newsRollValue'];  //2-6? (1 doesn't make sense)

            if ($zone >= 100) {  //assume never 200 or something for all islands
                $islandNum = $zone - 100;  //island zones are 'island4' = 104
//                $thisIslandSpots = [];
                //figure out which actual positions correspond to the island
                $islandSpots = [[75, 76, 77, 78], [79, 80, 81, 82], [83, 84, 85], [86, 87, 88, 89], [90, 91, 92, 93], [94, 95, 96], [97, 98, 99], [100, 101, 102], [103, 104, 105, 106], [107, 108, 109, 110], [111, 112, 113], [114, 115, 116, 117], [55, 56, 57, 58, 59, 60, 61, 62, 63, 64], [65, 66, 67, 68, 69, 70, 71, 72, 73, 74]];
                $thisIslandSpots = $islandSpots[$islandNum-1];
            } else {
                $thisIslandSpots = [99999999];
                $thisIslandSpots[0] = $zone;
            }

            $teamEffected = $r4['newsTeam'];  //Red, Blue, All



            //for each of these spots, loop through pieces in them, for the specific team listed (could be both), and do random roll to remove
            //if remove, delete the piece (and pieces inside if container?), and send updates to BOTH teams to do html updates
            for ($x = 0; $x < sizeof($thisIslandSpots); $x++) {
                if ($teamEffected == "All") {
                    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementPositionId = ?) AND (placementUnitId = unitId)';
                    $query = $db->prepare($query);
                    $query->bind_param("ii", $gameId, $thisIslandSpots[$x]);
                } else {
                    $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementPositionId = ?) AND (placementTeamId = ?) AND (placementUnitId = unitId)';
                    $query = $db->prepare($query);
                    $query->bind_param("iis", $gameId, $thisIslandSpots[$x], $teamEffected);
                }

                $query->execute();
                $results = $query->get_result();
                $num_results = $results->num_rows;

                if ($num_results > 0) {
                    for ($i=0; $i < $num_results; $i++) {
                        $r5= $results->fetch_assoc();
                        $placementId = $r5['placementId'];

                        $RandRoll = rand(1, 6);
                        if ($RandRoll < $rollValueNeeded) {
                            //add to phase string
                            $unitName = $r5['unitName'];
                            $teamId = $r5['placementTeamId'];

                            $phaseText = $phaseText.$teamId."'s ".$unitName." was destroyed. ";


                            //delete the real piece from database
                            $query = 'DELETE FROM placements WHERE placementId = ?';
                            $query = $db->prepare($query);
                            $query->bind_param("i", $placementId);
                            $query->execute();

                            //delete the stuff within the container
                            $query = 'DELETE FROM placements WHERE placementContainerId = ?';
                            $query = $db->prepare($query);
                            $query->bind_param("i", $placementId);
                            $query->execute();

                            //Tell other client about deletion
                            $red = "Red";
                            $blue = "Blue";
                            $newValue = 0;
                            $updateType = "rollDie";

                            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                            $query = $db->prepare($query);
                            $query->bind_param("iissi", $gameId, $newValue, $red, $updateType, $placementId);
                            $query->execute();

                            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                            $query = $db->prepare($query);
                            $query->bind_param("iissi", $gameId, $newValue, $blue, $updateType, $placementId);
                            $query->execute();

                            $Spec = "Spec";
                            $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                            $query = $db->prepare($query);
                            $query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $placementId);
                            $query->execute();
                        }
                    }
                }
            }
        }

        $moveEffect = "addMove";
        $one = 1;
        $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength = ? AND newsTeam = ? AND newsEffect = ? ORDER BY newsOrder";
        $preparedQuery4 = $db->prepare($query4);
        $preparedQuery4->bind_param("iiiss", $gameId, $one, $one, $new_gameCurrentTeam, $moveEffect);
        $preparedQuery4->execute();
        $results = $preparedQuery4->get_result();
        $num_results = $results->num_rows;

        if ($num_results == 1) {
//            $r9 = $results->fetch_assoc();
            //there was a newsalert to add moves
            $query4 = "UPDATE placements SET placementCurrentMoves = placementCurrentMoves + ? WHERE placementGameId = ? AND placementTeamId = ?";
            $preparedQuery4 = $db->prepare($query4);
            $preparedQuery4->bind_param("iis", $one, $gameId, $new_gameCurrentTeam);
            $preparedQuery4->execute();


            $query2 = 'SELECT * FROM placements WHERE placementTeamId = ? AND placementGameId = ?';
            $query2 = $db->prepare($query2);
            $query2->bind_param("i", $placementId);
            $query2->execute();
            $results = $query2->get_result();
            $num_results = $results->num_rows;

            if ($num_results > 0) {
                for ($i = 0; $i < $num_results; $i++) {
                    $r33 = $results->fetch_assoc();
                    $placementId = $r33['placementId'];
                    $newMoves = $r33['placementCurrentMoves'];

                    //Tell other client(s) about moveupdate
                    $newValue = 0;
                    $Red = "Red";
                    $Blue = "Blue";
                    $Spec = "Spec";
                    $updateType = "updateMoves";

                    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("iissii", $gameId, $newValue, $Red, $updateType, $placementId, $newMoves);
                    $query->execute();

                    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("iissii", $gameId, $newValue, $Blue, $updateType, $placementId, $newMoves);
                    $query->execute();

                    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("iissii", $gameId, $newValue, $Spec, $updateType, $placementId, $newMoves);
                    $query->execute();
                }
            }
        }

    }
} else {
    if ($new_gamePhase == 1) {
        //TODO: this code potentially never executes (i never as the client phasechange into my own newsalert)
        //news alert
        $canMove = "false";
        $canPurchase = "false";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";


        $zero = 0;
        $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? ORDER BY newsOrder";
        $preparedQuery4 = $db->prepare($query4);
        $preparedQuery4->bind_param("ii", $gameId, $zero);
        $preparedQuery4->execute();
        $results4 = $preparedQuery4->get_result();
        $r4= $results4->fetch_assoc();

        $newsId = $r4['newsId'];
        $newsEffect = $r4['newsEffect'];
        $newsText = $r4['newsText'];
        $newsEffectText = $r4['newsEffectText'];

        //decrement -1 for all activated length != 0
        $decrementValue = 1;
        $query = 'UPDATE newsAlerts SET newsLength = newsLength - ? WHERE (newsGameId = ?) AND (newsActivated = ?) AND (newsLength != ?)';
        $query = $db->prepare($query);
        $query->bind_param("iiii", $decrementValue, $gameId, $nowActivated, $zero);
        $query->execute();

        //activate this newsalert
        $query = 'UPDATE newsAlerts SET newsActivated = ? WHERE (newsId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("ii", $nowActivated, $newsId);
        $query->execute();


        //add moves to the pieces if the news alert is active for this team




    } elseif ($new_gamePhase == 2) {
        $bankAdd = "bankAdd";
        $one = 1;
        $zero = 0;

        //TODO: change this turn into 15 so no one gets points until red's first turn (this may come early if lots of battles???)
        if ($new_gameTurn > 14) {
            $addPoints = 0;

            //for each island ownership, know how many points each is worth and add to the current team's reinforcement points
            if ($r['gameIsland1'] == $gameCurrentTeam) {
                $addPoints += 4;
                $zone = 101;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $gameCurrentTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 4;
                }
            } else {
                $zone = 101;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $gameCurrentTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 4;
                }
            }
            if ($r['gameIsland2'] == $gameCurrentTeam) {
                $addPoints += 6;
                $zone = 102;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 6;
                }
            } else {
                $zone = 102;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 6;
                }
            }
            if ($r['gameIsland3'] == $gameCurrentTeam) {
                $addPoints += 4;
                $zone = 103;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 4;
                }
            } else {
                $zone = 103;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 4;
                }
            }
            if ($r['gameIsland4'] == $gameCurrentTeam) {
                $addPoints += 3;
                $zone = 104;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 3;
                }
            } else {
                $zone = 104;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 3;
                }
            }
            if ($r['gameIsland5'] == $gameCurrentTeam) {
                $addPoints += 8;
                $zone = 105;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 8;
                }
            } else {
                $zone = 105;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 8;
                }
            }
            if ($r['gameIsland6'] == $gameCurrentTeam) {
                $addPoints += 7;
                $zone = 106;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 7;
                }
            } else {
                $zone = 106;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 7;
                }
            }
            if ($r['gameIsland7'] == $gameCurrentTeam) {
                $addPoints += 7;
                $zone = 107;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 7;
                }
            } else {
                $zone = 107;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 7;
                }
            }
            if ($r['gameIsland8'] == $gameCurrentTeam) {
                $addPoints += 10;
                $zone = 108;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 10;
                }
            } else {
                $zone = 108;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 10;
                }
            }
            if ($r['gameIsland9'] == $gameCurrentTeam) {
                $addPoints += 8;
                $zone = 109;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 8;
                }
            } else {
                $zone = 109;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 8;
                }
            }
            if ($r['gameIsland10'] == $gameCurrentTeam) {
                $addPoints += 5;
                $zone = 110;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 5;
                }
            } else {
                $zone = 110;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 5;
                }
            }
            if ($r['gameIsland11'] == $gameCurrentTeam) {
                $addPoints += 5;
                $zone = 111;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 5;
                }
            } else {
                $zone = 111;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 5;
                }
            }
            if ($r['gameIsland12'] == $gameCurrentTeam) {
                $addPoints += 5;
                $zone = 112;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints -= 5;
                }
            } else {
                $zone = 112;
                $query4 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength > ? AND newsTeam = ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                $preparedQuery4 = $db->prepare($query4);
                $preparedQuery4->bind_param("iiissi", $gameId, $one, $zero, $myTeam, $bankAdd, $zone);
                $preparedQuery4->execute();
                $results = $preparedQuery4->get_result();
                $num_results = $results->num_rows;
                if ($num_results == 1) {
                    $addPoints += 5;
                }
            }
            //don't do news alert cause couldn't select?!?!?!?!
            if ($r['gameIsland13'] == $gameCurrentTeam) {
                $addPoints += 15;
            }
            if ($r['gameIsland14'] == $gameCurrentTeam) {
                $addPoints += 25;
            }

            if ($gameCurrentTeam == "Red") {
                $gameRedRpoints += $addPoints;
            } else {
                $gameBlueRpoints += $addPoints;
            }

            //update games table with new rpoints
            $query = 'UPDATE games SET gameRedRpoints = ?, gameBlueRpoints = ? WHERE (gameId = ?)';
            $query = $db->prepare($query);
            $query->bind_param("iii", $gameRedRpoints, $gameBlueRpoints, $gameId);
            $query->execute();
        }


        //reinforcement purchase
        $canMove = "true";
        $canPurchase = "true";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "true";
        $canAttack = "false";
    } elseif ($new_gamePhase == 3) {
        //combat
        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "true";
    } elseif ($new_gamePhase == 4) {
        //fortification movement (+1 moves for aircraft in same position as tanker)

        //loop through all tankers for this team
        //at each of their positions, look for aircraft
        //for each aircraft + moves

        $tanker = "tanker";
        $bomber = "bomber";
        $fighter = "fighter";
        $stealthBomber = "stealthBomber";
        $query = 'SELECT b.placementId, b.unitName, b.placementCurrentMoves FROM (SELECT * FROM placements NATURAL JOIN units WHERE (placementUnitId = unitId)) a Join (SELECT * FROM placements NATURAL JOIN units WHERE (placementUnitId = unitId)) b USING(placementPositionId) WHERE (a.placementTeamId = ?) AND (b.placementTeamId = ?) AND (a.placementGameId = ?) AND (b.placementGameId = ?) AND (a.placementPositionId = b.placementPositionId) AND (a.unitName = ?) AND (b.unitName = ? OR b.unitName = ? OR b.unitName = ?)';
        $query = $db->prepare($query);
        $query->bind_param("ssiissss", $myTeam,$myTeam, $gameId, $gameId, $tanker, $bomber, $fighter, $stealthBomber);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        if ($num_results > 0) {
            for ($i = 0; $i < $num_results; $i++) {
                $r6 = $results->fetch_assoc();
                $placementId = $r6['placementId'];
                $unitName = $r6['unitName'];

                $updateValue = 2;
                if ($unitName == "bomber" || $unitName == "stealthBomber") {
                    $updateValue = 3;
                }

                $query = 'UPDATE placements SET placementCurrentMoves = placementCurrentMoves + ? WHERE (placementId = ?)';
                $query = $db->prepare($query);
                $query->bind_param("ii", $updateValue, $placementId);
                $query->execute();

                $query2 = 'SELECT * FROM placements WHERE placementId = ?';
                $query2 = $db->prepare($query2);
                $query2->bind_param("i", $placementId);
                $query2->execute();
                $results2 = $query2->get_result();
                $r62 = $results2->fetch_assoc();
                $newMoves = $r62['placementCurrentMoves'];

                //Tell other client(s) about moveupdate
                $newValue = 0;
                $Red = "Red";
                $Blue = "Blue";
                $Spec = "Spec";
                $updateType = "updateMoves";

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissii", $gameId, $newValue, $Red, $updateType, $placementId, $newMoves);
                $query->execute();

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissii", $gameId, $newValue, $Blue, $updateType, $placementId, $newMoves);
                $query->execute();

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId, updateNewMoves) VALUES (?, ?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissii", $gameId, $newValue, $Spec, $updateType, $placementId, $newMoves);
                $query->execute();
            }
        }

        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } elseif ($new_gamePhase == 5) {
        //reinforcement place

        //delete any aircraft that aren't where they need to be
        //fighters in carriers or on airstrips
        //bombers or stealthbombers or tankers on airstrips
        //heli over land

        $airFieldSpots = [56, 57, 78, 83, 89, 113, 66, 68];
        $carrierSpots = [];
        //get carrier positions
        $carrier = "aircraftCarrier";
        $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementUnitId = unitId) AND (unitName = ?) AND (placementTeamId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("iss", $gameId, $carrier, $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        if ($num_results > 0) {
            for ($i = 0; $i < $num_results; $i++) {
                $r0 = $results->fetch_assoc();
                $thisPosition = $r0['placementId'];
                array_push($carrierSpots, $thisPosition);
            }
        }

        $heli = "attackHeli";
        $tanker = "tanker";
        $bomber = "bomber";
        $fighter = "fighter";
        $stealthBomber = "stealthBomber";
        $purchaseSpot = 118;
        $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementTeamId = ?) AND (placementGameId = ?) AND (placementUnitId = unitId) AND (unitName = ? OR unitName = ? OR unitName = ? OR unitName = ? OR unitName = ?) AND (placementPositionId != ?)';
        $query = $db->prepare($query);
        $query->bind_param("sisssssi", $myTeam, $gameId, $heli, $tanker, $bomber, $fighter, $stealthBomber, $purchaseSpot);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        if ($num_results > 0) {
            for ($i = 0; $i < $num_results; $i++) {
                $r0 = $results->fetch_assoc();
                $placementId = $r0['placementId'];
                $unitName = $r0['unitName'];
                $containerId = $r0['placementContainerId'];
                $positionId = $r0['placementPositionId'];
                $deletePiece = false;
                if ($unitName == "attackHeli") {
                    //over water
                    if ($positionId < 55) {
                        $deletePiece = true;
                    }
                } elseif ($unitName == "fighter") {
                    if (!in_array($containerId, $carrierSpots) && !in_array($positionId, $airFieldSpots)) {
                        $deletePiece = true;
                    }
                } else {
                    //must be tanker, bomber, stealthbomber
                    if (!in_array($positionId, $airFieldSpots)) {
                        $deletePiece = true;
                    }
                }

                if ($deletePiece) {
                    //TODO: other things for losing a piece (subtract points that its worth?)

                    //delete the real piece from database
                    $query = 'DELETE FROM placements WHERE placementId = ?';
                    $query = $db->prepare($query);
                    $query->bind_param("i", $placementId);
                    $query->execute();

                    //Tell other client(s) about deletion
                    $newValue = 0;
                    $Red = "Red";
                    $Blue = "Blue";
                    $updateType = "rollDie";

                    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("iissi", $gameId, $newValue, $Red, $updateType, $placementId);
                    $query->execute();

                    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("iissi", $gameId, $newValue, $Blue, $updateType, $placementId);
                    $query->execute();

                    $Spec = "Spec";
                    $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $placementId);
                    $query->execute();
                }

            }
        }


        $canMove = "true";
        $canPurchase = "false";
        $canUndo = "true";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } elseif ($new_gamePhase == 6) {
        //hybrid warfare

        //delete pieces that were not placed
        $purchaseSpot = 118;
        $query = 'SELECT * FROM placements WHERE (placementPositionId = ?) AND (placementGameId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("ii", $purchaseSpot, $gameId);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        if ($num_results > 0) {
            for ($i = 0; $i < $num_results; $i++) {
                $r1 = $results->fetch_assoc();
                $placementId = $r1['placementId'];

                //delete the real piece from database
                $query = 'DELETE FROM placements WHERE placementId = ?';
                $query = $db->prepare($query);
                $query->bind_param("i", $placementId);
                $query->execute();

                //Tell other client(s) about deletion
                $newValue = 0;
                $Red = "Red";
                $Blue = "Blue";
                $Spec = "Spec";  //spectator solution
                $updateType = "rollDie";

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissi", $gameId, $newValue, $Red, $updateType, $placementId);
                $query->execute();

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissi", $gameId, $newValue, $Blue, $updateType, $placementId);
                $query->execute();

                $query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType, updatePlacementId) VALUES (?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iissi", $gameId, $newValue, $Spec, $updateType, $placementId);
                $query->execute();
            }
        }

        $canMove = "false";
        $canPurchase = "false";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";
    } else {
        //tally points (7)
        $canMove = "false";
        $canPurchase = "false";
        $canUndo = "false";
        $canNextPhase = "true";
        $canTrash = "false";
        $canAttack = "false";

        //This marks the end of this player's turn
        //reset the moves of each piece for the team that just ended their turn / reset battle used
        $query = 'SELECT * FROM placements NATURAL JOIN units WHERE (placementGameId = ?) AND (placementTeamId = ?) AND (unitId = placementUnitId)';
        $query = $db->prepare($query);
        $query->bind_param("is", $gameId, $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;

        for ($x = 0; $x < $num_results; $x++) {
            $r= $results->fetch_assoc();

            $placementId = $r['placementId'];
            $placementMovesReset = $r['unitMoves'];
            $battleUsed = 0;

            $query2 = 'UPDATE placements SET placementBattleUsed = ?, placementCurrentMoves = ? WHERE (placementId = ?)';
            $query2 = $db->prepare($query2);
            $query2->bind_param("iii", $battleUsed, $placementMovesReset, $placementId);
            $query2->execute();
        }
    }
}

$Blue = "Blue";
$Red = "Red";
$newValue = 0;
$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Blue, $updateType);
$query->execute();

$Spec = "Spec";
$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Spec, $updateType);
$query->execute();

$query = 'INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iiss", $gameId, $newValue, $Red, $updateType);
$query->execute();



$arr = array('gamePhase' => (string) $new_gamePhase,
    'gameTurn' => (string) $new_gameTurn,
    'gameCurrentTeam' => (string) $new_gameCurrentTeam,
    'canMove' => (string) $canMove,
    'canPurchase' => (string) $canPurchase,
    'canUndo' => (string) $canUndo,
    'canNextPhase' => (string) $canNextPhase,
    'canTrash' => (string) $canTrash,
    'canAttack' => (string) $canAttack,
    'gameRedRpoints' => (string) $gameRedRpoints,
    'gameBlueRpoints' => (string) $gameBlueRpoints,
    'gameRedHpoints' => (string) $gameRedHpoints,
    'gameBlueHpoints' => (string) $gameBlueHpoints,
    'newsEffect' => (string) $newsEffect,
    'newsText' => (string) $newsText,
    'newsEffectText' => (string) $newsEffectText,
    'phaseText' => (string) $phaseText);
echo json_encode($arr);


$db->close();


