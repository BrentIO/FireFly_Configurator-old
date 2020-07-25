<?php

    //Comment out this line for production
    ini_set('display_errors', 'On');

    $config = array();

    function getConfig($key){

        global $config;
        
        if(count($config) == 0){

            #Only read the file from disk once
            $configFile = file_get_contents("/var/www/config.json");
            $config = json_decode($configFile, true);

        }

        #Cycle through each key to find the one we want
        foreach($config as $jkey => $value){
            if(strtolower($jkey) == strtolower($key)){
                return $value;
            }
        }
    }

?>