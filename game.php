<?php
session_start();
include("db.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Island Rush Game</title>
        <link rel="stylesheet" type="text/css" href="game.css">
        <script src="game.js"></script>
        <script type="text/javascript">
            var phaseNames = ['News', 'Buy Reinforcements', 'Combat', 'Fortify Move', 'Reinforcement Place', 'Hybrid War', 'Tally Points'];
            var unitsMoves = <?php $query = 'SELECT * FROM units'; $query = $db->prepare($query); $query->execute(); $results = $query->get_result(); $num_results = $results->num_rows; $arr = array();
                if ($num_results > 0) {
                    for ($i=0; $i < $num_results; $i++) {
                        $r= $results->fetch_assoc();
                        $unitName = $r['unitName'];
                        $unitMoves = $r['unitMoves'];
                        $arr[$unitName] = $unitMoves;
                    }
                }
                echo json_encode($arr); ?>;

            var gameId = "<?php echo $_SESSION['gameId']; ?>";
            var gamePhase = "<?php echo $_SESSION['gamePhase']; ?>";
            var gameCurrentTurn = "<?php echo $_SESSION['gameTurn']; ?>";
            var gameCurrentTeam = "<?php echo $_SESSION['gameCurrentTeam']; ?>";
            var myTeam = "<?php echo $_SESSION['myTeam']; ?>";

            var gameBattleSection = "<?php echo $_SESSION['gameBattleSection']; ?>";
            var gameBattleSubSection = "<?php echo $_SESSION['gameBattleSubSection']; ?>";
            var gameBattleLastRoll = "<?php echo $_SESSION['gameBattleLastRoll']; ?>";
            var gameBattleLastMessage = "<?php echo $_SESSION['gameBattleLastMessage']; ?>";


            var canMove = "<?php echo $_SESSION['canMove']; ?>";
            var canPurchase = "<?php echo $_SESSION['canPurchase']; ?>";
            var canUndo = "<?php echo $_SESSION['canUndo']; ?>";
            var canNextPhase = "<?php echo $_SESSION['canNextPhase']; ?>";
            var canTrash = "<?php echo $_SESSION['canTrash']; ?>";
            var canAttack = "<?php echo $_SESSION['canAttack']; ?>";
        </script>
    </head>

    <body>
        <div id="whole_game">
            <div id="side_panel">
                <div id="titlebar">Reinforcements</div>
                <div id="purchase_buttons_container">
                    <div class="purchase_square transport" id="transport" data-unitId="0"></div>
                    <div class="purchase_square submarine" id="submarine" data-unitId="1"></div>
                    <div class="purchase_square destroyer" id="destroyer" data-unitId="2"></div>
                    <div class="purchase_square aircraftCarrier" id="aircraftCarrier" data-unitId="3"></div>
                    <div class="purchase_square soldier" id="soldier" data-unitId="4"></div>
                    <div class="purchase_square artillery" id="artillery" data-unitId="5"></div>
                    <div class="purchase_square tank" id="tank" data-unitId="6"></div>
                    <div class="purchase_square marine" id="marine" data-unitId="7"></div>
                    <div class="purchase_square lav" id="lav" data-unitId="8"></div>
                    <div class="purchase_square attackHeli" id="attackHeli" data-unitId="9"></div>
                    <div class="purchase_square sam" id="sam" data-unitId="10"></div>
                    <div class="purchase_square fighter" id="fighter" data-unitId="11"></div>
                    <div class="purchase_square bomber" id="bomber" data-unitId="12"></div>
                    <div class="purchase_square stealthBomber" id="stealthBomber" data-unitId="13"></div>
                    <div class="purchase_square tanker" id="tanker" data-unitId="14"></div>
                </div>
                <div id="shopping_things">
                    <div id="purchased_container" data-positionId="118"></div>
                    <div id="trashbox">*Trash*</div>
                </div>
                <div id="rest_things">
                    <div id="phase_indicator">Phase Indicator</div>
                    <div id="team_indicator">Team Indicator</div>
                    <button>Undo Movement</button>
                    <button>Next Phase</button>
                    <button>Select BattlePos</button>
                    <button>Select BattlePieces</button>
                    <button>Start Battle</button>
                    <button>Exit Game</button>
                </div>
            </div>

            <div id="game_board" onclick="clickGameBoard(event, this);">
                <div class="gridblockLeftBig" id="special_island13">
                    <div class="gridblockTiny" data-positionType="land" id="pos13a" data-positionId="55"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13b" data-positionId="56"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13c" data-positionId="57"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13d" data-positionId="58"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13e" data-positionId="59"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13f" data-positionId="60"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13g" data-positionId="61"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13h" data-positionId="62"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13i" data-positionId="63"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13j" data-positionId="64"></div>
                </div>
                <div class="gridblock" data-positionId="0" data-positionContainerId="999999" data-positionType="water" onclick="clickWater(event, this);" ondragover="pieceDragover(event, this);" ondrop="pieceDrop(event, this);"><?php $positionId = 0; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="1" data-positionContainerId="999999" data-positionType="water" onclick="clickWater(event, this);" ondragover="pieceDragover(event, this);" ondrop="pieceDrop(event, this);"><?php $positionId = 1; include("pieceDisplay.php"); ?></div>
                <div class="gridblock" data-positionId="2" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="3" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="4" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="5" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="6" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="7" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="8" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island1" onclick="clickIsland(event, this);">
                    <div id="bigblock1" class="special_island1 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos1a" data-positionId="75"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos1b" data-positionId="76"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos1c" data-positionId="77"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos1d" data-positionId="78"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="9" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="10" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island2" onclick="clickIsland(event, this);">
                    <div id="bigblock2" class="special_island2 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos2a" data-positionId="79"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos2b" data-positionId="80"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos2c" data-positionId="81"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos2d" data-positionId="82"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="11" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island3" onclick="clickIsland(event, this);">
                    <div id="bigblock3" class="special_island3 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos3a" data-positionId="83"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos3b" data-positionId="84"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos3c" data-positionId="85"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="12" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblockRightBig" id="special_island14">
                    <div class="gridblockTiny" data-positionType="land" id="pos13a" data-positionId="65"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13b" data-positionId="66"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13c" data-positionId="67"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13d" data-positionId="68"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13e" data-positionId="69"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13f" data-positionId="70"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13g" data-positionId="71"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13h" data-positionId="72"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13i" data-positionId="73"></div>
                    <div class="gridblockTiny" data-positionType="land" id="pos13j" data-positionId="74"></div>
                </div>
                <div class="gridblock" data-positionId="13" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="14" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="15" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island4" onclick="clickIsland(event, this);">
                    <div id="bigblock4" class="special_island4 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos4a" data-positionId="86"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos4b" data-positionId="87"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos4c" data-positionId="88"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos4d" data-positionId="89"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="16" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="17" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="18" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblockEmptyLeft"></div>
                <div class="gridblock" data-positionId="19" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="20" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="21" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island5" onclick="clickIsland(event, this);">
                    <div id="bigblock5" class="special_island5 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos5a" data-positionId="90"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos5b" data-positionId="91"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos5c" data-positionId="92"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos5d" data-positionId="93"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="22" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="23" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="24" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island6" onclick="clickIsland(event, this);">
                    <div id="bigblock6" class="special_island6 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos6a" data-positionId="94"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos6b" data-positionId="95"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos6c" data-positionId="96"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="25" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="26" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island7" onclick="clickIsland(event, this);">
                    <div id="bigblock7" class="special_island7 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos7a" data-positionId="97"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos7b" data-positionId="98"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos7c" data-positionId="99"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="27" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock">
<!--                    TODO: Deal with this exception later (special_island5)-->
                </div>
                <div class="gridblock" data-positionId="28" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island8" onclick="clickIsland(event, this);">
                    <div id="bigblock8" class="special_island8 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos8a" data-positionId="100"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos8b" data-positionId="101"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos8c" data-positionId="102"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="29" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="30" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="31" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="32" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="33" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="34" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="35" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island9" onclick="clickIsland(event, this);">
                    <div id="bigblock9" class="special_island9 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos9a" data-positionId="103"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos9b" data-positionId="104"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos9c" data-positionId="105"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos9d" data-positionId="106"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="36" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="37" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island10" onclick="clickIsland(event, this);">
                    <div id="bigblock10" class="special_island10 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos10a" data-positionId="107"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos10b" data-positionId="108"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos10c" data-positionId="109"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos10d" data-positionId="110"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="38" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="39" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="40" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island11" onclick="clickIsland(event, this);">
                    <div id="bigblock11" class="special_island11 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos11a" data-positionId="111"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos11b" data-positionId="112"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos11c" data-positionId="113"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="41" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="42" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" id="special_island12" onclick="clickIsland(event, this);">
                    <div id="bigblock12" class="special_island12 bigblock3x3">
                        <div class="gridblockTiny" data-positionType="land" id="pos12a" data-positionId="114"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos12b" data-positionId="115"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos12c" data-positionId="116"></div>
                        <div class="gridblockTiny" data-positionType="land" id="pos12d" data-positionId="117"></div>
                    </div>
                </div>
                <div class="gridblock" data-positionId="43" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="44" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="45" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="46" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="47" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="48" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="49" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="50" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="51" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="52" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="53" data-positionType="water" onclick="clickWater(event, this);"></div>
                <div class="gridblock" data-positionId="54" data-positionType="water" onclick="clickWater(event, this);"></div>
            </div>
        </div>
    </body>
</html>