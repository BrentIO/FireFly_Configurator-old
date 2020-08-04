<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    $simpleRest = new simpleRest();
    $controller = new controller();

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

        //Populate the controller object with a preferene for the URL rather than the payload
        if(isset($_GET_lower['id']) && $_GET_lower['id'] != "" && is_numeric($_GET_lower['id']) == True){

            $controller->id = intval($_GET_lower['id']);

        }else{

            if(isset($data['id'])){
                $controller->id = $data['id'];
            }   

        }
 
        //Populate the object from the payload of the body
        if(isset($data['name'])){
            $controller->name = $data['name'];
        }

        if(isset($data['displayName'])){
            $controller->displayName = $data['displayName'];
        }

        if(isset($data['macAddress'])){
            $controller->macAddress = $data['macAddress'];
        }

        if(isset($data['ipAddress'])){
            $controller->ipAddress = $data['ipAddress'];
        }

        if(isset($data['subnet'])){
            $controller->subnet = $data['subnet'];
        }

        if(isset($data['dns'])){
            $controller->dns = $data['dns'];
        }

        if(isset($data['gateway'])){
            $controller->gateway = $data['gateway'];
        }

        if(isset($data['mqttUsername'])){
            $controller->mqttUsername = $data['mqttUsername'];
        }

        if(isset($data['mqttPassword'])){
            $controller->mqttPassword = $data['mqttPassword'];
        }

        if(isset($data['hwVersion'])){
            $controller->hwVersion = $data['hwVersion'];
        }

        switch(strtolower($_SERVER['REQUEST_METHOD'])){

            case "post":

                $controller->id = NULL;

                $controller->edit();

                print($controller->get());        

            break;

            case "patch":

                //Make sure we have an ID to edit
                if($controller->id == NULL || $controller->id == 0){
                    throw new Exception("No ID specified for patching", 400);
                }

                $controller->edit();

                print($controller->get());      
                
            break;

            case "get":

                #See if the user is attempting to get one or many ID's
                if($controller->id != NULL || $controller->macAddress != NULL){

                    #Get the specific ID requested
                    print($controller->get());

                }else{

                    //The user wants a list
                    print($controller->list());

                }

            break;

            case "delete":

                //Make sure we have an ID to delete
                if($controller->id == NULL){
                    throw new Exception("No ID specified for deletion", 400);
                }

                //Check to make sure the procedure was successful
                if($controller->delete() != true)
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

    class controller{

        public $id;
        public $name;
        public $displayName;
        public $macAddress;
        public $ipAddress;
        public $subnet;
        public $dns;
        public $gateway;
        public $mqttUsername;
        public $mqttPassword;
        public $hwVersion;


        function __construct(){

            $this->id = NULL;
            $this->name = NULL;
            $this->displayName = NULL;
            $this->macAddress = NULL;
            $this->ipAddress = NULL;
            $this->subnet = NULL;
            $this->dns = NULL;
            $this->gateway = NULL;
            $this->mqttUsername = NULL;
            $this->mqttPassword = NULL;
            $this->hwVersion = NULL;

        }

        function list(){

            global $database;
            global $simpleRest;

            $response = $database->query("SELECT json FROM getControllers;");

            //Return the list from SQL
            return($response);

        }
    
        function get(){

            global $database;
            global $simpleRest;

            if($this->id){
                $response = $database->query("SELECT json FROM getControllers WHERE id = " . $this->id . ";");

            }elseif($this->macAddress != ""){
                $response = $database->query("SELECT json FROM getControllers WHERE macAddress = '" . $this->macAddress . "';");
            }
            
            if(is_array(json_decode($response)) == False){
                       
                return($response);
        
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
            $variables = array($this->id, $this->macAddress, $this->name, $this->displayName, $this->ipAddress, $this->subnet, $this->dns, $this->gateway, $this->mqttUsername, $this->mqttPassword, $this->hwVersion);
            $varTypes = "isssssssssi";

            //Perform the creation
            $database->callProcedure("CALL editController(?,?,?,?,?,?,?,?,?,?,?)", $varTypes, $variables);

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
            $database->callProcedure("CALL deleteController(" . $this->id . ")");

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