<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    $simpleRest = new simpleRest();

    $_GET_lower = array_change_key_case($_GET, CASE_LOWER);

    try {

        $database = new database();

        //Always assume success
        $simpleRest->setHttpHeaders(200);

        //Ensure the request method is GET
        if(strtolower($_SERVER['REQUEST_METHOD']) != "get"){

            throw new Exception(NULL, 405);

        }

        //Determine which statistic is requested
        if(isset($_GET_lower['statistic']) == false && $_GET_lower['statistic'] != ""){

            throw new Exception("No statistic specified", 400);

        }

        switch(strtolower($_GET_lower['statistic'])){

            case strtolower("breakerUtilization"):

                print(listStat("SELECT json FROM statBreakerUtilization;"));

            break;

            case strtolower("buttonColorCount"):

                print(listStat("SELECT json FROM statButtonColorCount;"));

            break;

            case strtolower("controllerCount"):

                print(listStat("SELECT json FROM statControllerCount;"));

            break;

            case strtolower("controllerPinUtilization"):

                print(listStat("SELECT json FROM statControllerPinUtilization;"));

            break;

            case strtolower("controllerPortUtilization"):

                print(listStat("SELECT json FROM statControllerPortUtilization;"));

            break;

            case strtolower("faceplateCount"):

                print(listStat("SELECT json FROM statFaceplateCount;"));

            break;

            case strtolower("outputType"):

                print(listStat("SELECT json FROM statOutputType;"));

            break;

            case strtolower("switchCount"):

                print(listStat("SELECT json FROM statSwitchCount;"));

            break;        

            default:
                throw new Exception(NULL, 404);
            break;

        }

    }
    
    catch (Exception $e){

        //Set the error message to be returned to the user
        $simpleRest->setErrorMessage($e->getMessage());

        //Set the HTTP response code appropriately
        if($e->getCode() != 0){
            $simpleRest->setHttpHeaders($e->getCode());
        }else{
            $simpleRest->setHttpHeaders(500);
        }
    }

    function listStat($sql){

        global $database;
        global $simpleRest;

        $response = $database->query($sql);

        //Ensure the response is an array, even if there are 0 or 1 rows
        if(is_array(json_decode($response)) == False){

            $responseArray = array();
            $responseArray[] = json_decode($response);

            return json_encode($responseArray);

        }else{

            //Return the list from SQL
            return($response);

        }

    }

?>