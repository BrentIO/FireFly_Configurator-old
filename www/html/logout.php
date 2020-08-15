<?php
        require_once('./api/getConfig.php');
        session_start();
        session_destroy();
        header("Location: login.php");
?>