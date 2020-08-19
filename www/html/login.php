<?php
        require_once('./api/getConfig.php');

        $loginFailed = false;

        session_start();

        if(isset($_SESSION['loggedIn']) == true){

            if($_SESSION['loggedIn'] == true) {
                header("Location: statistics.php");
                exit();
            }
        }

        if(isset($_SESSION['loggedIn']) == false && isset($_POST['password']) == true)  {

            if($_POST['password'] == getConfig('guiPassword')) {
              
                $_SESSION['loggedIn'] = true;
                header("Location: statistics.php");
                exit();

            }else{
                $loginFailed = true;
            }
        }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>FireFly Configurator - Login</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="./jquery.toaster.js"></script>
        <script>
            function displayLoginFailure(){
                $.toaster({ settings : {'donotdismiss' : ['danger']  }});
                $.toaster({ priority :'danger', title :'Login Failed', message : 'Incorrect Password'});
            };
        </script>
        
    </head>
    <body class="login" <?php if($loginFailed == true){ ?>onload="displayLoginFailure()"<?php };?>>
        <div class="loginboxOuter">
            <div class="loginboxInner">
                <form action="login.php" method = "post">
                    <input type="password" name="password" placeholder="Password" class="password"><br>
                    <button type="submit" class="btn btn-primary" id="loginButton">Login</button>
                </form>
            </div>
        </div>

    </body>
</html>