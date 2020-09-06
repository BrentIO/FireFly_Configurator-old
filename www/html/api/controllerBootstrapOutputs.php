<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    //Do not require auth for this page
    $simpleRest = new simpleRest(false);
    $controllerBootstrapOutputs = new controllerBootstrapOutputs();

    $_GET_lower = array_change_key_case($_GET, CASE_LOWER);

    try {

        $database = new database();

        //Always assume success
        $simpleRest->setHttpHeaders(200);

        //Populate the object with a preferene for the URL rather than the payload
        if(isset($_GET_lower['id'])){

            $controllerBootstrapOutputs->id = $_GET_lower['id'];
        }
        
        switch(strtolower($_SERVER['REQUEST_METHOD'])){

            case "get":

                #See if the user is attempting to get one or many ID's
                if($controllerBootstrapOutputs->id != NULL){

                    #Get the specific ID requested
                    print($controllerBootstrapOutputs->get());
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

    class controllerBootstrapOutputs{

        public $id;

        function __construct(){

            $this->id = NULL;
        }
    
        function get(){

            global $database;
            global $simpleRest;

            if($this->id != NULL){
                $response = $database->query("SELECT json FROM getBootstrapOutputs WHERE id = " . $this->id . ";");
                
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