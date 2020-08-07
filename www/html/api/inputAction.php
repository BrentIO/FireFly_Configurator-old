<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    $simpleRest = new simpleRest();
    $action = new action();
    
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
        if(isset($_GET_lower['inputid']) && $_GET_lower['inputid'] != "" && is_numeric($_GET_lower['inputid']) == True){

            $action->inputId = intval($_GET_lower['inputid']);

        }
  
        switch(strtolower($_SERVER['REQUEST_METHOD'])){

            case "get":

                //The user wants a list
                print($action->list());

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

    class action{

        public $id;
        public $inputId;
        public $outputId;
        public $actionType;
 
        function __construct(){

            $this->id = NULL;
            $this->inputId = NULL;
            $this->outputId = NULL;
            $this->actionType = NULL;
        }

  
        function list(){

            global $database;
            global $simpleRest;
            
            if($this->inputId != NULL){
                $response = $database->query("SELECT json FROM getInputActions WHERE inputId = " . $this->inputId . ";");
            }else{
                throw new Exception(NULL, 400);
            }
            
            if(is_array(json_decode($response)) == True){
                       
                return($response);
        
            }else{
                throw new Exception(NULL, 404);
                return;
            }

        }
    }

?>