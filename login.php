<?php
session_abort();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Island Rush Login</title>
        <link rel="stylesheet" type="text/css" href="index.css">
        <script type="text/javascript">
                function checkLoginForm(){
                    const sectionRegex = /^[MmTt][1-7][ABCDEFabcdef][1-9]$/;

                    let section = document.forms['login']['section'].value;
                    let instructor = document.forms['login']['instructor'].value;
                    let team = document.forms['login']['team'].value;
                    let valid = true;

                    if(section === ""){
                        document.getElementById('sectionFeedback').innerHTML = "\tERROR: Section must be specified";
                        valid = false;
                    }
                    else if(sectionRegex.test(section) === false){
                        document.getElementById('sectionFeedback').innerHTML = "\tERROR: Section improperly formatted, " +
                            "must look like 'M3A1'";
                        valid = false;
                    }
                    if(instructor === ""){
                        document.getElementById('instructorFeedback').innerHTML = "\tERROR: Instructor must be specified";
                        valid = false;
                    }
                    if(team !== 'Red' && team !== 'Blue' && team != 'Spectator'){
                        document.getElementById('formFeedback').innerHTML = "ERROR: Team not set correctly. Ensure you are using Chrome.";
                        valid = false;
                    }
                    return valid;
                }

                // function populateGame() {
                //     let section = document.forms['login']['section'].value;
                //     let instructor = document.forms['login']['instructor'].value;
                //
                //     let phpGamePopulate = new XMLHttpRequest();
                //     phpGamePopulate.open("POST", "gamePopulate.php?section=" + section + "&instructor=" + instructor, true);
                //     phpGamePopulate.send();
                //
                //     document.getElementById("populateButton").disabled = true;
                // }
        </script>
    </head>

    <body>
        <h1>Island Rush Login</h1>
        <nav>
            <a href="./index.php">Home</a>
            <a class="active" href="login.php">Play the Game</a>
            <a href="adminLogin.php">Teacher Admin</a>
            <a href="ruleBook.php">Rule Book</a>
        </nav>

        <div class="spacer">
            <table border="0" width="100%">
                <tbody>
                    <tr>
                        <td colspan="4">
                            <br />
                            <div id="login_header">Login to Your Island Rush Game:</div>
                            <form name="login" method="post" id="login" action="loginVerify.php" onsubmit="return checkLoginForm()">
                                <table border="0" cellpadding="3" cellspacing="1">
                                    <tr>
                                        <td colspan="2">
                                            <div id="formFeedback" class="formError">
                                                <?php
                                                if (isset($_GET['err'])) {
                                                    $eType = (int) $_GET['err'];
                                                    if ($eType == 1) {
                                                        echo 'ERROR: Something Not Valid.';
                                                    }
                                                    if ($eType == 2) {
                                                        echo 'ERROR: This player is already logged in. Have your teacher disable and re-enable the game.';
                                                    }
                                                    if ($eType == 3) {
                                                        echo 'ERROR: Server did not receive all 3 inputs. (Section + Instructor + Team)';
                                                    }
                                                    if ($eType == 4) {
                                                        echo 'Teacher Forced a Logout...';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Section</td>
                                        <td>
                                            <input name="section" type="text" id="section" placeholder="m1a1" autofocus="true" required>
                                            <div style="display: inline" id="sectionFeedback" class="formError"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Teacher Last Name</td>
                                        <td>
                                            <input name="instructor" type="text" id="instructor" placeholder="Name" required>
                                            <div style="display: inline" id="instructorFeedback" class="formError"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Team</td>
                                        <td>
                                            <input type="radio" name="team" value="Spectator" checked> Spectator<br>
                                            <input type="radio" name="team" value="Blue"> Blue<br>
                                            <input type="radio" name="team" value="Red"> Red<br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><br/><input type="submit" name="Submit" value="Login"></td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>


