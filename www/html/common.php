<?php

    function checkLogin(){

        /*************************************************/
        /*  Validates the user is logged in.  If not,   */
        /*      redirects to the login page.            */
        /*************************************************/

        session_start();

        if(isset($_SESSION['loggedIn']) != true){

            header("Location: login.php");
            exit();            
        }

    }

?>