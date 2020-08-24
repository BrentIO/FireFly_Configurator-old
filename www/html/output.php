<?php

    require_once('./api/getConfig.php');
    require_once('common.php');

    //Validate the user is logged in
    checkLogin();

    $pageName = "output";
    $url = "http://" . $_SERVER['SERVER_ADDR'] . "/api/output";
    $controllerURL = "http://" . $_SERVER['SERVER_ADDR'] . "/api/controller";
    $breakerURL = "http://" . $_SERVER['SERVER_ADDR'] . "/api/breaker";

?>
<!DOCTYPE html>
<html>
    <head>
        <title>FireFly Configurator - Outputs</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="jquery.toaster.js"></script>
        <script>        

            function setPortHidden(value){
                var editItemForm = document.editItem;
                editItemForm.elements["port"].hidden = value;

                if(value == true){

                    document.getElementById('portLabel').style.visibility = 'hidden';

                }else{
                    document.getElementById('portLabel').style.visibility = 'visible';
                }
                            
            }

            function setPinHidden(value){
                var editItemForm = document.editItem;
                editItemForm.elements["pin"].hidden = value;

                if(value == true){

                    document.getElementById('pinLabel').style.visibility = 'hidden';

                }else{
                    document.getElementById('pinLabel').style.visibility = 'visible';
                }
                            
            }

            function controllerChanged(){

                var editItemForm = document.editItem;
                
                if(editItemForm.elements["controllerId"].value == editItemForm.elements["controllerId"].getAttribute("data-default")){
                    //The current value was re-selected
                    editItemForm.elements["portAutoAssign"].checked = false;
                    editItemForm.elements["portAutoAssign"].disabled = false;
                    editItemForm.elements["pinAutoAssign"].checked = false;
                    editItemForm.elements["pinAutoAssign"].disabled = false;
                    setPortHidden(false);
                    setPinHidden(false);
                }else{
                    //A different value was selected, block the user from manually assigning the pin and port
                    editItemForm.elements["portAutoAssign"].checked = true;
                    editItemForm.elements["portAutoAssign"].disabled = true;
                    editItemForm.elements["pinAutoAssign"].checked = true;
                    editItemForm.elements["pinAutoAssign"].disabled = true;
                    setPortHidden(true);
                    setPinHidden(true);
                }                
            }

            $(document).ready(function(){

                $.toaster({ settings : {'donotdismiss' : ['danger']  }});

                class output {
                    id = null;
                    name = null;
                    displayName = null;
                    controllerId = null;
                    port = null;
                    pin = null;
                    outputType = null;
                    breakerId = null;
                    amperage = null;
                    enabled = null;
                }

                class controller {
                    id = null;
                    displayName = null;
                }

                class breaker {
                    id = null;
                    displayName = null;
                }
           
                //Load the data on page load
                loadTableData();

                $('#modalEditItem').on('show.bs.modal', function (event) {
                    /*************************************************
                    **  Handle the edit item modal being requested  **
                    *************************************************/

                    //Retrieve the data associated to the button press event
                    var button = $(event.relatedTarget);
                    var operation = button.data('operation');

                    //Clear existing input
                    destroyEditData();

                    var editItemForm = document.editItem;

                    if(operation == "add"){

                        //Set the modal title
                        document.getElementById("operation").innerHTML= "Add New Output";
                        
                        $.when(

                            //Get the list of controllers
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($controllerURL);?>",

                                success: function(data) {

                                    //Add the list of controllers to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\">" + data[i].displayName + "</option>";
                                    
                                        $('#controllerId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Controllers Failed', message : data['responseJSON']['error']});
                                }
                            }),

                            //Get the list of breakers
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($breakerURL);?>",

                                success: function(data) {
                                    
                                    //Add the list of breakers to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\">" + data[i].displayName + "</option>";
                                    
                                        $('#breakerId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Breakers Failed', message : data['responseJSON']['error']});
                                }
                            })

                        ).then(function(){

                            editItemForm.elements["portAutoAssign"].checked = true;
                            editItemForm.elements["portAutoAssign"].disabled = true;
                            editItemForm.elements["pinAutoAssign"].checked = true;
                            editItemForm.elements["pinAutoAssign"].disabled = true;
                            setPortHidden(true);
                            setPinHidden(true);

                        });

                    }

                    //If editing, retrieve the data from the API
                    if(operation == "edit"){

                        //Set the modal title
                        document.getElementById("operation").innerHTML= "Edit Existing Output";

                        outputData = null;

                        $.when(

                            //Get the data about this ID
                            $.ajax({

                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },

                                type: 'GET',
                                url: "<?php print($url);?>" + '/' + button.data('uniqueid'),

                                success: function(data) {

                                    outputData = data;
                                },

                                error: function(data){
                                    $.toaster({ priority :'danger', title :'Failed', message : data['responseJSON']['error']});
                                },
                            }),

                            //Get the list of controllers
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($controllerURL);?>",

                                success: function(data) {

                                    //Add the list of controllers to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\">" + data[i].displayName + "</option>";
                                    
                                        $('#controllerId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Controllers Failed', message : data['responseJSON']['error']});
                                }
                            }),

                            //Get the list of breakers
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($breakerURL);?>",

                                success: function(data) {
                                    
                                    //Add the list of breakers to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\">" + data[i].displayName + "</option>";
                                    
                                        $('#breakerId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Breakers Failed', message : data['responseJSON']['error']});
                                }
                            })

                        ).then(function(){

                                    //Populate the field elements with the data returned by the API
                                    editItemForm.elements["uniqueId"].value = outputData['id']; 
                                    editItemForm.elements["name"].value = outputData['name']; 
                                    editItemForm.elements["displayName"].value = outputData['displayName']; 
                                    editItemForm.elements["controllerId"].value = outputData['controllerId'];
                                    editItemForm.elements["controllerId"].setAttribute("data-default", outputData['controllerId']);
                                    editItemForm.elements["port"].value = outputData['port'];
                                    editItemForm.elements["pin"].value = outputData['pin'];
                                    editItemForm.elements["outputType"].value = outputData['outputType'];
                                    editItemForm.elements["breakerId"].value = outputData['breakerId'];
                                    editItemForm.elements["amperage"].value = outputData['amperage'];
                                    editItemForm.elements["enabled"].checked = outputData['enabled'];
                        });

                    }
                });


                $(document).on('click', '#buttonSave', function(event){

                    var editItemForm = document.editItem;

                    elementName = editItemForm.elements["name"];

                    var item = new output();

                    item.id = editItemForm.elements["uniqueId"].value;
                    item.name = editItemForm.elements["name"].value;
                    item.displayName = editItemForm.elements["displayName"].value;
                    item.controllerId = editItemForm.elements["controllerId"].value;
                    item.outputType = editItemForm.elements["outputType"].value;
                    item.breakerId = editItemForm.elements["breakerId"].value;
                    item.amperage = editItemForm.elements["amperage"].value;
                    item.enabled = editItemForm.elements["enabled"].checked;

                    if(editItemForm.elements["portAutoAssign"].checked){
                        item.port = null;
                    }else{
                        item.port = editItemForm.elements["port"].value;
                    }

                    if(editItemForm.elements["pinAutoAssign"].checked){
                        item.pin = null;
                    }else{
                        item.pin = editItemForm.elements["pin"].value;
                    }

                    if(item.id == ""){
                        item.id = null;
                    }

                    //Send the item to the API
                    editItem(item);

                    //Hide the modal
                    $("#modalEditItem").modal("hide");

                });


                $('#modalDeleteItem').on('show.bs.modal', function (event) {
                    /*************************************************
                    **  Handle the delete item modal being requested**
                    *************************************************/

                    //Retrieve the data associated to the button press event
                    var button = $(event.relatedTarget);
                    var displayName = button.data('displayname');

                    //Clear existing input
                    destroyDeleteData();

                    var deleteItemForm = document.deleteItem;

                    //Set the hidden input value to the ID
                    deleteItemForm.elements["uniqueId"].value = button.data('uniqueid');

                    //Create the prompt text
                    var prompt = "Are you sure you wish to delete $DISPLAYNAME$?";

                    //Build the prompt
                    prompt = prompt.replace("$DISPLAYNAME$", displayName);

                    //Replace any double-spaces
                    prompt = prompt.replace("  ", "");

                    //Set the prompt text on the modal
                    document.getElementById("deletePrompt").innerHTML= prompt;            

                });


                $(document).on('click', '#buttonDelete', function(event){

                    var deleteItemForm = document.deleteItem;

                    var item = new output;

                    item.id = deleteItemForm.elements["uniqueId"].value;

                    //Delete the item
                    deleteItem(item);

                    //Hide the modal
                    $("#modalDeleteItem").modal("hide");

                });


                function destroyEditData(){
                    /*************************************************
                    **  Destroys the data on the edit form          **
                    *************************************************/

                    //Get the form inputs
                    var editItemForm = document.editItem;

                    //For each input
                    for(var i = 0; i < editItemForm.length; i++){
                        
                        //Clear the data
                        editItemForm[i].value = null;
                    }

                    //Delete the controller drop-down options
                    while(document.getElementById("controllerId").length > 0){
                        document.getElementById("controllerId").remove(0);
                    }

                    //Delete the breaker drop-down options
                    while(document.getElementById("breakerId").length > 0){
                        document.getElementById("breakerId").remove(0);
                    }

                    //Set enabled to true by default
                    editItemForm.elements["enabled"].checked = true;

                    //Set binary as the default
                    editItemForm.elements["outputType"].value = "BINARY";

                    //Re-enabled the form elements
                    editItemForm.elements["portAutoAssign"].checked = false;
                    editItemForm.elements["portAutoAssign"].disabled = false;
                    editItemForm.elements["pinAutoAssign"].checked = false;
                    editItemForm.elements["pinAutoAssign"].disabled = false;
                    setPortHidden(false);
                    setPinHidden(false);

                };


                function destroyDeleteData(){
                    /*************************************************
                    **  Destroys the data on the delete form          **
                    *************************************************/

                    //Get the form inputs
                    var deleteItemForm = document.deleteItem;

                    //For each input
                    for(var i = 0; i < deleteItemForm.length; i++){
                        
                        //Clear the data
                        deleteItemForm[i].value = null;
                    }

                };


                function editItem(item){
                    /*************************************************
                    **  Adds new or edits existing entries based on **
                    **      the ID passed in                        **
                    *************************************************/

                    if(item.id == null){
                        var method="POST";
                        var url = "<?php print($url);?>";
                    }else{
                        var method="PATCH";
                        var url = "<?php print($url);?>" + '/' + item.id;
                    }

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        data: JSON.stringify(item),
                        type: method,
                        url: url,

                        success: function(data) {
                            $.toaster({ priority :'success', title :'Edit', message : 'Successful'});
                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Failed', message : data['responseJSON']['error']});
                        },

                        complete: function(){
                            loadTableData();
                        }

                    });

                }


                function deleteItem(item){
                    /*************************************************
                    **  Loads the specified ID from the database    **
                    *************************************************/

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        type: 'DELETE',
                        url: "<?php print($url);?>" + '/' + item.id,

                        success: function(data) {
                            $.toaster({ priority :'success', title :'Deletion', message : 'Successful'});
                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Failed', message : data['responseJSON']['error']});
                        },

                        complete: function(){
                            loadTableData();                    
                        }

                    });
                }


                function loadTableData(){
                    /*************************************************
                    **  Loads the data from the API into the data   **
                    **      table for viewing and editing           **
                    *************************************************/

                    //Delete the existing contents of the table
                    $('.dynamic').remove();

                    //Load the table
                    $.ajax({
                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                        },
                        url: "<?php print($url);?>",

                        success: function(data) {
                            $.each(data, function(i){

                                if(data[i].enabled == true){
                                    outputEnabled = "Yes";
                                }else{
                                    outputEnabled = "No";
                                }

                                switch(data[i].outputType){

                                    case "BINARY":
                                        outputType = "Binary";
                                        break;

                                    case "VARIABLE":
                                        outputType = "Variable";
                                        break;

                                    break;

                                    default:
                                        outputType = "Unknown";
                                        break;
                                }

                                trHTML = "<tr class=\"dynamic\">"
                                            + "<td>" + data[i].displayName + "</td>"
                                            + "<td>" + data[i].controllerDisplayName + "</td>"
                                            + "<td>" + outputType + "</td>"
                                            + "<td>" + data[i].port + "</td>"
                                            + "<td>" + data[i].pin + "</td>"
                                            + "<td>" + data[i].amperage + "</td>"
                                            + "<td>" + outputEnabled + "</td>"
                                            + "<td><button class=\"btn btn-default\" data-toggle=\"modal\" data-target=\"#modalEditItem\" data-backdrop=\"static\" data-operation=\"edit\" data-uniqueid=\"" + data[i].id + "\">Edit</button>"
                                                + "<button class=\"btn btn-danger\" data-toggle=\"modal\" data-target=\"#modalDeleteItem\" data-backdrop=\"static\" data-displayname=\"" + data[i].displayName + "\"  id=\"deleteButton\" data-uniqueid=\"" + data[i].id + "\">Delete</button>"
                                            + "</td>"
                                        +"</tr>"
                                $('#dynamicData').append(trHTML);
                            });
                        },

                        fail: function(data){
                            $.toaster({ priority :'danger', title :'Failed', message : data['responseJSON']['error']});
                        }
                    });
                };
            
            });

        </script>
    </head>
    <body>

<?php include "menu.php"?>
        
        <div class="content">
        <div id="pageName">
            <div id="pageTitle">Outputs</div><button data-toggle="modal" data-target="#modalEditItem" data-backdrop="static" data-operation="add" class="btn btn-success">Add New</button>
        </div>

        <table class="dataTable" id="dynamicData">
            <tbody>
            <tr>
                <th>Name</th>
                <th>Controller</th>
                <th>Type</th>
                <th>Port</th>
                <th>Pin</th>
                <th>Amperage</th>
                <th>Enabled</th>
                <th>Operations</th>
            </tr>
            </tbody>
        </table>


        <!-- Edit Item Modal -->
        <div class="modal fade" id="modalEditItem" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><div id="operation"></div></h4>
                    </div>
                    <div class="modal-body" name="form">
                        <form name="editItem">
                            <input type="hidden" id="uniqueId">
                            <label for="name">Name:</label>
                            <input type="text" id="name"><br><br>
                            <label for="displayName">Display Name:</label>
                            <input type="text" id="displayName"><br><br>
                            <label for="controllerId">Controller:</label>
                            <select id="controllerId" oninput="controllerChanged()"></select><br><br>
                            <label for="portAutoAssign">Auto-Assign Port</label>
                            <input type="checkbox" id="portAutoAssign" onchange="setPortHidden(this.checked)">
                            <label for="port" id="portLabel">Port:</label>
                            <input type="text" id="port" size="4" readonly><br><br>
                            <label for="pinAutoAssign">Auto-Assign Pin</label>
                            <input type="checkbox" id="pinAutoAssign" onchange="setPinHidden(this.checked)">
                            <label for="pin" id="pinLabel">Pin:</label>
                            <input type="text" id="pin" size="4" readonly><br><br>
                            <label for="outputType">Type:</label>
                            <select id="outputType">
                                <option value="BINARY">Binary</option>
                                <option value="VARIABLE">Variable</option>
                            </select><br><br>
                            <label for="breakerId">Breaker:</label>
                            <select id="breakerId"></select><br><br>
                            <label for="amperage">Amperage:</label>
                            <input type="number" id="amperage" size="4" min="0" max="100"><br><br>
                            <label for="enabled">Enabled:</label>
                            <input type="checkbox" id="enabled">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="buttonSave">Save</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Delete Item Modal -->
        <div class="modal fade" id="modalDeleteItem" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Delete</h4>
                    </div>
                    <form name="deleteItem">
                        <input type="hidden" id="uniqueId">
                    </form>
                    <div class="modal-body"><div id="deletePrompt"></div></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="buttonDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </body>
</html>