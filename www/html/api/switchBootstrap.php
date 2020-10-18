<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    //Do not require auth for this page
    $simpleRest = new simpleRest(false);
    $switchBootstrap = new switchBootstrap();

    $_GET_lower = array_change_key_case($_GET, CASE_LOWER);

    try {

        $database = new database();

        //Always assume success
        $simpleRest->setHttpHeaders(200);

        //Get the body contents
        $data = json_decode(file_get_contents('php://input'), true);

        //If there is JSON in the body, make sure it is valid
        if (json_last_error() !== JSON_ERROR_NONE && strlen(file_get_contents('php://input'))>0) {

            throw new Exception("Invalid body", 400);
        }

        //Populate the object with a preferene for the URL rather than the payload
        if(isset($_GET_lower['devicename'])){

            $switchBootstrap->deviceName = $_GET_lower['devicename'];
            $switchBootstrap->deviceName = strtoupper($switchBootstrap->deviceName);
        }     
        
        switch(strtolower($_SERVER['REQUEST_METHOD'])){

            case "get":

                #See if the user is attempting to get one or many ID's
                if($switchBootstrap->deviceName != NULL){

                    #Get the specific ID requested
                    print($switchBootstrap->get());
                }else{
                    throw new Exception("No Device Name specified", 400);
                }

            break;

            default:
                throw new Exception(NULL, 405);
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

    class switchBootstrap{

        public $deviceName;

        function __construct(){

            $this->deviceName = NULL;
        }
    
        function get(){

            global $database;
            global $simpleRest;

            if($this->deviceName != NULL){
                $response = $database->query("SELECT json FROM getSwitchBootstraps WHERE deviceName = '" . $this->deviceName . "';");
            }
            
            if(is_array(json_decode($response)) == False){

                return($response);
        
            }else{
                throw new Exception(NULL, 404);
                return;
            }
        }
    }

?>