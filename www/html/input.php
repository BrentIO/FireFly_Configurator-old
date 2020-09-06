<?php

    require_once('./api/getConfig.php');
    require_once('common.php');

    //Validate the user is logged in
    checkLogin();

    $pageName = "input";
    $url = "http://" . $_SERVER['SERVER_ADDR'] . "/api/input";
    $switchURL = "http://" . $_SERVER['SERVER_ADDR'] . "/api/switch";
    $buttonColorURL = "http://" . $_SERVER['SERVER_ADDR'] . "/api/buttonColor";

?>
<!DOCTYPE html>
<html>
    <head>
        <title>FireFly Configurator - Inputs</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="jquery.toaster.js"></script>
        <script>

            function updateColorSwatch(hexValue){
                var colorSwatch = document.getElementById('colorSwatch');
                colorSwatch.style.background = hexValue;
            }

            function switchChanged(){
                var editItemForm = document.editItem;

                if(editItemForm.elements["switchId"].value == editItemForm.elements["switchId"].getAttribute("data-default")){
                    //The current value was re-selected
                    editItemForm.elements["pinAutoAssign"].checked = false;
                    editItemForm.elements["pinAutoAssign"].disabled = false;
                    setPinHidden(false);
                }else{
                    //A different value was selected, block the user from manually assigning the pin and port
                    editItemForm.elements["pinAutoAssign"].checked = true;
                    editItemForm.elements["pinAutoAssign"].disabled = true;
                    setPinHidden(true);

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

            $(document).ready(function(){

                $.toaster({ settings : {'donotdismiss' : ['danger']  }});

                class input {
                    id = null;
                    displayName = null;
                    switchId = null;
                    port = null;
                    pin = null;
                    colorId = null;
                    circuitType = null;
                    broadcastOnChange = null;
                    enabled = null;
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
                        document.getElementById("operation").innerHTML= "Add New Input";

                        $.when(

                            //Get the list of switches
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($switchURL);?>",

                                success: function(data) {

                                    data = data.sort((a, b) => (a.name > b.name) ? 1 : -1);

                                    //Add the default selection
                                    optionHTML="<option selected disabled>Select...</option>";

                                    $('#switchId').append(optionHTML);

                                    //Add the list of switches to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\">(" + data[i].name + ") "  + data[i].displayName + "</option>";
                                    
                                        $('#switchId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Switches Failed', message : data['responseJSON']['error']});
                                }
                            }),

                            //Get the list of colors
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($buttonColorURL);?>",

                                success: function(data) {
                                    
                                    //Add the list of colors to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\" data-hexvalue=\"" + data[i].hexValue + "\">" + data[i].displayName + "</option>";
                                    
                                        $('#colorId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Button Colors Failed', message : data['responseJSON']['error']});
                                }
                            })

                        ).then(function(){
                                editItemForm.elements["switchId"].setAttribute("data-default", null);
                                editItemForm.elements["enabled"].checked = true;
                                editItemForm.elements["broadcastOnChange"].checked = true;
                                editItemForm.elements["pinAutoAssign"].checked = true;
                                editItemForm.elements["pinAutoAssign"].disabled = true;
                                setPinHidden(true);
                                editItemForm.elements["colorId"].selectedIndex = 0;
                                updateColorSwatch(editItemForm.elements["colorId"].options[editItemForm.elements["colorId"].selectedIndex].getAttribute('data-hexvalue'));
                        });

                    }

                    //If editing, retrieve the data from the API
                    if(operation == "edit"){

                        inputData = null;

                        //Set the modal title
                        document.getElementById("operation").innerHTML= "Edit Existing Input";

                        $.when(

                            //Get the data about this ID
                            $.ajax({

                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },

                                type: 'GET',
                                url: "<?php print($url);?>" + '/' + button.data('uniqueid'),

                                success: function(data) {

                                    inputData = data
                                },

                                error: function(data){
                                    $.toaster({ priority :'danger', title :'Failed', message : data['responseJSON']['error']});
                                },
                            }),

                            //Get the list of switches
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($switchURL);?>",

                                success: function(data) {

                                    data = data.sort((a, b) => (a.name > b.name) ? 1 : -1);

                                    //Add the list of switches to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\">(" + data[i].name + ") " + data[i].displayName + "</option>";
                                    
                                        $('#switchId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Switches Failed', message : data['responseJSON']['error']});
                                }
                            }),

                            //Get the list of colors
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($buttonColorURL);?>",

                                success: function(data) {
                                    
                                    //Add the list of colors to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\" data-hexvalue=\"" + data[i].hexValue + "\">" + data[i].displayName + "</option>";
                                    
                                        $('#colorId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Button Colors Failed', message : data['responseJSON']['error']});
                                }
                            })

                        ).then(function(){

                            //Populate the field elements with the data returned by the API
                            editItemForm.elements["uniqueId"].value = inputData['id'];
                            editItemForm.elements["displayName"].value = inputData['displayName'];
                            editItemForm.elements["port"].value = inputData['port'];
                            editItemForm.elements["colorId"].value = inputData['colorId'];
                            updateColorSwatch(inputData['hexValue']);
                            editItemForm.elements["switchId"].value = inputData['switchId'];
                            editItemForm.elements["switchId"].setAttribute("data-default", inputData['switchId']);
                            editItemForm.elements["circuitType"].value = inputData['circuitType'];
                            editItemForm.elements["pin"].value = inputData['pin'];
                            editItemForm.elements["broadcastOnChange"].checked = inputData['broadcastOnChange'];
                            editItemForm.elements["enabled"].checked = inputData['enabled'];
                        });

                    }

                });


                $(document).on('click', '#buttonSave', function(event){

                    var editItemForm = document.editItem;

                    elementName = editItemForm.elements["name"];

                    var item = new input();

                    item.id = editItemForm.elements["uniqueId"].value;
                    item.displayName = editItemForm.elements["displayName"].value;
                    item.switchId = editItemForm.elements["switchId"].value;
                    item.port = editItemForm.elements["port"].value;
                    
                    item.colorId = editItemForm.elements["colorId"].value;
                    item.circuitType = editItemForm.elements["circuitType"].value;
                    item.broadcastOnChange = editItemForm.elements["broadcastOnChange"].checked;
                    item.enabled = editItemForm.elements["enabled"].checked;

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

                    var item = new input;

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

                    //Delete the color drop-down options
                    while(document.getElementById("colorId").length > 0){
                        document.getElementById("colorId").remove(0);
                    }

                    //Delete the color drop-down options
                    while(document.getElementById("switchId").length > 0){
                        document.getElementById("switchId").remove(0);
                    }

                    //Set enabled to true by default
                    editItemForm.elements["enabled"].checked = true;

                    //Set binary as the default
                    editItemForm.elements["circuitType"].value = "NORMALLY_OPEN";

                    //Re-enabled the form elements
                    editItemForm.elements["pinAutoAssign"].checked = false;
                    editItemForm.elements["pinAutoAssign"].disabled = false;
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

                            data = data.sort((a, b) => (a.displayName > b.displayName) ? 1 : -1);

                            $.each(data, function(i){

                                switch(data[i].circuitType){
                                    case "NORMALLY_OPEN":
                                        circuitType = "Normally Open";
                                        break;

                                    case "NORMALLY_CLOSED":
                                        circuitType = "Normally Closed";
                                        break;

                                    default:
                                    circuitType = "Unknown";
                                        break;
                                }

                                if(data[i].broadcastOnChange){
                                    broadcastOnChange = "Yes";
                                }else{
                                    broadcastOnChange = "No";
                                }

                                if(data[i].enabled){
                                    enabled = "Yes";
                                }else{
                                    enabled = "No";
                                }

                                trHTML = "<tr class=\"dynamic\">"
                                            + "<td>" + data[i].switchDisplayName + " (position " + data[i].port + ")</td>"
                                            + "<td><span style=\"width: 15px; height: 15px; margin:auto; display: inline-block; border: 0.5px solid gray; vertical-align: middle; border-radius: 50%; background: " + data[i].hexValue + "\"></span> </td>"
                                            + "<td>" + data[i].displayName + "</td>"
                                            + "<td>" + data[i].controllerDisplayName + " (port " + data[i].controllerPort + ", pin " + data[i].pin + ")</td>"
                                            + "<td>" + circuitType + "</td>"
                                            + "<td>" + broadcastOnChange + "</td>"
                                            + "<td>" + enabled + "</td>"
                                            + "<td><button class=\"btn btn-default\" data-toggle=\"modal\" data-target=\"#modalEditItem\" data-backdrop=\"static\" data-operation=\"edit\" data-uniqueid=\"" + data[i].id + "\">Edit</button>"
                                                + "<button class=\"btn btn-danger\" data-toggle=\"modal\" data-target=\"#modalDeleteItem\" data-backdrop=\"static\" data-displayname=\"" + data[i].displayName + "\" data-switchdisplayname=\"" + data[i].switchDisplayName + "\"  id=\"deleteButton\" data-uniqueid=\"" + data[i].id + "\">Delete</button>"
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
            <div id="pageTitle">Inputs</div><button data-toggle="modal" data-target="#modalEditItem" data-backdrop="static" data-operation="add" class="btn btn-success">Add New</button>
        </div>

        <table class="dataTable" id="dynamicData">
            <tbody>
            <tr>
                <th colspan="2">Switch</th>
                <th>Name</th>
                <th>Controller</th>
                <th>Circuit Type</th>
                <th>Broadcast on Change</th>
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
                            <label for="displayName">Display Name:</label>
                            <input type="text" id="displayName"><br><br>
                            <label for="port">Position:</label>
                            <select name="port">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                                <option value="F">F</option>
                            </select><br><br>
                            <label for="colorId">Button LED Color:</label>
                            <select id="colorId" onchange="updateColorSwatch(this.options[this.selectedIndex].getAttribute('data-hexvalue'))"></select>
                            <span id="colorSwatch" style="width: 20px; height: 20px; margin:auto; display: inline-block; border: 0.5px solid gray; vertical-align: middle; border-radius: 25%; background: transparent"></span><br><br>
                            <label for="switchId">Switch:</label>
                            <select id="switchId" oninput="switchChanged()"></select><br><br>
                            <label for="circuitType">Circuit Type:</label>
                            <select id="circuitType">
                                <option value="NORMALLY_OPEN">Normally Open</option>
                                <option value="NORMALLY_CLOSED">Normally Closed</option>
                            </select><br><br>
                            <label for="pinAutoAssign">Auto-Assign Controller Pin</label>
                            <input type="checkbox" id="pinAutoAssign" onchange="setPinHidden(this.checked)">
                            <label for="pin" id="pinLabel">Pin:</label>
                            <input type="text" id="pin" size="4" readonly><br><br>
                            <label for="broadcastOnChange">Broadcast on Change:</label>
                            <input type="checkbox" id="broadcastOnChange"><br><br>
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