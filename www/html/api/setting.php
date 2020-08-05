<?php
    
    require_once('getConfig.php');
    require_once('simpleRest.php');
    require_once('database.php');

    $simpleRest = new simpleRest();
    $setting = new setting();

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
        if(isset($_GET_lower['id']) && $_GET_lower['id'] != "" && is_numeric($_GET_lower['id']) == True){

            $setting->id = intval($_GET_lower['id']);

        }else{

            if(isset($data['id'])){
                $setting->id = $data['id'];
            }   

        }

        //Populate the object from the payload of the body
        if(isset($data['name'])){
            $setting->name = $data['name'];
        }

        if(isset($data['displayName'])){
            $setting->displayName = $data['displayName'];
        }

        if(isset($data['value'])){
            $setting->value = $data['value'];
        }

        switch(strtolower($_SERVER['REQUEST_METHOD'])){

            case "patch":

                //Make sure we have an ID to edit
                if($setting->id == NULL || $setting->id == 0){
                    throw new Exception("No ID specified to PATCH", 400);
                }

                $setting->edit();

                print($setting->get());      
                
            break;

            case "get":

                #See if the user is attempting to get one or many ID's
                if($setting->id != NULL){

                    #Get the specific ID requested
                    print($setting->get());

                }else{
                    //The user wants a list
                    print($setting->list());

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

    class setting{

        public $id;
        public $name;
        public $displayName;
        public $value;


        function __construct(){

            $this->id = NULL;
            $this->name = NULL;
            $this->displayName = NULL;
            $this->value = 0;

        }

        function list(){

            global $database;
            global $simpleRest;

            $response = $database->query("SELECT json FROM getSettings;");

            //Return the list from SQL
            return($response);

        }
    
        function get(){

            global $database;
            global $simpleRest;

            if($this->id){
                $response = $database->query("SELECT json FROM getSettings WHERE id = " . $this->id . ";");

            }elseif($this->name != ""){
                $response = $database->query("SELECT json FROM getSettings WHERE name = '" . $this->name . "';");
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
            $variables = array($this->id, $this->value);
            $varTypes = "is";

            //Perform the creation
            $database->callProcedure("CALL editSetting(?,?)", $varTypes, $variables);

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
            $database->callProcedure("CALL deleteSetting(" . $this->id . ")");

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