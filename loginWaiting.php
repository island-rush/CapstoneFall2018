<?php
session_start();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Waiting for Players...</title>
        <link rel="stylesheet" type="text/css" href="../kulpable2/homepageStyle.css">
        <script>
            var time_to_wait = 250;  // how often to check if the other player has joined
            var intUpdate;  // used to do functions on a timer
            var gameId = <?php echo '"'.$_SESSION['gameId'].'"'; ?>;

            intUpdate=window.setTimeout("keep_waiting()", time_to_wait);  //initialize refreshing

            function keep_waiting() {
                var xmlRequest = new XMLHttpRequest();
                xmlRequest.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        if (this.responseText === "start_game") {
                            clearTimeout(intUpdate);  //don't continue waiting
                            start_game();
                        }
                    }
                };
                xmlRequest.open("GET", "loginWaitingSynch.php?gameId=" + gameId, true);
                xmlRequest.send();
                intUpdate=window.setTimeout("keep_waiting()", time_to_wait);
            }

            function start_game() {
                window.open("playGame.php", "_self")
            }
        </script>
    </head>

    <body onload="keep_waiting()">
        <h1>Waiting for Other Player to Login...</h1>
    </body>
</html>