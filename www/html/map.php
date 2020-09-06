<?php

    require_once('./api/getConfig.php');
    require_once('common.php');

    //Validate the user is logged in
    checkLogin();

    $pageName = "connectivityMap";
    $breakerUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/statistic/breakerUtilization";
    $outputUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/output";
    $controllerUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/controller";
    $switchUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/switch";
    $inputUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/input";
    $actionUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/action";

?>
<!DOCTYPE html>
<html>
    <head>
        <title>FireFly Configurator - Connectivity Map</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="jquery.toaster.js"></script>
        <script>        

            $(document).ready(function(){

                $.toaster({ settings : {'donotdismiss' : ['danger']  }});

                breakerData = null;
                outputData = null;
                controllerData = null;
                inputData = null;
                switchData = null;
                inputActions = null;

                $.when(

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },

                        type: 'GET',
                        url: "<?php print($breakerUrl);?>",

                        success: function(data) {

                            breakerData = data;                   
                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Failed to Retrieve Breakers', message : data['responseJSON']['error']});
                        }
                    }),

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },

                        type: 'GET',
                        url: "<?php print($outputUrl);?>",

                        success: function(data) {

                            outputData = data.sort((a, b) => (a.port > b.port) ? 1 : -1);                
                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Failed to Retrieve Outputs', message : data['responseJSON']['error']});
                        }
                    }),

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },

                        type: 'GET',
                        url: "<?php print($controllerUrl);?>",

                        success: function(data) {

                            controllerData = data;                   
                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Failed to Retrieve Controllers', message : data['responseJSON']['error']});
                        }
                    }),

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },

                        type: 'GET',
                        url: "<?php print($switchUrl);?>",

                        success: function(data) {
  
                            switchData = data.sort((a, b) => (a.controllerPort > b.controllerPort) ? 1 : -1);             
                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Failed to Retrieve Switches', message : data['responseJSON']['error']});
                        }
                    }),

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },

                        type: 'GET',
                        url: "<?php print($inputUrl);?>",

                        success: function(data) {

                             inputData = data.sort((a, b) => (a.port > b.port) ? 1 : -1);              
                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Failed to Retrieve Inputs', message : data['responseJSON']['error']});
                        }
                    }),

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },

                        type: 'GET',
                        url: "<?php print($actionUrl);?>",

                        success: function(data) {

                            actionData = data;              
                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Failed to Retrieve Actions', message : data['responseJSON']['error']});
                        }
                    })


                ).then(function(){

                    document.getElementById("electricalMap").innerHTML= "<ul id=\"electricalMapView\">";
                    document.getElementById("controlMap").innerHTML = "<ul id=\"controlMapView\">";

                    /* Paint the Electrical Map */
                    $.each(breakerData, function(i){

                        if(breakerData[i].utilization >= 80){
                            utilizationPercentText = "<br><span class=\"highUtilization\">Utilization: " + breakerData[i].amperageUsed + " Amps (" + breakerData[i].utilization + "%)</span>";
                        }else{
                            utilizationPercentText = "<br>Utilization: " + breakerData[i].amperageUsed + " Amps (" + breakerData[i].utilization + "%)";
                        }

                        liHTML = "<li><a href=\"#\">(" + breakerData[i].name + ") " + breakerData[i].displayName + "<div class=\"breakerDetail\">Capacity: " + breakerData[i].amperage + " Amps" + utilizationPercentText + "</div></a>"

                        circuitCount = 0;
                        innerLiHTML = "";

                        //Get the list of each output associated to the current breaker
                        $.each(outputData, function(j){

                            if(outputData[j].breakerId == breakerData[i].id){

                                //If this is the first circuit for this breaker, add the list data
                                if(circuitCount == 0){
                                    innerLiHTML = innerLiHTML + "<ul>";
                                }

                                switch(outputData[j].outputType){

                                    case "BINARY":
                                        className = "badge badge-outputBinary";
                                        label = "Binary";
                                    break;

                                    case "VARIABLE":
                                        className = "badge badge-outputVariable";
                                        label = "Variable";
                                    break;
                                        
                                    default:
                                        className = "badge";
                                        label = "Unknown";
                                    break;

                                }

                                if(outputData[j].enabled != true){
                                    statusText = "<div class=\"outputDisabled\">Disabled</div>";
                                }else{
                                    statusText = "";
                                }

                                innerLiHTML = innerLiHTML + "<li><a href=\"#\">(" + outputData[j].name + ") " + outputData[j].displayName + "<div class=\"outputDetail\"><span class=\""+className+"\">" + label + "</span><br>Amperage: " + outputData[j].amperage + " Amps<br>" + statusText + "</div></a>";
                                innerLiHTML = innerLiHTML + "<ul><li><a href=\"#\">" + outputData[j].controllerDisplayName + "<div class=\"controllerDetail\">Port: " + outputData[j].port + "<br>Pin: " + outputData[j].pin + "</div></a></li></ul></li>";
                                circuitCount = circuitCount + 1;

                            }

                        });

                        if(circuitCount > 0){
                            innerLiHTML = innerLiHTML + "</ul>";
                        }

                        liHTML = liHTML + innerLiHTML;
                        liHTML = liHTML + "</li>";
                        
                        $('#electricalMapView').append(liHTML);

                    });

                    document.getElementById("electricalMap").innerHTML = document.getElementById("electricalMap").innerHTML + "</ul>";        

                    /* Paint the Control Map */
                    $.each(controllerData, function(i){

                        liHTML = "<li><a href=\"#\">" + controllerData[i].displayName + "</a>";

                        controllerChildCount = 0;
                        innerLiHTML = "";

                        //innerLiHTML = innerLiHTML + "<li><a href=\"#\">Inputs</a><ul>";

                        //Get the list of switches attached to this controller
                        $.each(switchData, function(j){

                            if(switchData[j].controllerId == controllerData[i].id){

                                //If this is the first input for this controller, add the list data
                                if(controllerChildCount == 0){
                                    innerLiHTML = innerLiHTML + "<ul>";
                                }

                                innerLiHTML = innerLiHTML + "<li><a href=\"#\">(" + switchData[j].name + ") " + switchData[j].displayName + "<div class=\"inputDetail\">Controller Port: " + switchData[j].controllerPort + "</div></a>";

                                inputCount = 0;

                                

                                //Get the list of inputs for this switch
                                $.each(inputData, function(k){

                                    if(inputData[k].switchId == switchData[j].id){
                                        
                                        if(inputCount == 0){
                                            innerLiHTML = innerLiHTML + "<ul>";
                                        }

                                        innerLiHTML = innerLiHTML + "<li><a href=\"#\">Position " + inputData[k].port  + " <span style=\"width: 15px; height: 15px; margin:auto; display: inline-block; border: 0.5px solid gray; vertical-align: middle; border-radius: 50%; background: " + inputData[k].hexValue + ";\"></span><div class=\"inputDetail\">Controller Pin: " + inputData[k].pin + "</div></a>";

                                        actionCount = 0;

                                        //Get the list of actions for this input
                                        $.each(actionData, function(m){

                                            if(actionData[m].inputId == inputData[k].id){

                                                if(actionCount == 0){
                                                    innerLiHTML = innerLiHTML + "<ul>";
                                                }

                                                switch(actionData[m].actionType){
                                                    case "INCREASE":
                                                        actionType = "Increase";
                                                        break;

                                                    case "DECREASE":
                                                        actionType = "Decrease";
                                                        break;

                                                    case "TOGGLE":
                                                        actionType = "Toggle";
                                                        break;

                                                    default:
                                                        actionType = "Unknown";
                                                        break;
                                                }

                                                innerLiHTML = innerLiHTML + "<li><a href=\"#\">(" + actionData[m].outputName + ") " + actionData[m].outputDisplayName + " <div class=\"actionDetail\">Action: " + actionType + "</div></a></li>";

                                                actionCount = actionCount + 1;
                                            }

                                        });

                                        if(actionCount > 0){
                                            innerLiHTML = innerLiHTML + "</ul>";
                                        }

                                        innerLiHTML = innerLiHTML + "</li>"

                                        inputCount = inputCount + 1;

                                    }

                                });

                                if(inputCount > 0){
                                    innerLiHTML = innerLiHTML + "</ul>";
                                }

                    
                                controllerChildCount = controllerChildCount + 1;
                                innerLiHTML = innerLiHTML + "</li>"

                            }

                            
                        });

                        //innerLiHTML = innerLiHTML + "</ul></li>";
                        innerLiHTML = innerLiHTML + "<li><a href=\"#\">Outputs</a><ul>";

                        //Get the list of outputs attached to this controller
                        $.each(outputData, function(j){

                            if(outputData[j].controllerId == controllerData[i].id){

                                switch(outputData[j].outputType){

                                    case "BINARY":
                                        className = "badge badge-outputBinary";
                                        label = "Binary";
                                    break;

                                    case "VARIABLE":
                                        className = "badge badge-outputVariable";
                                        label = "Variable";
                                    break;
                                        
                                    default:
                                        className = "badge";
                                        label = "Unknown";
                                    break;

                                }

                                innerLiHTML = innerLiHTML + "<li><a href=\"#\">(" + outputData[j].name + ") " + outputData[j].displayName + "<div class=\"outputDetail\"><span class=\""+className+"\">" + label + "</span><br>Controller Port: " + outputData[j].port + "</div></a>";

                                actionCount = 0;

                                //Get the list of actions for this input
                                $.each(actionData, function(m){

                                    if(actionData[m].outputId == outputData[j].id){

                                        if(actionCount == 0){
                                            innerLiHTML = innerLiHTML + "<ul>";
                                        }

                                        switch(actionData[m].actionType){
                                            case "INCREASE":
                                                actionType = "Increase";
                                                break;

                                            case "DECREASE":
                                                actionType = "Decrease";
                                                break;

                                            case "TOGGLE":
                                                actionType = "Toggle";
                                                break;

                                            default:
                                                actionType = "Unknown";
                                                break;
                                        }

                                        innerLiHTML = innerLiHTML + "<li><a href=\"#\">" + actionData[m].switchDisplayName + " (" + actionData[m].port  + ")<div class=\"actionDetail\">Input: " + actionData[m].inputDisplayName + " <span style=\"width: 10px; height: 10px; margin:auto; display: inline-block; border: 0.5px solid gray; vertical-align: middle; border-radius: 50%; background: " + actionData[m].hexValue + ";\"></span><br>Action: " + actionType + "</div></a></li>";

                                        actionCount = actionCount + 1;
                                    }

                                });

                                if(actionCount > 0){
                                    innerLiHTML = innerLiHTML + "</ul>";
                                }
                                controllerChildCount = controllerChildCount + 1;
                                innerLiHTML = innerLiHTML + "</li>"
                            }

                        });

                        innerLiHTML = innerLiHTML + "</ul></li>";

                        liHTML = liHTML + innerLiHTML;
                        liHTML = liHTML + "</li>";

                        $('#controlMapView').append(liHTML);

                    });

                    document.getElementById("controlMapView").innerHTML = document.getElementById("controlMapView").innerHTML + "</ul>"; 
                    document.getElementById("spinner").style.visibility = "hidden";

                });

            });

        </script>
    </head>
    <body>

<?php include "menu.php"?>
        
        <div class="content">
        <div class="spinner" id="spinner"></div>
            <div id="pageName">
                <div id="pageTitle">Connectivity Map</div>
            </div>
                <div class="sub-header">Electrical Map</div>
                <div class="tree" id="electricalMap"></div>
                <br>
                <div class="sub-header">Control Map</div>
                <div class="tree" id="controlMap"></div>
            </div>
        </div>
    </body>
</html>