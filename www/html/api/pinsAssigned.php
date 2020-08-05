<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    $simpleRest = new simpleRest();
    $pin = new pin();

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

            $pin->controllerId = intval($_GET_lower['controllerid']);

        }

        switch(strtolower($_SERVER['REQUEST_METHOD'])){

            case "get":

                #See if the user is attempting to get one or many ID's
                if($pin->controllerId != NULL && is_numeric($pin->controllerId) && $pin->controllerId !=0){

                    #List the specific controller ID requested
                    print($pin->list());

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

    class pin{

        public $pin;
        public $controllerId;
        public $inputAllowed;
        public $binaryOutputAllowed;
        public $variableOutputAllowed;


        function __construct(){

            $this->pin = NULL;
            $this->controllerId = NULL;
            $this->inputAllowed = NULL;
            $this->binaryOutputAllowed = NULL;
            $this->variableOutputAllowed = 0;

        }

        function list(){

            global $database;
            global $simpleRest;

            $response = $database->query("SELECT json FROM getControllerPinsUsed WHERE controllerId = " . $this->controllerId . ";");

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
    }

?>