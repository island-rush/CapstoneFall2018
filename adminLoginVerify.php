<?php
session_abort(); //log out?
session_start();


if ( (isset($_POST['section'])) && (isset($_POST['instructor'])) && (isset($_POST['password'])) ){
    include("db.php");

    $section = $_POST['section'];
    $instructor = $_POST['instructor'];
    $password = md5($_POST['password']);

    $query = "SELECT * FROM GAMES WHERE gameInstructor = ? AND gameSection = ? AND gameAdminPassword = ?";
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("sss", $instructor, $section, $password);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $numRows = $results->num_rows;

    if ($numRows == 1) {
        $r= $results->fetch_assoc();
        $_SESSION['gameId'] = $r['gameId'];
        $_SESSION['secretAdminSessionVariable'] = "SpencerIsCool";
        header("location:admin.php");
    } else {
        //somehow had none or multiple database hits, not exactly 1 hit
        header("location:adminLogin.php?err=3");
    }

    $db->close();

} else {
    //Came to this file without sending everything
    header("location:adminLogin.php?err=1");
}

exit;
