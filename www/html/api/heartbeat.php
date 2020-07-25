<?php
   
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');
    
    $database = new database();
    $simpleRest = new simpleRest();

    $procCall = $database->callProcedure("CALL getHeartbeat(@response)");

    //Check to make sure the procedure was successful
    if($procCall == true)
    {
        $simpleRest->setHttpHeaders(200);

        //Get the response back from the getHeartbeat SP
        $heartbeat = $database->query("SELECT 'ok' AS 'status', @response AS timeUTC;");
        
        //Output the time and status
        print($heartbeat);

    }else{

        $simpleRest->setHttpHeaders(500);

        //The procedure call had a failure (wrong name or no rows in the settings table?), so return that the system is unhealthy
        print('{"status":"failed"}');
        
    }
?>