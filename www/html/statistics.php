<?php

    require_once('./api/getConfig.php');
    require_once('common.php');

    //Validate the user is logged in
    checkLogin();

    $pageName = "statistics";
    $url = "http://" . $_SERVER['SERVER_ADDR'] . "/api/statistic";
    $controllerUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/controller";

?>
<!DOCTYPE html>
<html>
    <head>
        <title>FireFly Configurator - Statistics</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="jquery.toaster.js"></script>
        <script>

            function breakersPopulateCards(data){

                $.each(data, function(i){

                    //Determine the bar color
                    switch(true){
                        case (data[i].utilization < 70):
                            progressBarColor = "ok";
                        break;

                        case (70 < data[i].utilization &&  80 > data[i].utilization):
                            progressBarColor = "warning";
                        break;

                        case (data[i].utilization > 80):
                            progressBarColor = "danger progress-bar-striped progress-bar-animated active";
                        break;

                        default:
                            progressBarColor = "";
                        break;
                    };

                    html = "<div class=\"breakerStatistic\"><div class=\"breakerStatisticContent\">"
                            + "<span class=\"breakerName\">" + data[i].displayName + "</span><br><br>"
                            + "<div class=\"progress\">"
                            + "<div class=\"progress-bar " + progressBarColor + "\" role=\"progressbar\" style=\"width: " + data[i].utilization + "%\">" + data[i].utilization + "%</div><span class=\"progressRemaining\">" + (data[i].amperage - data[i].amperageUsed) + "</span>"
                            + "</div>"
                            + "Total Amps: " + data[i].amperage + "<br>Used Amps: " + data[i].amperageUsed
                            + "</div></div>";

                    $('.breakerStatisticBox').append(html);
                });
            }

            function outputsPopulateTable(data){
                $.each(data, function(i){

                    outputType = "";

                    switch(data[i].outputType.toUpperCase()){

                        case "BINARY".toUpperCase():
                            outputType = "Binary";
                        break;

                        case "VARIABLE".toUpperCase():
                            outputType = "Variable";
                        break;

                        default:
                            outputType = "Unknown";
                        break;
                    }
                    
                    trHTML = "<tr>"
                                + "<td>" + outputType + "</td>"
                                + "<td>" + data[i].count + "</td>"
                            +"</tr>"
                    $('#outputTypes').append(trHTML);
                });
            }

            function controllerCountPopulateTable(data){
                $.each(data, function(i){

                    trHTML = "<tr>"
                                + "<td>Version " + data[i].hwVersion + "</td>"
                                + "<td>" + data[i].count + "</td>"
                            +"</tr>"
                    $('#controllerCount').append(trHTML);
                    });

            }

            function getProgressBarColor(value, maximum){

                var returnValue = {
                    usagePercent : 0,
                    color : ""
                };

                //Determine the percentage of usage
                returnValue.usagePercent = Math.round((value/maximum)*100);

                switch(true){

                    case(returnValue.usagePercent < 80):
                        returnValue.color = "ok";
                    break;

                    case(returnValue.usagePercent > 80 && returnValue.usagePercent < 90):
                        returnValue.color = "warning";
                    break;

                    default:
                        returnValue.color = "danger";
                    break;
                }

                return returnValue;

            }

            function controllerPopulateCards(controllerData, pinData, portData){
                
                $.each(controllerData, function(i){

                    var displayData = {

                        inputPort : {
                            available : 0,
                            assigned : 0,
                            total : 0
                        },
                        outputPort : {
                            available : 0,
                            assigned : 0,
                            total : 0
                        },
                        inputPin : {
                            available : 0,
                            assigned : 0,
                            total : 0
                        },
                        binaryOutputPin : {
                            available : 0,
                            assigned : 0,
                            total : 0
                        },
                        variableOutputPin : {
                            available : 0,
                            assigned : 0,
                            total : 0
                        }
                    }

                    //Get the ports for the given controller
                    $.each(portData, function(j){

                        if(portData[j].controllerId == controllerData[i].id){

                            $.each(portData[j].ports, function(k){

                                if(portData[j].ports[k].type.toUpperCase() == "INPUT"){

                                    switch(portData[j].ports[k].status.toUpperCase()){

                                        case "ASSIGNED":
                                            displayData.inputPort.assigned = portData[j].ports[k].count;
                                        break;

                                        case "AVAILABLE":
                                            displayData.inputPort.available = portData[j].ports[k].count;
                                        break;

                                        case "TOTAL":
                                            displayData.inputPort.total = portData[j].ports[k].count;
                                        break;
                                    }                                  

                                }else if(portData[j].ports[k].type.toUpperCase() == "OUTPUT"){

                                    switch(portData[j].ports[k].status.toUpperCase()){

                                        case "ASSIGNED":
                                            displayData.outputPort.assigned = portData[j].ports[k].count;
                                        break;

                                        case "AVAILABLE":
                                            displayData.outputPort.available = portData[j].ports[k].count;
                                        break;

                                        case "TOTAL":
                                            displayData.outputPort.total = portData[j].ports[k].count;
                                        break;
                                    }
                                }
                            });
                        };
                    });

                    //Get the pins for the given controller
                    $.each(pinData, function(j){

                        if(pinData[j].controllerId == controllerData[i].id){
                    

                            $.each(pinData[j].pins, function(k){

                                if(pinData[j].pins[k].type.toUpperCase() == "INPUT"){

                                    switch(pinData[j].pins[k].utilization.toUpperCase()){

                                        case "ASSIGNED":
                                            displayData.inputPin.assigned = pinData[j].pins[k].count;
                                        break;

                                        case "AVAILABLE":
                                            displayData.inputPin.available = pinData[j].pins[k].count;
                                        break;

                                        case "TOTAL":
                                            displayData.inputPin.total = pinData[j].pins[k].count;
                                        break;
                                    }                                  

                                }else if(pinData[j].pins[k].type.toUpperCase() == "BINARY_OUTPUT"){

                                    switch(pinData[j].pins[k].utilization.toUpperCase()){

                                        case "ASSIGNED":
                                            displayData.binaryOutputPin.assigned = pinData[j].pins[k].count;
                                        break;

                                        case "AVAILABLE":
                                            displayData.binaryOutputPin.available = pinData[j].pins[k].count;
                                        break;

                                        case "TOTAL":
                                            displayData.binaryOutputPin.total = pinData[j].pins[k].count;
                                        break;
                                    }                                  

                                } else if(pinData[j].pins[k].type.toUpperCase() == "VARIABLE_OUTPUT"){

                                    switch(pinData[j].pins[k].utilization.toUpperCase()){

                                        case "ASSIGNED":
                                            displayData.variableOutputPin.assigned = pinData[j].pins[k].count;
                                        break;

                                        case "AVAILABLE":
                                            displayData.variableOutputPin.available = pinData[j].pins[k].count;
                                        break;

                                        case "TOTAL":
                                            displayData.variableOutputPin.total = pinData[j].pins[k].count;
                                        break;
                                    }                                  
                                } 
                            });
                        };

                    });

                    //console.log(getProgressBarColor(10, 75));

                    html = "<div class=\"controllerStatistic\">"
                            + "<div class=\"controllerStatisticContent\">"
                                + "<span class=\"controllerName\">"+ controllerData[i].displayName +"</span><br>"
                                + "<div class=\"portPinBox\">"
                                    + "<div class=\"portPinHeader\">Ports</div>"
                                    + "<div style=\"text-align: left;\">Inputs</div>"
                                    + "<div class=\"progress\">"
                                        + "<div class=\"progress-bar " + getProgressBarColor(displayData.inputPort.assigned, displayData.inputPort.total).color + "\" role=\"progressbar\" style=\"width: "+ getProgressBarColor(displayData.inputPort.assigned, displayData.inputPort.total).usagePercent +"%\">"+ getProgressBarColor(displayData.inputPort.assigned, displayData.inputPort.total).usagePercent + "%</div>"
                                        + "<span class=\"progressRemaining\">" + displayData.inputPort.available + "</span>"
                                    + "</div>"
                                    + "<div style=\"text-align: left;\">Outputs</div>"
                                    + "<div class=\"progress\" >"
                                        + "<div class=\"progress-bar " + getProgressBarColor(displayData.outputPort.assigned, displayData.outputPort.total).color + "\" role=\"progressbar\" style=\"width: "+ getProgressBarColor(displayData.outputPort.assigned, displayData.outputPort.total).usagePercent +"%\">"+ getProgressBarColor(displayData.outputPort.assigned, displayData.outputPort.total).usagePercent + "%</div>"
                                        + "<span class=\"progressRemaining\">" + displayData.outputPort.available + "</span>"
                                    + "</div>"
                                + "</div>"
                                + "<br>"
                                + "<div class=\"portPinBox\">"
                                    + "<div class=\"portPinHeader\">Pins</div>"
                                    + "<div style=\"text-align: left; \">Inputs</div>"
                                    + "<div class=\"progress\">"
                                        + "<div class=\"progress-bar " + getProgressBarColor(displayData.inputPin.assigned, displayData.inputPin.total).color + "\" role=\"progressbar\" style=\"width: "+ getProgressBarColor(displayData.inputPin.assigned, displayData.inputPin.total).usagePercent +"%\">"+ getProgressBarColor(displayData.inputPin.assigned, displayData.inputPin.total).usagePercent + "%</div>"
                                        + "<span class=\"progressRemaining\">" + displayData.inputPin.available + "</span>"
                                + "</div>"
                                + "<div style=\"text-align: left;\">Binary Outputs</div>"
                                + "<div class=\"progress\" >"
                                    + "<div class=\"progress-bar " + getProgressBarColor(displayData.binaryOutputPin.assigned, displayData.binaryOutputPin.total).color + "\" role=\"progressbar\" style=\"width: "+ getProgressBarColor(displayData.binaryOutputPin.assigned, displayData.binaryOutputPin.total).usagePercent +"%\">"+ getProgressBarColor(displayData.binaryOutputPin.assigned, displayData.binaryOutputPin.total).usagePercent + "%</div>"
                                    + "<span class=\"progressRemaining\">" + displayData.binaryOutputPin.available + "</span>"
                                + "</div>"
                                + "<div style=\"text-align: left;\">Variable Outputs</div>"
                                + "<div class=\"progress\" >"
                                    + "<div class=\"progress-bar " + getProgressBarColor(displayData.variableOutputPin.assigned, displayData.variableOutputPin.total).color + "\" role=\"progressbar\" style=\"width: "+ getProgressBarColor(displayData.variableOutputPin.assigned, displayData.variableOutputPin.total).usagePercent +"%\">"+ getProgressBarColor(displayData.variableOutputPin.assigned, displayData.variableOutputPin.total).usagePercent + "%</div>"
                                    + "<span class=\"progressRemaining\">" + displayData.variableOutputPin.available + "</span>"
                                + "</div>"
                            + "</div>"
                            + "<br>"
                            + "Hardware Version: " + controllerData[i].hwVersion
                        + "</div>"
                    + "</div>"

                    $('.controllerStatisticBox').append(html);
                });

            }

            function buttonColorCountPopulateTable(data){
                $.each(data, function(i){

                    trHTML = "<tr>"
                                + "<td style=\"text-align:left;\"><span style=\"width: 30px; height: 30px; margin:auto; display: inline-block; border: 1px solid gray; vertical-align: middle; border-radius: 2px; background: " + data[i].hexValue + "\"></span> " + data[i].color + "</td>"
                                + "<td>" + data[i].count + "</td>"
                            +"</tr>"
                    $('#buttonColorCount').append(trHTML);
                });

            }

            function switchCountPopulateTable(data){
                $.each(data, function(i){

                    trHTML = "<tr>"
                                + "<td>Version " + data[i].hwVersion + "</td>"
                                + "<td>" + data[i].count + "</td>"
                            +"</tr>"
                    $('#switchCount').append(trHTML);
                    });

            }

            function faceplateCountPopulateTable(data){
                $.each(data, function(i){

                    if(data[i].positions == 1){
                        inputLabel = "Input";

                    }else{
                        inputLabel = "Inputs";

                    }

                    trHTML = "<tr>"
                                + "<td>" + data[i].positions + " " + inputLabel + "</td>"
                                + "<td>" + data[i].quantity + "</td>"
                            +"</tr>"
                    $('#faceplateCount').append(trHTML);
                    });

            }

            $(document).ready(function(){

                $.toaster({ settings : {'donotdismiss' : ['danger']  }});

                breakerData = null;
                outputTypeData = null;
                controllerData = null;
                controllerCount = null;
                controllerPinUtilization = null;
                controllerPortUtilization = null;
                switchCount = null;
                buttonColorCount = null;
                faceplateCount = null;

                $.when(

                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>" + '/breakerUtilization',

                        success: function(data) {
                            breakerData = data;
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Breaker Utilization', message : data['responseJSON']['error']});
                        }
                    }),

                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($controllerUrl);?>",

                        success: function(data) {
                            controllerData = data;
                            
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Controller Data', message : data['responseJSON']['error']});
                        }
                    }),
                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>" + '/outputType',

                        success: function(data) {
                            outputTypeData = data;
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Output Type', message : data['responseJSON']['error']});
                        }
                    }),
                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>" + '/controllerCount',

                        success: function(data) {
                            controllerCount = data;
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Controller Count', message : data['responseJSON']['error']});
                        }
                    }),
                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>" + '/controllerPinUtilization',

                        success: function(data) {
                            controllerPinUtilization = data;
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Controller Pin Utilization', message : data['responseJSON']['error']});
                        }
                    }),
                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>" + '/controllerPortUtilization',

                        success: function(data) {
                            controllerPortUtilization = data;
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Controller Port Utilization', message : data['responseJSON']['error']});
                        }
                    }),
                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>" + '/switchCount',

                        success: function(data) {
                            switchCount = data;
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Switch Count', message : data['responseJSON']['error']});
                        }
                    }),
                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>" + '/buttonColorCount',

                        success: function(data) {
                            buttonColorCount = data;
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Button Color Count', message : data['responseJSON']['error']});
                        }
                    }),
                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>" + '/faceplateCount',

                        success: function(data) {
                            faceplateCount = data;
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed Loading Faceplate Count', message : data['responseJSON']['error']});
                        }
                    }),


                ).then(function(){
                    breakersPopulateCards(breakerData);
                    outputsPopulateTable(outputTypeData);
                    controllerCountPopulateTable(controllerCount);
                    switchCountPopulateTable(switchCount);
                    buttonColorCountPopulateTable(buttonColorCount);
                    faceplateCountPopulateTable(faceplateCount);
                    controllerPopulateCards(controllerData, controllerPinUtilization, controllerPortUtilization);

                    document.getElementById("spinner").style.visibility = "hidden";

                });

            });

        </script>
    </head>
    <body>

<?php include "menu.php"?>

        <div class="content">
            <div id="pageName">
                <div id="pageTitle">Statistics</div>
            </div>

            <div class="spinner" id="spinner"></div>

            <div class="sub-header">Breakers</div>
            <span class="breakerStatisticBox">     
            </span>
            <br>
            <div class="sub-header">Outputs</div>
            <table id="outputTypes" class="dataTable" style="margin-left: 20px;">
                <tbody>
                <tr>
                    <th>Type</th>
                    <th>Quantity</th>
                </tr>
                </tbody>
            </table>
            <br>

            <div class="sub-header">Controllers</div>
            <table id="controllerCount" class="dataTable" style="margin-left: 20px;">
                <tbody>
                <tr>
                    <th>Hardware</th>
                    <th>Quantity</th>
                </tr>
                </tbody>
            </table>
            <br>
            <span class="controllerStatisticBox">
            </span>
            <br>

            <div class="sub-header">Switches</div>
            <br>
            <table id="switchCount" class="dataTable" style="margin-left: 20px;">
                <tbody>
                <tr>
                    <th>Hardware</th>
                    <th>Quantity</th>
                </tr>
                </tbody>
            </table>
            <br>
            <table id="faceplateCount" class="dataTable" style="margin-left: 20px;">
                <tbody>
                <tr>
                    <th>Number of Positions</th>
                    <th>Quantity</th>
                </tr>
                </tbody>
            </table>
            <br>
            <table id="buttonColorCount" class="dataTable" style="margin-left: 20px;">
                <tbody>
                <tr>
                    <th>Button Color</th>
                    <th>Quantity</th>
                </tr>
                </tbody>
            </table>
            <br>
        </div>
    </body>
</html>