<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    $simpleRest = new simpleRest();
    $breaker = new breaker();

    try {

        $database = new database();

        //Always assume success
        $simpleRest->setHttpHeaders(200);

        #Retrieve the data from the query string
        if(isset($_GET['id']) && $_GET['id'] != "" && is_numeric($_GET['id']) == True){

            $breaker->id = $_GET['id'];

        }else{
            $breaker->id = NULL;
        }

        if(isset($_GET['name'])){
            $breaker->name = $_GET['name'];
        }

        if(isset($_GET['displayName'])){
            $breaker->displayName = $_GET['displayName'];
        }

        if(isset($_GET['amperage'])){
            $breaker->amperage = intval($_GET['amperage']);
        }

        switch(strtolower($_SERVER['REQUEST_METHOD'])){

            case "post":

                $breaker->edit();

                print($breaker->get());                       

            break;

            case "get":

                #See if the user is attempting to get one or many ID's
                if($breaker->id != NULL){

                    #Get the specific ID requested
                    print($breaker->get());


                }else{
                    //The user wants a list
                    print($breaker->list());

                }

            break;

            case "delete":

                //Make sure we have an ID to delete
                if($breaker->id == NULL){
                    throw new Exception("No ID specified in query string for deletion", 400);
                }

                //Check to make sure the procedure was successful
                if($breaker->delete() != true)
                {
                    //Unknown error occurred, because it wasn't caught by the delete function
                    throw new Exception("Unexpected response during deletion", 500);
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

    class breaker{

        public $id;
        public $name;
        public $displayName;
        public $amperage;

        function __construct(){

            $this->id = NULL;
            $this->name = "";
            $this->displayName = "";
            $this->amperage = 0;

        }

        function list(){

            global $database;
            global $simpleRest;

            $breakerResponse = $database->query("SELECT json FROM getBreakers;", false, false);

            if(is_array($breakerResponse) == False){
                
                ////The response from the database is <= 1 row, convert it to an array
                if($breakerResponse == "[]"){

                    //Empty array
                    $breakerResponse = array();
                }
                else{
                    
                    //Temporary variable that will contain a single object of data
                    $tmpArray[0] = json_decode($breakerResponse);                   
                    $breakerResponse =  array();
                    $breakerResponse = $tmpArray;
                }
            }

            if($breakerResponse){           
                return(json_encode($breakerResponse));
        
            }else{
                return(json_encode($breakerResponse));

            }
        }
    
        function get(){

            global $database;
            global $simpleRest;

            if($this->id){

                $breakerResponse = $database->query("SELECT json FROM getBreakers WHERE id = " . $this->id . ";");
            }elseif($this->name != ""){

                $breakerResponse = $database->query("SELECT json FROM getBreakers WHERE name = '" . $this->name . "';");
            }

            if(is_array(json_decode($breakerResponse)) == False){
                       
                return($breakerResponse);
        
            }else{
                throw new Exception(NULL, 404);
                return;
            }

        }
    
        function edit(){

            global $database;
            global $simpleRest;

            $this->id = intval($this->id);

            #If the data passed in is not number, assume null
            if($this->id == 0){
                $this->id = NULL;
            }

            //Build the variables
            $variables = array($this->id, $this->name, $this->displayName, $this->amperage);
            $varTypes = "issi";

            //Perform the creation
            $database->callProcedure("CALL editBreaker(?,?,?,?)", $varTypes, $variables);

            //Handle the number of affected rows correctly
            switch($database->rowsAffected){

                case 0:

                    //Nothing was edited
                    return $this->id;

                break;

                case 1:

                    //Get the ID from the database if we don't already know it
                    if($this->id == NULL){
                        $this->id = $database->id;
                    }
                    
                    //Return the record id to the caller
                    return $this->id;

                break;

                case -1:
                    throw new Exception("Unable to update; Validate name is unique", 409);
                break;
            }

        }

        function delete(){

            global $database;
            global $simpleRest;

            //Perform the deletion
            $database->callProcedure("CALL deleteBreaker(" . $this->id . ")");

            //Handle the number of affected rows correctly
            switch($database->rowsAffected){

                case 0:
                    throw new Exception(NULL, 404);
                break;

                case 1:
                    return true;
                break;

                case -1:
                    throw new Exception("Unable to delete; Validate there are no dependent links", 409);
                break;

                default:
                    //We should always be deleting exactly 1 row, so if we have another number, we are in trouble
                    throw new Exception("Multiple rows deleted, expected 1", 400);
                break;
            }

        }
    }

?>