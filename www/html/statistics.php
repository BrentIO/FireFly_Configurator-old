<?php

    require_once('./api/getConfig.php');
    require_once('common.php');

    //Validate the user is logged in
    checkLogin();

    $pageName = "statistics";
    $url = "http://" . $_SERVER['SERVER_ADDR'] . "/api/statistics";

?>
<!DOCTYPE html>
<html>
    <head>
        <title>FireFly Configurator - Statistics</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="jquery.toaster.js"></script>
        <script>        

            $(document).ready(function(){

                $.toaster({ settings : {'donotdismiss' : ['danger']  }});

                
            
            });

        </script>
    </head>
    <body>

<?php include "menu.php"?>
        
        <div class="content">
        <div id="pageName">
            <div id="pageTitle">Statistics</div>
        </div>

        

        </div>
    </body>
</html>