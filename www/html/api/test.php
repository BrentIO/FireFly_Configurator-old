<?php
    ini_set('display_errors', 'On');

    require_once('simpleRest.php');
    require_once('database.php');

    $database = new database();
    $simpleRest = new simpleRest();

    $controllers = $database->query("SELECT brightnessNames FROM firefly.getColorBrightnessNames;", true, false);

    if($controllers){

        $simpleRest->setHttpHeaders(200);
        
        print($controllers);

    }else{
        $simpleRest->setHttpHeaders(500);
    }

    

?>