<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    $simpleRest = new simpleRest();
    $port = new port();

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

        //Populate the breaker object with a preferene for the URL rather than the payload
        if(isset($_GET_lower['controllerid']) && $_GET_lower['controllerid'] != "" && is_numeric($_GET_lower['controllerid']) == True){

            $port->controllerId = intval($_GET_lower['controllerid']);

        }

        switch(strtolower($_SERVER['REQUEST_METHOD'])){

            case "get":

                #See if the user is attempting to get one or many ID's
                if($port->controllerId != NULL && is_numeric($port->controllerId)){

                    #Get the specific ID requested
                    print($port->get());

                }
                else{
                    throw new Exception(NULL, 400);
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

    class port{

        public $controllerId;
        public $port;
        public $inputAllowed;
        public $outputAllowed;


        function __construct(){

            $this->controllerId = NULL;
            $this->port = NULL;
            $this->inputAllowed = NULL;
            $this->outputAllowed = NULL;

        }
   
        function get(){

            global $database;
            global $simpleRest;

            $response = $database->query("SELECT json FROM getControllerPortsUnused WHERE controllerId = " . $this->controllerId . ";");

            if(count(json_decode($response)) > 0){
                
                return($response);
        
            }else{
                throw new Exception(NULL, 404);
                return;
            }

        }
        
    }

?>