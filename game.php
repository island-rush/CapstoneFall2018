<?php
//TODO: make sure can't get here with invalid stuff or simple url typing (check session here)(session exists?)
session_start();
include("db.php");
$gameId = $_SESSION['gameId'];
$query = "SELECT * FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$u = $results->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Island Rush Game V0.23.4 Alpha</title>
    <link rel="stylesheet" type="text/css" href="game.css">
    <script type="text/javascript">
        var phaseNames = ['News', 'Buy Reinforcements', 'Combat', 'Fortify Move', 'Reinforcement Place', 'Hybrid War', 'Round Recap'];
        var unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
        var unitsMoves = <?php $query2 = 'SELECT * FROM units'; $query2 = $db->prepare($query2); $query2->execute(); $results2 = $query2->get_result(); $num_results2 = $results2->num_rows; $arr = array();
            if ($num_results2 > 0) {
                for ($i=0; $i < $num_results2; $i++) {
                    $z= $results2->fetch_assoc();
                    $unitName = $z['unitName'];
                    $unitMoves = $z['unitMoves'];
                    $arr[$unitName] = $unitMoves;
                }
            }
            echo json_encode($arr); ?>;

        var attackMatrix = <?php
            $arr2 = array();
            for ($q = 0; $q < 15; $q++) {
                $arr2[$q] = array();
                for ($w = 0; $w < 15; $w++) {
                    $arr2[$q][$w] = (int) $_SESSION['attack'][$q][$w];
                }
            }
            echo json_encode($arr2);
            ?>;

        var gameId = "<?php echo $_SESSION['gameId']; ?>";
        var gamePhase = "<?php echo $u['gamePhase']; ?>";
        var gameTurn = "<?php echo $u['gameTurn']; ?>";
        var gameCurrentTeam = "<?php echo $u['gameCurrentTeam']; ?>";
        var myTeam = "<?php echo $_SESSION['myTeam']; ?>";

        var gameRedRpoints = <?php echo $u['gameRedRpoints']; ?>;
        var gameBlueRpoints = <?php echo $u['gameBlueRpoints']; ?>;
        var gameRedHpoints = <?php echo $u['gameRedHpoints']; ?>;
        var gameBlueHpoints = <?php echo $u['gameBlueHpoints']; ?>;


        var gameBattlePosSelected = "<?php echo $u['gameBattlePosSelected']; ?>";
        var gameBattleSection = "<?php echo $u['gameBattleSection']; ?>";
        var gameBattleSubSection = "<?php echo $u['gameBattleSubSection']; ?>";
        var gameBattleLastRoll = "<?php echo $u['gameBattleLastRoll']; ?>";
        var gameBattleLastMessage = "<?php echo $u['gameBattleLastMessage']; ?>";
        var gameBattleTurn = <?php echo $u['gameBattleTurn']; ?>;

        var gameBattleAdjacentArray;

        <?php
        if ($u['gameCurrentTeam'] != $_SESSION['myTeam']) {
            //not this team's turn, don't allow anything
            $canMove = "false";
            $canPurchase = "false";
            $canUndo = "false";
            $canNextPhase = "false";
            $canTrash = "false";
            $canAttack = "false";
        } else {
            if ($u['gamePhase'] == 1) {
                //news alerts
                $canMove = "false";
                $canPurchase = "false";
                $canUndo = "false";
                $canNextPhase = "true";
                $canTrash = "false";
                $canAttack = "false";
            } elseif ($u['gamePhase'] == 2) {
                //reinforcement purchase
                $canMove = "true";
                $canPurchase = "true";
                $canUndo = "false";
                $canNextPhase = "true";
                $canTrash = "true";
                $canAttack = "false";
            } elseif ($u['gamePhase'] == 3) {
                //combat
                $canMove = "true";
                $canPurchase = "false";
                $canUndo = "true";
                $canNextPhase = "true";
                $canTrash = "false";
                $canAttack = "true";
            } elseif ($u['gamePhase'] == 4) {
                //fortification movement
                $canMove = "true";
                $canPurchase = "false";
                $canUndo = "true";
                $canNextPhase = "true";
                $canTrash = "false";
                $canAttack = "false";
            } elseif ($u['gamePhase'] == 5) {
                //reinforcement place
                $canMove = "true";
                $canPurchase = "false";
                $canUndo = "true";
                $canNextPhase = "true";
                $canTrash = "false";
                $canAttack = "false";
            } elseif ($u['gamePhase'] == 6) {
                //hybrid warfare
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
            }
        }
        ?>

        var canMove = "<?php echo $canMove; ?>";
        var canPurchase = "<?php echo $canPurchase; ?>";
        var canUndo = "<?php echo $canUndo; ?>";
        var canNextPhase = "<?php echo $canNextPhase; ?>";
        var canTrash = "<?php echo $canTrash; ?>";
        var canAttack = "<?php echo $canAttack; ?>";

        <?php
        $activated = 1;
        $zero = 0;
        $query3 = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength != ? ORDER BY newsOrder DESC";
        $preparedQuery3 = $db->prepare($query3);
        $preparedQuery3->bind_param("iii", $gameId, $activated, $zero);
        $preparedQuery3->execute();
        $results3 = $preparedQuery3->get_result();
        $r3 = $results3->fetch_assoc();
        ?>
        var newsEffectText = "<?php echo $r3['newsEffectText'] ?>";
        var newsText = "<?php echo $r3['newsText'] ?>";
        var newsEffect = "<?php echo $r3['newsEffect'] ?>";
    </script>
    <script src="game.js?v=9"></script>
</head>

<body onload="bodyLoader();">
<div id="whole_game">
    <div id="side_panel" >
        <div id="titlebar">Logged into: <?php echo $u['gameSection']; ?>, <?php echo $u['gameInstructor']; ?> as <?php echo $_SESSION['myTeam']; ?><br>Reinforcement Shop</div>
        <div id="purchase_buttons_container">
            <div class="purchase_square Transport" title="Transport&#013;Cost: 8&#013;Moves: 2" id="Transport" data-unitCost="8" data-unitId="0" data-unitTerrain="water" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square Submarine" title="Submarine&#013;Cost: 8&#013;Moves: 2" id="Submarine" data-unitCost="8" data-unitId="1" data-unitTerrain="water" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square Destroyer" title="Destroyer&#013;Cost: 10&#013;Moves: 2" id="Destroyer" data-unitCost="10" data-unitId="2" data-unitTerrain="water" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square AircraftCarrier" title="AircraftCarrier&#013;Cost: 15&#013;Moves: 2" id="AircraftCarrier" data-unitCost="15" data-unitId="3" data-unitTerrain="water" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square ArmyCompany" title="ArmyCompany&#013;Cost: 4&#013;Moves: 1" id="ArmyCompany" data-unitCost="4" data-unitId="4" data-unitTerrain="land" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square ArtilleryBattery" title="ArtilleryBattery&#013;Cost: 5&#013;Moves: 1" id="ArtilleryBattery" data-unitCost="5" data-unitId="5" data-unitTerrain="land" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square TankPlatoon" title="TankPlatoon&#013;Cost: 6&#013;Moves: 1" id="TankPlatoon" data-unitCost="6" data-unitId="6" data-unitTerrain="land" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square MarinePlatoon" title="MarinePlatoon&#013;Cost: 5&#013;Moves: 1" id="MarinePlatoon" data-unitCost="5" data-unitId="7" data-unitTerrain="land" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square MarineConvoy" title="MarineConvoy&#013;Cost: 8&#013;Moves: 2" id="MarineConvoy" data-unitCost="8" data-unitId="8" data-unitTerrain="land" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square AttackHelo" title="AttackHelo&#013;Cost: 7&#013;Moves: 3" id="AttackHelo" data-unitCost="7" data-unitId="9" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square SAM" title="SAM&#013;Cost: 8&#013;Moves: 1" id="SAM" data-unitCost="8" data-unitId="10" data-unitTerrain="land" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square FighterSquadron" title="FighterSquadron&#013;Cost: 12&#013;Moves: 4" id="FighterSquadron" data-unitCost="12" data-unitId="11" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square BomberSquadron" title="BomberSquadron&#013;Cost: 12&#013;Moves: 6" id="BomberSquadron" data-unitCost="12" data-unitId="12" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square StealthBomberSquadron" title="StealthBomberSquadron&#013;Cost: 15&#013;Moves: 5" id="StealthBomberSquadron" data-unitCost="15" data-unitId="13" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square Tanker" title="Tanker&#013;Cost: 11&#013;Moves: 5" id="Tanker" data-unitCost="11" data-unitId="14" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
            <div class="purchase_square LandBasedSeaMissile" title="LandBasedSeaMissile&#013;Cost: 10" id="LandBasedSeaMissile" data-unitCost="10" data-unitId="15" data-unitTerrain="missile" onclick="piecePurchase(event, this);"></div>
        </div>
        <div id="purchase_seperator">Inventory</div>
        <div id="shopping_things">
            <div id="purchased_container" data-positionType="purchased_container" data-positionId="118" data-positionContainerId="999999"><?php $positionId = 118; include("pieceDisplay.php"); ?></div>
            <div id="trashbox" ondragover="positionDragover(event, this);" ondrop="pieceTrash(event, this);"></div>
        </div>
        <div id="rest_things">
            <div id="phase_indicator">Current Phase = Loading...</div>
            <div id="team_indicators">
                <div id="red_team_indicator">Züün</div>
                <div id="blue_team_indicator">Vestrland</div>
            </div>
            <div id="rPoints_indicators">
                <div id="red_rPoints_indicator">Loading</div>
                <div id="rPoints_label">RP</div>
                <div id="blue_rPoints_indicator">Loading</div>
            </div>
            <div id="hPoints_indicators">
                <div id="red_hPoints_indicator">Loading</div>
                <div id="hPoints_label">HWP</div>
                <div id="blue_hPoints_indicator">Loading</div>
            </div>
            <div id="misc_info_undo">
                <div id="logout_div">
                    <button id="logout_button" onclick="logout();">Logout</button>
                </div>
                <div id="undo_button_div">
                    <button id="undo_button" disabled onclick="pieceMoveUndo();">Undo Movement</button>
                </div>
            </div>
        </div>
        <div id="bottom_panel">
            <div id="battle_button_container">
                <button id="battle_button" disabled>Loading...</button>
            </div>
            <div id="user_feedback_container">
                <div id="user_feedback">User Feedback Loading...</div>
            </div>
            <div id="phase_button_container">
                <button id="phase_button" class="<?php echo 'phase_'.$_SESSION['myTeam']; ?>" disabled onclick="userFeedback('This action will switch to the next phase! (Unrelated to battle).'); setTimeout(function thing() {changePhase();}, 50);">Next Phase</button>
            </div>
        </div>
    </div>

    <div id="game_board" onclick="gameboardClick(event, this);">
        <div id="grid_marker_top"></div>
        <div class="gridblockLeftBig <?php echo $u['gameIsland13']; ?>" title="This island is worth 15 Reinforcement Points" id="special_island13" data-islandNum="13">
            <div class="gridblockTiny" data-positionType="land" id="pos13a" data-positionId="55" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 55; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13b" data-positionId="56" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 56; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13c" data-positionId="57" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 57; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13d" data-positionId="58" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 58; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13e" data-positionId="59" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 59; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13f" data-positionId="60" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 60; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13g" data-positionId="61" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 61; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13h" data-positionId="62" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 62; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13i" data-positionId="63" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 63; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos13j" data-positionId="64" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 64; include("pieceDisplay.php"); ?></div>
        </div>
        <div class="gridblock water" data-positionId="0" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 0; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="1" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 1; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="2" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 2; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="3" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 3; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="4" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 4; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="5" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 5; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="6" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 6; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="7" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 7; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="8" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 8; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island1 <?php echo $u['gameIsland1']; ?>" title="This island is worth 4 Reinforcement Points" id="special_island1" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island1_pop" class="special_island1 special_island3x3 <?php echo $u['gameIsland1']; ?>" data-islandNum="1" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos1a" data-positionId="75" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 75; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos1b" data-positionId="76" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 76; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos1c" data-positionId="77" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 77; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos1d" data-positionId="78" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 78; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="9" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 9; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="10" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 10; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island2 <?php echo $u['gameIsland2']; ?>" title="This island is worth 6 Reinforcement Points" id="special_island2" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island2_pop" class="special_island2 special_island3x3 <?php echo $u['gameIsland2']; ?>" data-islandNum="2" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos2a" data-positionId="79" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 79; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos2b" data-positionId="80" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 80; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos2c" data-positionId="81" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 81; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos2d" data-positionId="82" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 82; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" data-positionType="missile" id="posM1" data-positionId="121" data-positionContainerId="999999" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 121; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="11" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 11; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island3 <?php echo $u['gameIsland3']; ?>" title="This island is worth 4 Reinforcement Points" id="special_island3" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island3_pop" class="special_island3 special_island3x3 <?php echo $u['gameIsland3']; ?>" data-islandNum="3" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos3a" data-positionId="83" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 83; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos3b" data-positionId="84" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 84; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos3c" data-positionId="85" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 85; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="12" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 12; include("pieceDisplay.php"); ?></div>
        <div class="gridblockRightBig <?php echo $u['gameIsland14']; ?>" title="This island is worth 25 Reinforcement Points" id="special_island14" data-islandNum="14">
            <div class="gridblockTiny" data-positionType="land" id="pos14a" data-positionId="65" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 65; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14b" data-positionId="66" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 66; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14c" data-positionId="67" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 67; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14d" data-positionId="68" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 68; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14e" data-positionId="69" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 69; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14f" data-positionId="70" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 70; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14g" data-positionId="71" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 71; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14h" data-positionId="72" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 72; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14i" data-positionId="73" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 73; include("pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" data-positionType="land" id="pos14j" data-positionId="74" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 74; include("pieceDisplay.php"); ?></div>
        </div>
        <div class="gridblock water" data-positionId="13" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 13; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="14" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 14; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="15" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 15; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island4 <?php echo $u['gameIsland4']; ?>" title="This island is worth 3 Reinforcement Points" id="special_island4" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island4_pop" class="special_island4 special_island3x3 <?php echo $u['gameIsland4']; ?>" data-islandNum="4" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos4a" data-positionId="86" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 86; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos4b" data-positionId="87" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 87; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos4c" data-positionId="88" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 88; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos4d" data-positionId="89" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 89; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="16" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 16; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="17" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 17; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="18" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 18; include("pieceDisplay.php"); ?></div>
        <div class="gridblockEmptyLeft"></div>
        <div class="gridblock water" data-positionId="19" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 19; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="20" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 20; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="21" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 21; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island5_1 <?php echo $u['gameIsland5']; ?>" title="This island is worth 8 Reinforcement Points" id="special_island5" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island5_pop" class="special_island5 special_island3x3 <?php echo $u['gameIsland5']; ?>" data-islandNum="5" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos5a" data-positionId="90" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 90; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos5b" data-positionId="91" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 91; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos5c" data-positionId="92" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 92; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos5d" data-positionId="93" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 93; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="22" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 22; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="23" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 23; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="24" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 24; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island6 <?php echo $u['gameIsland6']; ?>" title="This island is worth 7 Reinforcement Points" id="special_island6" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island6_pop" class="special_island6 special_island3x3 <?php echo $u['gameIsland6']; ?>" data-islandNum="6" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos6a" data-positionId="94" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 94; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos6b" data-positionId="95" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 95; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos6c" data-positionId="96" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 96; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" data-positionType="missile" id="posM2" data-positionId="122" data-positionContainerId="999999" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 122; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="25" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 25; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="26" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 26; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island7 <?php echo $u['gameIsland7']; ?>" title="This island is worth 7 Reinforcement Points" id="special_island7" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island7_pop" class="special_island7 special_island3x3 <?php echo $u['gameIsland7']; ?>" data-islandNum="7" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos7a" data-positionId="97" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 97; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos7b" data-positionId="98" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 98; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos7c" data-positionId="99" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 99; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" data-positionType="missile" id="posM3" data-positionId="123" data-positionContainerId="999999" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 123; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="27" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 27; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island5_2 <?php echo $u['gameIsland5']; ?>" title="This island is worth 8 Reinforcement Points" id="special_island5_extra" ondragleave="islandDragleave(event, document.getElementById('special_island5'));" ondragenter="islandDragenter(event, document.getElementById('special_island5'));" onclick="islandClick(event, document.getElementById('special_island5'));">
        </div>
        <div class="gridblock water" data-positionId="28" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 28; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island8 <?php echo $u['gameIsland8']; ?>" title="This island is worth 10 Reinforcement Points" id="special_island8" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island8_pop" class="special_island8 special_island3x3 <?php echo $u['gameIsland8']; ?>" data-islandNum="8" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos8a" data-positionId="100" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 100; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos8b" data-positionId="101" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 101; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos8c" data-positionId="102" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 102; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="29" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 29; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="30" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 30; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="31" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 31; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="32" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 32; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="33" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 33; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="34" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 34; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="35" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 35; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island9 <?php echo $u['gameIsland9']; ?>" title="This island is worth 8 Reinforcement Points" id="special_island9" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island9_pop" class="special_island9 special_island3x3 <?php echo $u['gameIsland9']; ?>" data-islandNum="9" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos9a" data-positionId="103" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 103; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos9b" data-positionId="104" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 104; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos9c" data-positionId="105" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 105; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos9d" data-positionId="106" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 106; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" data-positionType="missile" id="posM4" data-positionId="124" data-positionContainerId="999999" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 124; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="36" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 36; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="37" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 37; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island10 <?php echo $u['gameIsland10']; ?>" title="This island is worth 5 Reinforcement Points" id="special_island10" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island10_pop" class="special_island10 special_island3x3 <?php echo $u['gameIsland10']; ?>" data-islandNum="10" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos10a" data-positionId="107" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 107; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos10b" data-positionId="108" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 108; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos10c" data-positionId="109" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 109; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos10d" data-positionId="110" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 110; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="38" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 38; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="39" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 39; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="40" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 40; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island11 <?php echo $u['gameIsland11']; ?>" title="This island is worth 5 Reinforcement Points" id="special_island11" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island11_pop" class="special_island11 special_island3x3 <?php echo $u['gameIsland11']; ?>" data-islandNum="11" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos11a" data-positionId="111" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 111; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos11b" data-positionId="112" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 112; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos11c" data-positionId="113" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 113; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="41" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 41; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="42" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 42; include("pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island12 <?php echo $u['gameIsland12']; ?>" title="This island is worth 5 Reinforcement Points" id="special_island12" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
            <div id="special_island12_pop" class="special_island12 special_island3x3 <?php echo $u['gameIsland12']; ?>" data-islandNum="12" ondragleave="popupDragleave(event, this);">
                <div class="gridblockTiny" data-positionType="land" id="pos12a" data-positionId="114" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 114; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos12b" data-positionId="115" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 115; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos12c" data-positionId="116" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 116; include("pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" data-positionType="land" id="pos12d" data-positionId="117" data-positionContainerId="999999" ondblclick="landdblclick(event, this);" onclick="landClick(event, this);" ondragleave="landDragLeave(event, this);" ondragenter="popupDragEnter(event, this);" ondragover="popupDragOver(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 117; include("pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="43" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 43; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="44" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 44; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="45" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 45; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="46" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 46; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="47" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 47; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="48" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 48; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="49" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 49; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="50" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 50; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="51" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 51; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="52" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 52; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="53" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 53; include("pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="54" data-positionContainerId="999999" data-positionType="water" ondblclick="waterdblclick(event, this);" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 54; include("pieceDisplay.php"); ?></div>
        <div id="battleZonePopup">
            <div id="unused_attacker" data-boxId="1"><?php $boxId = 1; include("battlePieceDisplay.php"); ?></div>
            <div id="unused_defender" data-boxId="2"><?php $boxId = 2; include("battlePieceDisplay.php"); ?></div>
            <div id="used_attacker" data-boxId="3"><?php $boxId = 3; include("battlePieceDisplay.php"); ?></div>
            <div id="used_defender" data-boxId="4"><?php $boxId = 4; include("battlePieceDisplay.php"); ?></div>
            <div id="center_attacker" data-boxId="5"><?php $boxId = 5; include("battlePieceDisplay.php"); ?></div>
            <div id="center_defender" data-boxId="6"><?php $boxId = 6; include("battlePieceDisplay.php"); ?></div>
            <div id="battle_outcome"></div>
            <div id="battle_buttons">
                <button id="attackButton" disabled>Loading...</button>
                <button id="changeSectionButton" disabled>Loading...</button>
            </div>
            <div id="battleActionPopup">
                <div id="battleActionPopupContainer">
                    <div id="dice_image_container">
                        <div id="dice_image1" class="dice_image"></div>
                        <div id="dice_image2" class="dice_image"></div>
                        <div id="dice_image3" class="dice_image"></div>
                        <div id="dice_image4" class="dice_image"></div>
                        <div id="dice_image5" class="dice_image"></div>
                        <div id="dice_image6" class="dice_image"></div>
                    </div>
                    <div id="lastBattleMessage">Loading...</div>
                    <button id="actionPopupButton" onclick="battleEndRoll();">Loading...</button>
                </div>
            </div>
        </div>
        <div id="popup">
            <div id="popupTitle">Loading Title...</div>
            <div id="popupBodyNews">
                <div id="newsBodyText">loading text...</div>
                <div id="newsBodySubText">loading subtext...</div>
            </div>
            <div id="popupBodyHybridMenu">
                <div id="hybridInstructions">
                    <p>Instructions:<br>Select which Hybrid Warfare Option you would like to use. Mouse over the name for more information about what each option does.</p>
                </div>
                <div id="hybridTable">
                    <table>
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Name</th>
                                <th>Cost</th>
                                <th>Choose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2">Cyber</td>
                                <td title="A Cyber attack causes an enemy airfield to be completely shutdown. &#013;Aircraft may not leave or enter that airfield during the enemy turn.">Air Traffic Control Scramble</td>
                                <td>3</td>
                                <td><button id="hybridAirfieldShutdown" onclick="hybridDisableAirfield();">Choose</button></td>
                            </tr>
                            <tr>
                                <td title="Enemy island value counts towards your points for the next two turns. &#013;Enemy team does not earn any points from this island.">Bank Drain</td>
                                <td>4</td>
                                <td><button id="hybridBankDrain" onclick="hybridBank();">Choose</button></td>
                            </tr>
                            <tr>
                                <td rowspan="2">Space</td>
                                <td title="Satellite technology has discovered how to temporarily shorten all &#013;logisical routes. For one turn, all your units get +1 moves.">Advanced Remote Sensing</td>
                                <td>8</td>
                                <td><button id="hybridAddMove" onclick="hybridAddMove();">Choose</button></td>
                            </tr>
                            <tr>
                                <td title="Satellite technology allows for kinetic effects from space! &#013;Instantly destroy a unit on the board. &#013;(destroying a container destroys everything inside of it)">Rods from God</td>
                                <td>6</td>
                                <td><button id="hybridDeletePiece" onclick="hybridDeletePiece();">Choose</button></td>
                            </tr>
                            <tr>
                                <td rowspan="2" title="Using a nuclear option makes a team unable to use Humanitarian options for 3 turns">Nuclear*</td>
                                <td title="A high altitude ICBM detonation produces an electromagnetic pulse &#013;over all enemy aircraft, disabling them for their next turn.">Goldeneye</td>
                                <td>10</td>
                                <td><button id="hybridAircraftDisable" onclick="hybridDisableAircraft();">Choose</button></td>
                            </tr>
                            <tr>
                                <td title="An ICBM ground burst strike destroys a non-capital island. All units on island &#013;and adjacent sea zones are destroyed. The island will not be used for &#013;the rest of the game and does not contribute to points.">Nuclear Strike</td>
                                <td>12</td>
                                <td><button id="hybridNukeIsland" onclick="hybridNuke();">Choose</button></td>
                            </tr>
                            <tr>
                                <td>Humanitarian</td>
                                <td title="When a News alert notifies a team about a catastrophe in an area, &#013;teams have the option to provide humanitarian aid to that nation. &#013;Spend 3 HW points and receive 10 Reinforcement Points.">Humanitarian Option</td>
                                <td>3</td>
                                <td><button id="hybridHumanitarian" onclick="hybridHumanitary();">Choose</button></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
                <br>
                <button id="popupHybridClose" onclick="document.getElementById('popup').style.display = 'none';" style="margin:0 auto; width:30%;">Close this popup</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>