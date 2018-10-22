<?php
session_start();
include("db.php");

if (!isset($_SESSION['secretAdminSessionVariable'])) {
    header("location:index.php");
    exit;
}

$gameId = $_SESSION['gameId'];
$query = "SELECT * FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();

$results = $preparedQuery->get_result();
$r= $results->fetch_assoc();
$gameChecked = $r['gameActive'];

$section = $r['gameSection'];
$instructor = $r['gameInstructor'];

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {display:none;}

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    <title>Island Rush Admin</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <script type="text/javascript">

        let section = "<?php echo $section; ?>";
        let instructor = "<?php echo $instructor; ?>";

        function setActive(){
            let gameActive;
            if (document.getElementById("activeToggle").checked === true){
                gameActive = 1;
            }else{
                gameActive = 0;
            }
            // alert(gameActive);
            let setGameActivity = new XMLHttpRequest();
            setGameActivity.open("POST", "adminGameToggle.php?gameActive=" + gameActive, true);
            setGameActivity.send();
        }

        function populateGame() {
            if(confirm("ARE YOU SURE YOU WANT TO COMPLETELY RESET THIS GAME?")){
                if(confirm("This will delete all information for the game and set it back to the initial Start of the game. &#013;&#013;ARE YOU SURE YOU WANT TO RESET?")){
                    let phpGamePopulate = new XMLHttpRequest();
                    phpGamePopulate.open("POST", "gamePopulate.php?section=" + section + "&instructor=" + instructor, true);
                    phpGamePopulate.send();

                    document.getElementById("populateButton").disabled = true;
                }
            }
        }

        </script>
</head>

<body>
<h1>Island Rush Admin</h1>

<nav>
    <a href="./index.php">Home</a>
    <a href="./login.php">Play the Game</a>
    <a class="active" href="adminLogin.php">Teacher Admin</a>
    <a href="ruleBook.php">Rule Book</a>
</nav>


<div class="spacer">
    <table border="0" width="100%">
        <tbody>
        <tr>
            <td colspan="4">

                <h1>Admin Tools</h1>

                <div id="section">Section: <?php echo $section; ?></div>
                <div id="instructor">Instructor: <?php echo $instructor; ?></div>

                <br />
                <br />

                <div id="toggle_swtich_text">Toggle if the game is active or not.</div>

                <label  class="switch">
                    <input id="activeToggle" type="checkbox" <?php
                    if ($gameChecked === 1){
                        echo "checked";
                    }
                    ?> onclick="setActive()">
                    <span class="slider round"></span>
                </label>

                <br />
                <br />

                <button id="populateButton" onclick="populateGame()">RESET GAME</button>
            </td>
        </tr>
        </tbody>
    </table>

</body>
</html>
