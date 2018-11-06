<?php
session_start();
include("db.php");

if (!isset($_SESSION['secretAdminSessionVariable'])) {
    header("location:index.php?err=4");
    exit;
}

// search DB for game
$gameId = $_SESSION['gameId'];
$query = "SELECT * FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();

// get infor about this game
$results = $preparedQuery->get_result();
$r= $results->fetch_assoc();
$gameChecked = $r['gameActive'];
$section = $r['gameSection'];
$instructor = $r['gameInstructor'];


// search DB for this game's News Alerts
$zero = 0;
$query = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? ORDER BY newsOrder ASC";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("ii", $gameId, $zero);
$preparedQuery->execute();
// get info about this game's news alerts
$newsAlerts = $preparedQuery->get_result();
$news_rows= $newsAlerts->num_rows;

$query = "SELECT newsOrder FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? ORDER BY newsOrder ASC";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("ii", $gameId, $zero);
$preparedQuery->execute();
// whats the lowest order (for min values on order inputs)
$firstOrder = $preparedQuery->get_result()->fetch_assoc()['newsOrder'];

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .adminWrapper{
            /*background-color: lightblue;*/
            width: 80%;
            margin: 60px auto;
        }
        .adminWrapper > h1{
            margin:10px 0 20px 0;
            padding:0;
        }
        .adminWrapper > h3{
            margin: 10px 0 5px 0;
        }
        .adminWrapper > span{
            margin: 5px 20px;
        }

        .important {
            text-decoration: underline;
        }

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

        .btn-danger {
            color: #fbdedd;
            /*border-color: #F4511E;*/
            background: #ff4b37;
            /*background: linear-gradient(to bottom, #FF8A65 0%, #FF7043 100%);*/
            /*box-shadow: inset 0 1px #FFCCBC, 0 1px 2px rgba(0, 0, 0, 0.2);*/
        }
        .btn {
            display: inline-block;
            margin-bottom: 0;
            text-align: center;
            text-transform: uppercase;
            vertical-align: middle;
            cursor: pointer;
            background-image: none;
            whitespace: nowrap;
            padding: 6px 12px;
            font-size: 1.4rem;
            border-radius: 3px;
            border: 1px solid transparent;
            text-decoration: none;
            user-select: none;
        }
    </style>
    <title>Island Rush Admin</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <script type="text/javascript">
        let gameId = "<?php echo $gameId; ?>";
        let section = "<?php echo $section; ?>";
        let instructor = "<?php echo $instructor; ?>";

        let allSections = <?php $query2 = 'SELECT * FROM games'; $query2 = $db->prepare($query2); $query2->execute(); $results2 = $query2->get_result(); $num_results2 = $results2->num_rows; $arr = array();
            if ($num_results2 > 0) {
                for ($i=0; $i < $num_results2; $i++) {
                    $z= $results2->fetch_assoc();
                    $gameSection = $z['gameSection'];
                    $arr[$i] = $gameSection;
                }
            }
            echo json_encode($arr); ?>;

        let allInstructors = <?php $query2 = 'SELECT * FROM games'; $query2 = $db->prepare($query2); $query2->execute(); $results2 = $query2->get_result(); $num_results2 = $results2->num_rows; $arr = array();
            if ($num_results2 > 0) {
                for ($i=0; $i < $num_results2; $i++) {
                    $z= $results2->fetch_assoc();
                    $gameInstructor = $z['gameInstructor'];
                    $arr[$i] = $gameInstructor;
                }
            }
            echo json_encode($arr); ?>;

        function setActive(){
            let gameActive;
            if (document.getElementById("activeToggle").checked === true){
                gameActive = 1;
            }else{
                gameActive = 0;
            }
            let setGameActivity = new XMLHttpRequest();
            setGameActivity.open("POST", "adminGameToggle.php?gameActive=" + gameActive, true);
            setGameActivity.send();
        }

        function populateGame() {
            if(confirm("ARE YOU SURE YOU WANT TO COMPLETELY RESET THIS GAME?")){
                if(confirm("This will delete all information for the game and set it back to the initial start state of the game. " +
                    "\n\n   ARE YOU SURE YOU WANT TO RESET?")){
                    let phpGamePopulate = new XMLHttpRequest();
                    phpGamePopulate.open("GET", "gamePopulate.php?section=" + section + "&instructor=" + instructor, true);
                    phpGamePopulate.send();

                    document.getElementById("populateButton").disabled = true;
                    document.getElementById("activeToggle").checked = false;
                    setTimeout(function thingy() {window.location.replace("admin.php");}, 7000);
                }
            }
        }

        function swapNewsAlerts(){
            let swap1order = document.getElementById("swap1").value;
            let swap2order = document.getElementById("swap2").value;

            let phpSwapNewsAlerts  = new XMLHttpRequest();
            phpSwapNewsAlerts.open("GET", "adminSwapNews.php?gameId=" + gameId + "&swap1order=" + swap1order + "&swap2order=" + swap2order, true);
            phpSwapNewsAlerts.send();

            setTimeout(function thingy() {window.location.replace("admin.php");}, 7000);
        }

        function populateAllGames() {
            if (confirm("Are you sure you want to completely reset all games?")) {
                if (confirm("Are you really sure you want to completely reset all games?")) {
                    if (confirm("Are you super really sure you want to completely reset all games?")) {
                        if (confirm("Are you actually sure you want to completely reset all games?")) {
                            if (confirm("But like, Are you for real sure you want to completely reset all games?")) {
                                if (confirm("Are you for sure for sure you want to completely reset all games?")) {
                                    if (confirm("CAN YOU NOT?")) {
                                        for (let x = 0; x < allSections.length; x++) {
                                            let phpGamePopulate = new XMLHttpRequest();
                                            phpGamePopulate.open("GET", "gamePopulate.php?section=" + allSections[x] + "&instructor=" + allInstructors[x], true);
                                            phpGamePopulate.send();
                                        }
                                    }
                                }
                            }
                        }
                    }
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

<div class="adminWrapper">             <!------------------------ the  actual  body ---------------------------->

    <h1>Admin Tools</h1>
    <div>You are logged in for the Game:</div>
    <span class="important" id="section">Section: <?php echo $section; ?></span>
    <span class="important" id="instructor">Instructor: <?php echo $instructor; ?></span>

    <?php
        if ($instructor != "Start") {
            echo '<br>
    <hr>
    <h3>Turn Game Off/On</h3>
    <div>Toggle if the game is active or not. If inactive, students cannot log in to make moves, but can still spectate to see the board.
        <br>(***Also logs everyone out of the game. If there are problems logging in, turn the game off and back on using this slider.)</div>
    <span>Inactive</span>
    <label  class="switch">
        <input id="activeToggle" type="checkbox" ';
        if ($gameChecked === 1){
            echo "checked";
        }

        echo 'onclick="setActive()">
        <span class="slider round"></span>
    </label>
    <span>Active</span>

    <br>
    <hr>
    <h3>Reset Game</h3>
    <span>Completely reset this particular game:</span>
    <button class="btn btn-danger" id="populateButton" onclick="populateGame()">RESET GAME</button>';
        }
    ?>

    <?php
        if ($instructor == "Start") {
            echo '<br><hr><h3>Reset / Populate All Games (Never press this button)</h3><button class="btn btn-danger" id="populateButton" onclick="populateAllGames();">DO NOT PRESS THIS BUTTON</button>';
        }

    ?>

    <?php
        if ($instructor != "Start") {
            echo '<br>
    <hr>
    <h3>News Alerts for this game:</h3>
    <div id="newsAlertsContainer">';
        if ($news_rows > 0){
            // Setup the table for the news alerts
            echo "
            <form id='swapNewsForm'>
                <div>Use this form to swap two news alerts. Refresh the page to show the most up-to-date news alerts for this game.</div>
                <label>Swap #</label>
                <input type='number' id='swap1' required min='".$firstOrder."' max='".$news_rows."'>
                <label> with #</label>
                <input type='number' id='swap2' required min='".$firstOrder."' max='".$news_rows."'>
                <button onclick='swapNewsAlerts();'>swap</button>
            </form>
            <table id=\'newsAlertTable\'>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Effect</th>
                </tr>";
            // loop through all news alerts
            for($i = 0; $i < $news_rows; $i++){
                $news = $newsAlerts->fetch_assoc();
                $order = $news['newsOrder'];
                $name = $news['newsText'];
                $effect = $news['newsEffectText'];
                echo "<tr>
                        <td>".$order."</td>
                        <td>".$name."</td>
                        <td>".$effect."</td>
                    </tr>";
            }
        }
        else {
            echo "<h3>Notice: There are no News Alerts because the game is empty/not created. <br>Use the RESET GAME button above to populate the game.</h3>";
        };

        echo '</table>
    </div>';
        }
    ?>

</body>
</html>
