<?php
session_start();
include("db.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Island Rush Game</title>
        <link rel="stylesheet" type="text/css" href="game.css">
        <script type="text/javascript" src="game.js">
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
            var currentPhase = "<?php echo $_SESSION['gamePhase']; ?>";
            var currentTurn = "<?php echo $_SESSION['gameTurn']; ?>";
            var currentTeam = "<?php echo $_SESSION['gameCurrentTeam']; ?>";
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
            <div id="side_panel"></div>
            <div id="game_board">
                <div class="gridblockLeftBig" ondragenter="clear_hover_timer(event)" onclick="hideall_big()">

                </div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblockRightBig">

                </div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblockEmptyLeft"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
                <div class="gridblock"></div>
            </div>
        </div>
    </body>
</html>