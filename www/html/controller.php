<?php

    require_once('./api/getConfig.php');
    require_once('common.php');

    //Validate the user is logged in
    checkLogin();

    $pageName = "controller";
    $url = "http://" . $_SERVER['SERVER_ADDR'] . "/api/controller";
    $bootstrapURL = "http://" . $_SERVER['SERVER_ADDR'] . "/api/controller/{deviceName}/bootstrap";

?>
<!DOCTYPE html>
<html>
    <head>
        <title>FireFly Configurator - Controllers</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js" type="text/javascript"></script>
        <script src="bootstrap.min.js" type="text/javascript"></script>
        <script src="jquery.toaster.js" type="text/javascript"></script>
        <script src="mqtt.min.js" type="text/javascript"></script>
        <script>

            function setMQTTUsernameHidden(value){
                var editItemForm = document.editItem;
                editItemForm.elements["mqttUsername"].hidden = value;

                if(value == true){

                    document.getElementById('mqttUsernameLabel').style.visibility = 'hidden';

                }else{
                    document.getElementById('mqttUsernameLabel').style.visibility = 'visible';
                }
                
            }

            function setMQTTPasswordHidden(value){
                var editItemForm = document.editItem;
                editItemForm.elements["mqttPassword"].hidden = value;
                
                if(value == true){

                    document.getElementById('mqttPasswordLabel').style.visibility = 'hidden';

                }else{
                    document.getElementById('mqttPasswordLabel').style.visibility = 'visible';
                }
            }

            function bootstrapDevice(macAddress){

                deviceName = macAddress.replaceAll(":","");

                //Get the bootstrap data
                var bootstrapURL = "<?php print($bootstrapURL); ?>"
                bootstrapURL = bootstrapURL.replace("{deviceName}", deviceName);
                provisionData = "";

                //Get the bootstrap from the server
                $.ajax({

                    type: 'GET',
                    url: bootstrapURL,

                    success: function(data) {

                        var options = {
                            username: data.mqtt.username.replaceAll("$DEVICENAME$", deviceName),
                            password: data.mqtt.password.replaceAll("$DEVICENAME$", deviceName),
                            port: 9001,
                            clientId: 'webclient_' + Math.random().toString(16).substr(2, 8),
                            clean: true,
                        }

                        //Connect to MQTT with the device's credentials
                        var mqttClient = mqtt.connect("ws://" + data.mqtt.serverName + "", options);

                        mqttClient.on('error', function (err) {
                            $.toaster({ priority :'danger', title :'MQTT Connection', message : err});
                            mqttClient.end();
                        });

                        mqttClient.publish(data.mqtt.topics.client.replaceAll("$DEVICENAME$", deviceName) + "/bootstrap/set", bootstrapURL, function(){

                            //Close connection to MQTT
                            mqttClient.end();

                            //Display success message to user
                            $.toaster({ priority :'success', title :'Bootstrap Request Sent', message : 'Successful'}); 

                        });

    

                    },

                    error: function(data){
                        $.toaster({ priority :'danger', title :'Retrieval Error', message : data['responseJSON']['error']});
                    },

                });
            }

            $(document).ready(function(){

                $.toaster({ settings : {'donotdismiss' : ['danger']  }});

                class controller {
                    id = null;
                    name = null;
                    displayName = null;
                    macAddress = null;
                    ipAddress = null;
                    subnet = null;
                    dns = null;
                    gateway = null;
                    mqttUsername = null;
                    mqttPassword = null;
                    hwVersion = null;
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
                        document.getElementById("operation").innerHTML= "Add New Controller";
                        editItemForm.elements["hwVersion"].value = "2"; 
                        editItemForm.elements["mqttUsernameDefault"].checked = true;
                        editItemForm.elements["mqttPasswordDefault"].checked = true;
                        setMQTTUsernameHidden(true);
                        setMQTTPasswordHidden(true);                        

                    }

                    //If editing, retrieve the data from the API
                    if(operation == "edit"){

                        //Set the modal title
                        document.getElementById("operation").innerHTML= "Edit Existing Controller";

                        $.ajax({

                            beforeSend: function(request) {
                                request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                            },

                            type: 'GET',
                            url: "<?php print($url);?>" + '/' + button.data('uniqueid'),

                            success: function(data) {

                                //Populate the field elements with the data returned by the API
                                editItemForm.elements["uniqueId"].value = data['id'];
                                editItemForm.elements["name"].value = data['name'];
                                editItemForm.elements["displayName"].value = data['displayName'];
                                editItemForm.elements["macAddress"].value = data['macAddress'];
                                editItemForm.elements["ipAddress"].value = data['ipAddress'];
                                editItemForm.elements["subnet"].value = data['subnet'];
                                editItemForm.elements["dns"].value = data['dns'];
                                editItemForm.elements["gateway"].value = data['gateway'];
                                editItemForm.elements["hwVersion"].value = data['hwVersion'];

                                editItemForm.elements["mqttUsernameDefault"].checked = data['mqttUsernameDefault'];
                                setMQTTUsernameHidden(data['mqttUsernameDefault']);
                                editItemForm.elements["mqttUsername"].value = data['mqttUsername'];

                                editItemForm.elements["mqttPasswordDefault"].checked = data['mqttPasswordDefault'];
                                setMQTTPasswordHidden(data['mqttPasswordDefault']);
                                editItemForm.elements["mqttPassword"].value = data['mqttPassword'];

                            },

                            error: function(data){
                                $.toaster({ priority :'danger', title :'Failed', message : data['responseJSON']['error']});
                            },
                        });
                    }
                });


                $(document).on('click', '#buttonSave', function(event){

                    var editItemForm = document.editItem;

                    elementName = editItemForm.elements["name"];

                    var item = new controller();

                    item.id = editItemForm.elements["uniqueId"].value;
                    item.name = editItemForm.elements["name"].value;
                    item.displayName = editItemForm.elements["displayName"].value;
                    item.macAddress = editItemForm.elements["macAddress"].value;
                    item.ipAddress = editItemForm.elements["ipAddress"].value;
                    item.subnet = editItemForm.elements["subnet"].value;
                    item.dns = editItemForm.elements["dns"].value;
                    item.gateway = editItemForm.elements["gateway"].value;
                    item.hwVersion = editItemForm.elements["hwVersion"].value;

                    if(editItemForm.elements["mqttUsernameDefault"].checked){
                        item.mqttUsername = null;
                    }else{
                        item.mqttUsername = editItemForm.elements["mqttUsername"].value;
                    }

                    if(editItemForm.elements["mqttPasswordDefault"].checked){
                        item.mqttPassword = null;
                    }else{
                        item.mqttPassword = editItemForm.elements["mqttPassword"].value;
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

                    var item = new controller;

                    item.id = deleteItemForm.elements["uniqueId"].value;

                    //Delete the item
                    deleteItem(item);

                    //Hide the modal
                    $("#modalDeleteItem").modal("hide");

                });

                function closeModalAfterProvisioning(){
                    //Hide the modal
                    $("#modalProvisioning").modal("hide");
                }

                $('#modalProvisioning').on('show.bs.modal', function (event) {
                    /*************************************************
                    **  Handle the Provisioning modal being requested**
                    *************************************************/

                    //Retrieve the data associated to the button press event
                    var button = $(event.relatedTarget);
                    var displayName = button.data('displayname');

                    //Clear existing input
                    destroyProvisioningData();

                    var provisionForm = document.provisionForm;

                    //Set the hidden input value to the ID
                    provisionForm.elements["deviceName"].value = button.data('devicename');
                    provisionForm.elements["deviceName"].value = provisionForm.elements["deviceName"].value.replaceAll(":","");

                    //Set the prompt text on the modal
                    document.getElementById("controllerDisplayName").innerHTML = "Provision " + displayName;            

                });

                function postProvisioningToController(ipAddress, password, payload){

                    $.ajax({

                        beforeSend: function(request) {
                            request.setRequestHeader("x-api-key", password);
                        },
                        
                        type: 'POST',
                        url: "http://" + ipAddress + "/provision",
                        data: JSON.stringify(payload),

                        success: function(data) {

                            closeModalAfterProvisioning();                        
                            $.toaster({ priority :'success', title :'Provisioning Sent', message : 'Successful'});

                        },

                        error: function(data){

                            if(data.status == 0){
                                errMessage = "Unknown Error";
                            }else{
                                errMessage = data.statusText + " (" + data.status + ")";
                            }
                                                        
                            $.toaster({ priority :'danger', title :'Provisioning Failed', message : errMessage});
                        },
                    });

                }


                $(document).on('click', '#buttonProvision', function(event){

                    var provisionForm = document.provisionForm;
                    var bootstrapURL = "<?php print($bootstrapURL); ?>"
                    bootstrapURL = bootstrapURL.replace("{deviceName}", provisionForm.elements["deviceName"].value);
                    provisionData = "";

                    //Get the bootstrap from the server
                    $.ajax({

                        type: 'GET',
                        url: bootstrapURL,

                        success: function(bootstrapPayload) {

                            postProvisioningToController(provisionForm.elements["ipAddress"].value, provisionForm.elements["password"].value, bootstrapPayload);

                        },

                        error: function(data){
                            $.toaster({ priority :'danger', title :'Retrieval Error', message : data['responseJSON']['error']});
                        },
                    });

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

                function destroyProvisioningData(){
                    /*************************************************
                    **  Destroys the data on the provision form     **
                    *************************************************/

                    //Get the form inputs
                    var provisionForm = document.provisionForm;

                    //For each input
                    for(var i = 0; i < provisionForm.length; i++){
                        
                        //Clear the data
                        provisionForm[i].value = null;
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
                                trHTML = "<tr class=\"dynamic\">"
                                            + "<td>" + data[i].name + "</td>"
                                            + "<td>" + data[i].displayName + "</td>"
                                            + "<td>" + data[i].macAddress + "</td>"
                                            + "<td>" + data[i].ipAddress + "</td>"
                                            + "<td><button class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalProvisioning\" data-backdrop=\"static\" id=\"provisionButton\" data-displayname=\"" + data[i].displayName + "\" data-devicename=\"" + data[i].macAddress + "\">Provision</button>"
                                                +"<button class=\"btn btn-info\" id=\"bootstrapButton\" onClick=\"bootstrapDevice('" + data[i].macAddress + "');\">Bootstrap</button>"
                                                +"<button class=\"btn btn-default\" data-toggle=\"modal\" data-target=\"#modalEditItem\" data-backdrop=\"static\" data-operation=\"edit\" data-uniqueid=\"" + data[i].id + "\">Edit</button>"
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
            <div id="pageTitle">Controllers</div><button data-toggle="modal" data-target="#modalEditItem" data-backdrop="static" data-operation="add" class="btn btn-success">Add New</button>
        </div>

        <table class="dataTable" id="dynamicData">
            <tbody>
            <tr>
                <th>Short Name</th>
                <th>Display Name</th>
                <th>MAC Address</th>
                <th>IP Address</th>
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
                            <label for="name">Short Name:</label>
                            <input type="text" id="name"><br><br>
                            <label for="displayName">Display Name:</label>
                            <input type="text" id="displayName"><br><br>
                            <label for="macAddress">MAC Address:</label>
                            <input type="text" id="macAddress"><br><br>
                            <label for="ipAddress">IP Address:</label>
                            <input type="text" id="ipAddress"><br><br>
                            <label for="subnet">Subnet:</label>
                            <input type="text" id="subnet"><br><br>
                            <label for="dns">DNS:</label>
                            <input type="text" id="dns"><br><br>
                            <label for="gateway">Gateway:</label>
                            <input type="text" id="gateway"><br><br>
                            <label for="mqttUsernameDefault">Use Default MQTT Username</label>
                            <input type="checkbox" id="mqttUsernameDefault" onchange="setMQTTUsernameHidden(this.checked)"><br><br>
                            <label for="mqttUsername" id="mqttUsernameLabel">MQTT Username:</label>
                            <input type="text" id="mqttUsername" size=50><br><br>
                            <label for="mqttPasswordDefault">Use Default MQTT Password</label>
                            <input type="checkbox" id="mqttPasswordDefault" onchange="setMQTTPasswordHidden(this.checked)"><br><br>
                            <label for="mqttPassword" id="mqttPasswordLabel">MQTT Password:</label>
                            <input type="text" id="mqttPassword" size=50><br><br>
                            <label for="hwVersion">Hardware:</label>
                            <select id="hwVersion">
                                <option value="2">Version 2</option>
                            </select>
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

        <!-- Provisioning Modal -->
        <div class="modal fade" id="modalProvisioning" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="controllerDisplayName"></h4>
                    </div>

                    <div class="modal-body">
                    <form name="provisionForm">
                        <input type="hidden" id="deviceName">
                        <label for="ipAddress">Current IP Address:</label>
                        <input type="text" id="ipAddress" minlength="7" maxlength="15" size="15"><br><br>
                        <label for="password">Controller Key:</label>
                        <input type="text" id="password">
                    </form>                    
                    <div id="provisionPrompt"></div></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="buttonProvision">Provision</button>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </body>
</html>