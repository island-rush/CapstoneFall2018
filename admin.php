<?php
    //control this page with the session info (which game-admin page did they log into?)
    session_start();
    $gameId = $_SESSION['gameId'];
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title>Island Rush Admin</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <script type="text/javascript">
        //functions here for administrating this page
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


<p>Teacher Admin Stuff Here</p>


</body>
</html>
