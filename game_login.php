<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Island Rush Login</title>
        <link rel="stylesheet" type="text/css" href="../kulpable2/homepageStyle.css">
        <script type="text/javascript">
                function checkLoginForm(){
                    const sectionRegex = /^[MmTt][1-7][ABCDEFabcdef]$/;

                    var section = document.forms['login']['section'].value;
                    var instructor = document.forms['login']['instructor'].value;
                    var team = document.forms['login']['team'].value;
                    var valid = true;

                    if(section === ""){
                        document.getElementById('sectionFeedback').innerHTML = "\tERROR: Section must be specified";
                        valid = false;
                    }
                    else if(sectionRegex.test(section) === false){
                        document.getElementById('sectionFeedback').innerHTML = "\tERROR: Section improperly formatted, " +
                            "must look like 'M3A'";
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
        </script>
    </head>

    <body>
<!--    TODO: Change section to drop down menus for less user error, add passwords to the game/team? (more secure)-->
        <h1>Island Rush Login</h1>

        <nav>
            <a href="./index.php">Home</a>
            <a class="active" href="./game_login.php">Play the Game</a>
        </nav>

        <div class="spacer">
            <table border="0" width="100%">
                <tbody>
                    <tr>
                        <td colspan="4">
                            <br />
                            <div id="login_header">Login to Your Island Rush Game:</div>
                            <form name="login" method="post" id="login" action="game_login_verify.php" onsubmit="return checkLoginForm()">
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
                                            <input name="section" type="text" id="section">
                                            <div style="display: inline" id="sectionFeedback" class="formError"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Teacher Last Name</td>
                                        <td>
                                            <input name="instructor" type="text" id="instructor">
                                            <div style="display: inline" id="instructorFeedback" class="formError"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Red or Blue Team</td>
                                        <td>
                                            <input type="radio" name="team" value="Red" checked> Red<br>
                                            <input type="radio" name="team" value="Blue"> Blue<br>
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


