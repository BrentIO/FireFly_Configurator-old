<?php

class database{

    /* Performs low-level database SELECT and CALLs */

    private $dsn;
    private $conn;

    function __construct(){

        $configFile = file_get_contents("/var/www/config.json");
        $config = json_decode($configFile, true);

        $this->conn = mysqli_connect($config['server'], $config['username'], $config['password'], $config['database']);

    }


    function __destruct(){

        //Clean up the connection
        mysqli_close($this->conn);
    }


    private function minify($json){

        return json_encode(json_decode($json), JSON_UNESCAPED_SLASHES);
    }


    public function query($sql, $includeColumnName = true, $encodeToJson = true){

        /* Queries the data from SQL and returns a JSON object (if only one entry) or array (if multiple entries) */

        $result = mysqli_query($this->conn, $sql);

        //Get the number of rows to determine if we should return an object or an array
        if(mysqli_num_rows($result) == 1) {

            //If we only have one column and we don't need the column name (field contains JSON)
            if(mysqli_num_fields($result) == 1 && $includeColumnName == false){

                //Just get the row
                $r = mysqli_fetch_row($result);
    
                //See if we need to encode it or can send the row data raw
                if($encodeToJson == true){

                    //Encode the row data
                    return json_encode($r[0], JSON_UNESCAPED_SLASHES);
                }else{

                    //Row data must already include JSON, just send it as-is
                    return $r[0];
                }
                

            }else{

                //We have more than one field OR we requested the field name to be included in the object response
                $r = mysqli_fetch_object($result);

                $rows[] = $r;

                //We at least a field and a value, they must always be encoded
                return json_encode($r, JSON_UNESCAPED_SLASHES);
            
            }

        }else{

            //0 or > 1 rows, return an array
            $rows = array();

            if($includeColumnName == true){
                $sqlMode = MYSQLI_ASSOC;
            }else{                
                $sqlMode = MYSQLI_NUM;
            }

            //Flip through every row in the result
            while($r = mysqli_fetch_array($result, $sqlMode)) {

                
                if($includeColumnName == true){

                    $rows[] = $r;

                }else{
                        
                    //If we are not including column names, the data must be JSON, decode it so that it can be encoded properly
                    $rows[] = json_decode($r[0]);

                }

            }

            return json_encode($rows);
            
        }
    }


    public function callProcedure($sql, $variables = NULL){

        try {

            $prepare = mysqli_prepare($this->conn, $sql);

            //Make sure the variables is an array and not null
            if(is_array($variables)){

                //For each parameter passed as key=>value, append it to the request
                foreach($variables as $key => $value){
                    mysqli_stmt_bind_param($prepare, $key, $value);
                }
            }
        
            if($prepare == false){
                throw new Exception ('Query invalid');
            }
            
            //Returns true if the statement was successful
            return mysqli_stmt_execute($prepare);

        }

        catch (Exception $e){
            //Return false
            return false;

        }

    }
}

?>