<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Island Rush Admin</title>
        <link rel="stylesheet" type="text/css" href="index.css">
        <script type="text/javascript">
                function checkLoginForm(){
                    const sectionRegex = /^[MmTt][1-7][ABCDEFabcdef][1-9]$/;

                    let section = document.forms['teacherAdmin']['section'].value;
                    let instructor = document.forms['teacherAdmin']['instructor'].value;
                    let password = document.forms['teacherAdmin']['password'].value;
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
                    if(password === ""){
                        document.getElementById('passwordFeedback').innerHTML = "\tERROR: please put in a password";
                        valid = false;
                    }
                    // no longer uses teams for login as it is the instructor so we have to add this to the login query
                    return valid;
                }

        </script>
    </head>

    <body>
<!--    TODO: Change section to drop down menus for less user error, add passwords to the game/team? (more secure)...and simplify the form table-->
        <h1>Island Rush Login</h1>

        <nav>
            <a href="./index.php">Home</a>
            <a href="./login.php">Play the Game</a>
            <a class="active" href="adminLogin.php">Teacher Admin</a>
        </nav>

        <div class="spacer">
            <table border="0" width="100%">
                <tbody>
                    <tr>
                        <td colspan="4">
                            <br />
                            <div id="admin_header">Log in as an administrator:</div>
<!--                            Create code for the teacherLoginVerify-->
                            <form name="teacherAdmin" method="post" id="teacherAdmin" action="adminLoginVerify.php" onsubmit="return checkLoginForm()">
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
                                        <td>Password</td>
                                        <td>
                                            <input name="password" type="text" id="password">
                                            <div style="display: inline" id="passwordFeedback" class="formError"></div>
                                        </td>
                                    <tr>
<!--                                    wrong thing in value-->
                                        <td colspan="2"><br/><input type="submit" name="Submit" value="Log In"></td>
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


