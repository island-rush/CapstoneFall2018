<?php
session_start();
include("db.php");
$gameId = $_SESSION['gameId'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Island Rush Game</title>
        <link rel="stylesheet" type="text/css" href="game.css">
        <script src="game.js"></script>
        <script type="text/javascript">
            let phaseNames = ['News', 'Buy Reinforcements', 'Combat', 'Fortify Move', 'Reinforcement Place', 'Hybrid War', 'Tally Points'];
            let unitsMoves = <?php $query = 'SELECT * FROM units'; $query = $db->prepare($query); $query->execute(); $results = $query->get_result(); $num_results = $results->num_rows; $arr = array();
                if ($num_results > 0) {
                    for ($i=0; $i < $num_results; $i++) {
                        $r= $results->fetch_assoc();
                        $unitName = $r['unitName'];
                        $unitMoves = $r['unitMoves'];
                        $arr[$unitName] = $unitMoves;
                    }
                }
                echo json_encode($arr); ?>;

            let gameId = "<?php echo $_SESSION['gameId']; ?>";
            let gamePhase = "<?php echo $_SESSION['gamePhase']; ?>";
            let gameTurn = "<?php echo $_SESSION['gameTurn']; ?>";
            let gameCurrentTeam = "<?php echo $_SESSION['gameCurrentTeam']; ?>";
            let myTeam = "<?php echo $_SESSION['myTeam']; ?>";

            let gameBattlePosSelected = "<?php echo $_SESSION['gameBattlePosSelected']; ?>";
            let gameBattleSection = "<?php echo $_SESSION['gameBattleSection']; ?>";
            let gameBattleSubSection = "<?php echo $_SESSION['gameBattleSubSection']; ?>";
            let gameBattleLastRoll = "<?php echo $_SESSION['gameBattleLastRoll']; ?>";
            let gameBattleLastMessage = "<?php echo $_SESSION['gameBattleLastMessage']; ?>";

            let canMove = "<?php echo $_SESSION['canMove']; ?>";
            let canPurchase = "<?php echo $_SESSION['canPurchase']; ?>";
            let canUndo = "<?php echo $_SESSION['canUndo']; ?>";
            let canNextPhase = "<?php echo $_SESSION['canNextPhase']; ?>";
            let canTrash = "<?php echo $_SESSION['canTrash']; ?>";
            let canAttack = "<?php echo $_SESSION['canAttack']; ?>";

            let hoverTimer;
        </script>
    </head>

    <body onload="bodyLoader();">
        <div id="whole_game">
            <div id="side_panel">
                <div id="titlebar">Reinforcements</div>
                <div id="purchase_buttons_container">
                    <div class="purchase_square transport" id="transport" data-unitId="0" data-unitTerrain="water" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square submarine" id="submarine" data-unitId="1" data-unitTerrain="water" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square destroyer" id="destroyer" data-unitId="2" data-unitTerrain="water" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square aircraftCarrier" id="aircraftCarrier" data-unitId="3" data-unitTerrain="water" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square soldier" id="soldier" data-unitId="4" data-unitTerrain="ground" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square artillery" id="artillery" data-unitId="5" data-unitTerrain="ground" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square tank" id="tank" data-unitId="6" data-unitTerrain="ground" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square marine" id="marine" data-unitId="7" data-unitTerrain="ground" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square lav" id="lav" data-unitId="8" data-unitTerrain="ground" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square attackHeli" id="attackHeli" data-unitId="9" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square sam" id="sam" data-unitId="10" data-unitTerrain="ground" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square fighter" id="fighter" data-unitId="11" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square bomber" id="bomber" data-unitId="12" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square stealthBomber" id="stealthBomber" data-unitId="13" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
                    <div class="purchase_square tanker" id="tanker" data-unitId="14" data-unitTerrain="air" onclick="piecePurchase(event, this);"></div>
                </div>
                <div id="purchase_seperator">Inventory</div>
                <div id="shopping_things">
                    <div id="purchased_container" data-positionType="purchased_container" data-positionId="118" data-positionContainerId="999999"><?php $positionId = 118; include("pieceDisplay.php"); ?></div>
                    <div id="trashbox" ondragover="positionDragover(event, this);" ondrop="pieceTrash(event, this);">*Trash*</div>
                </div>
                <div id="rest_things">
                    <div id="phase_indicator">Current Phase = Loading...</div>
                    <div id="team_indicator">Current Team = Loading...</div>
                    <button id="undo_button" onclick="pieceMoveUndo();">Undo Movement</button>
                    <button id="phase_button" onclick="changePhase();">Next Phase</button>
                    <button id="battle_button" onclick="battleChangeSection('selectPos');">Select Battle</button>
                </div>
            </div>

            <div id="game_board" onclick="gameboardClick(event, this);">
                <div class="gridblockLeftBig" id="special_island13">
                    <div class="gridblockTiny" data-positionType="land" id="pos13a" data-positionId="55" data-positionContainerId="999999" onclick="landClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 55; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13b" data-positionId="56" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 56; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13c" data-positionId="57" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 57; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13d" data-positionId="58" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 58; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13e" data-positionId="59" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 59; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13f" data-positionId="60" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 60; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13g" data-positionId="61" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 61; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13h" data-positionId="62" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 62; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13i" data-positionId="63" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 63; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13j" data-positionId="64" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 64; include("pieceDisplay.php"); ?></div>
                </div>
                <div class="gridblock" data-positionId="0" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 0; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="1" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 1; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="2" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 2; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="3" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 3; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="4" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 4; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="5" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 5; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="6" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 6; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="7" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 7; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="8" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 8; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island1" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island1" class="special_island1 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos1a" data-positionId="75" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 75; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos1b" data-positionId="76" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 76; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos1c" data-positionId="77" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 77; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos1d" data-positionId="78" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 78; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="9" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 9; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="10" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 10; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island2" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island2" class="special_island2 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos2a" data-positionId="79" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 79; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos2b" data-positionId="80" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 80; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos2c" data-positionId="81" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 81; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos2d" data-positionId="82" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 82; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="11" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 11; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island3" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island3" class="special_island3 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos3a" data-positionId="83" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 83; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos3b" data-positionId="84" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 84; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos3c" data-positionId="85" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 85; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="12" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 12; include("pieceDisplay.php"); ?></div>
                <div class="gridblockRightBig" id="special_island14">
                    <div class="gridblockTiny" data-positionType="land" id="pos13a" data-positionId="65" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 65; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13b" data-positionId="66" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 66; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13c" data-positionId="67" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 67; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13d" data-positionId="68" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 68; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13e" data-positionId="69" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 69; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13f" data-positionId="70" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 70; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13g" data-positionId="71" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 71; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13h" data-positionId="72" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 72; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13i" data-positionId="73" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 73; include("pieceDisplay.php"); ?></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13j" data-positionId="74" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 74; include("pieceDisplay.php"); ?></div>
                </div>
                <div class="gridblock" data-positionId="13" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 13; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="14" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 14; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="15" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 15; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island4" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island4" class="special_island4 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos4a" data-positionId="86" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 86; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos4b" data-positionId="87" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 87; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos4c" data-positionId="88" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 88; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos4d" data-positionId="89" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 89; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="16" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 16; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="17" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 17; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="18" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 18; include("pieceDisplay.php"); ?></div>
                <div class="gridblockEmptyLeft"></div>
                <div class="gridblock" data-positionId="19" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 19; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="20" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 20; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="21" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 21; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island5" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island5" class="special_island5 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos5a" data-positionId="90" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 90; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos5b" data-positionId="91" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 91; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos5c" data-positionId="92" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 92; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos5d" data-positionId="93" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 93; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="22" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 22; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="23" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 23; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="24" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 24; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island6" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island6" class="special_island6 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos6a" data-positionId="94" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 94; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos6b" data-positionId="95" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 95; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos6c" data-positionId="96" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 96; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="25" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 25; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="26" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 26; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island7" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island7" class="special_island7 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos7a" data-positionId="97" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 97; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos7b" data-positionId="98" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 98; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos7c" data-positionId="99" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 99; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="27" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 27; include("pieceDisplay.php"); ?></div>
                <div class="gridblock">
<!--                    TODO: Deal with this exception later (special_island5)-->
                </div>
                <div class="gridblock" data-positionId="28" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 28; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island8" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island8" class="special_island8 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos8a" data-positionId="100" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 100; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos8b" data-positionId="101" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 101; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos8c" data-positionId="102" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 102; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="29" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 29; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="30" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 30; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="31" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 31; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="32" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 32; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="33" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 33; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="34" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 34; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="35" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 35; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island9" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island9" class="special_island9 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos9a" data-positionId="103" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 103; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos9b" data-positionId="104" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 104; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos9c" data-positionId="105" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 105; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos9d" data-positionId="106" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 106; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="36" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 36; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="37" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 37; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island10" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island10" class="special_island10 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos10a" data-positionId="107" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 107; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos10b" data-positionId="108" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 108; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos10c" data-positionId="109" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 109; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos10d" data-positionId="110" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 110; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="38" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 38; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="39" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 39; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="40" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 40; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island11" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island11" class="special_island11 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos11a" data-positionId="111" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 111; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos11b" data-positionId="112" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 112; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos11c" data-positionId="113" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 113; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="41" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 41; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="42" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 42; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" id="special_island12" data-islandPopped="false" ondragleave="islandDragleave(event, this);" ondragenter="islandDragenter(event, this);" onclick="islandClick(event, this);">
                    <div id="special_island12" class="special_island12 special_island3x3" ondragleave="popupDragleave(event, this);">
                        <div class="gridblockTiny" data-positionType="land" id="pos12a" data-positionId="114" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 114; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos12b" data-positionId="115" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 115; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos12c" data-positionId="116" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 116; include("pieceDisplay.php"); ?></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos12d" data-positionId="117" data-positionContainerId="999999" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 117; include("pieceDisplay.php"); ?></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="43" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 43; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="44" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 44; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="45" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 45; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="46" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 46; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="47" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 47; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="48" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 48; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="49" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 49; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="50" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 50; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="51" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 51; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="52" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 52; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="53" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 53; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="54" data-positionContainerId="999999" data-positionType="water" onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"><?php $positionId = 54; include("pieceDisplay.php"); ?></div>
                <div id="battleZonePopup">
                    <div id="unused_attacker" data-boxId="1"><?php $boxId = 1; include("battlePieceDisplay.php"); ?></div>
                    <div id="unused_defender" data-boxId="2"><?php $boxId = 2; include("battlePieceDisplay.php"); ?></div>
                    <div id="used_attacker" data-boxId="3"><?php $boxId = 3; include("battlePieceDisplay.php"); ?></div>
                    <div id="used_defender" data-boxId="4"><?php $boxId = 4; include("battlePieceDisplay.php"); ?></div>
                    <div id="center_attacker" data-boxId="5"><?php $boxId = 5; include("battlePieceDisplay.php"); ?></div>
                    <div id="center_defender" data-boxId="6"><?php $boxId = 6; include("battlePieceDisplay.php"); ?></div>
                    <div id="battle_outcome"></div>
                    <div id="battle_buttons">
                        <button id="attackButton" onclick="battleAttackCenter('attack')" disabled>Attack!</button>
                        <button id="changeSectionButton" onclick="battleChangeSection('counter')">End My Turn</button>
                    </div>
                    <div id="battleActionPopup">
                        <button id="actionPopupButton">Button?</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>