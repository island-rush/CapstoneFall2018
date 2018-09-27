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
                    if(team !== 'Red' && team !== 'Blue'){
                        document.getElementById('formFeedback').innerHTML = "ERROR: Team not set correctly," +
                            " how'd you even do this?";
                        valid = false;
                    }
                    return valid;
                }

                function populateGame() {
                    let section = document.forms['login']['section'].value;
                    let instructor = document.forms['login']['instructor'].value;

                    let phpGamePopulate = new XMLHttpRequest();
                    phpGamePopulate.open("POST", "gamePopulate.php?section=" + section + "&instructor=" + instructor, true);
                    phpGamePopulate.send();
                }
        </script>
    </head>

    <body>
<!--    TODO: Change section to drop down menus for less user error, add passwords to the game/team? (more secure)...and simplify the form table-->
        <h1>Island Rush Login</h1>
        <nav>
            <a href="./index.php">Home</a>
            <a class="active" href="login.php">Play the Game</a>
            <a href="adminLogin.php">Teacher Admin</a>
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
                                                if (isset($_GET['err'])) {echo 'ERROR: Something Not Valid.'; }
                                                ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Section</td>
                                        <td>
                                            <input name="section" type="text" id="section" value="m1a1">
                                            <div style="display: inline" id="sectionFeedback" class="formError"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Teacher Last Name</td>
                                        <td>
                                            <input name="instructor" type="text" id="instructor" value="adolph">
                                            <div style="display: inline" id="instructorFeedback" class="formError"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Red or Blue Team</td>
                                        <td>
                                            <input type="radio" name="team" value="Red"> Red<br>
                                            <input type="radio" name="team" value="Blue" checked> Blue<br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><br/><input type="submit" name="Submit" value="Login"></td>
                                    </tr>
                                </table>
                            </form>
                            <button onclick="populateGame()">Populate Game</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>


