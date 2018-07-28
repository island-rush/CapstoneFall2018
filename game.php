<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Island Rush Game</title>
        <link rel="stylesheet" type="text/css" href="game.css">
        <script type="text/javascript" src="game.js">
            var gameId = "<?php echo $_SESSION['gameId']; ?>";
            var currentPhase = "<?php echo $_SESSION['gamePhase']; ?>";
            var currentTurn = "<?php echo $_SESSION['gameTurn']; ?>";
            var currentTeam = "<?php echo $_SESSION['gameCurrentTeam']; ?>";
            var myTeam = "<?php echo $_SESSION['myTeam']; ?>";
            var canMove = "<?php echo $_SESSION['canMove']; ?>";
            var canPurchase = "<?php echo $_SESSION['canPurchase']; ?>";
            var canUndo = "<?php echo $_SESSION['canUndo']; ?>";
            var canNextPhase = "<?php echo $_SESSION['canNextPhase']; ?>";
            var canTrash = "<?php echo $_SESSION['canTrash']; ?>";
            var canAttack = "<?php echo $_SESSION['canAttack']; ?>";
            var gameBattleSection = "<?php echo $_SESSION['gameBattleSection']; ?>";  //possibly use this for disabling board stuff when popup is active?
            var gameBattleSubSection = "<?php echo $_SESSION['gameBattleSubSection']; ?>";
            var gameBattleLastRoll = "<?php echo $_SESSION['gameBattleLastRoll']; ?>";
            var gameBattleLastMessage = "<?php echo $_SESSION['gameBattleLastMessage']; ?>";

            var hovertimer;  //used for waiting to pop something up (island/transport)
            var bigblockvisible = "true";  //used in check_prevent_popup (prevent if not already visible)
            var skip = "no";  //used for not doing a parent's call when child called the method
            var skipclear = 2; //2 is don't skip, 3 is skip TODO: change these and other weird shit
            var skipdrop1 = 8; //8 is don't skip, 4 is skip (more for children/parent call inheritance)
            var unitsMoves = <?php include("unit_moves.php"); ?>;  //array to store default moves of each piece (for resetting on a certain phase?)
            var phaseNames = ['News', 'Buy Reinforcements', 'Combat', 'Fortify Move', 'Reinforcement Place', 'Hybrid War', 'Tally Points'];
        </script>
    </head>

    <body>

    </body>
</html>