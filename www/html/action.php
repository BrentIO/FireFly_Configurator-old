<?php

    require_once('./api/getConfig.php');
    require_once('common.php');

    //Validate the user is logged in
    checkLogin();

    $pageName = "action";
    $url = "http://" . $_SERVER['SERVER_ADDR'] . "/api/action";
    $inputUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/input";
    $outputUrl = "http://" . $_SERVER['SERVER_ADDR'] . "/api/output";

?>
<!DOCTYPE html>
<html>
    <head>
        <title>FireFly Configurator - Actions</title>
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.min.js"></script>
        <script src="bootstrap.min.js"></script>
        <script src="jquery.toaster.js"></script>
        <script>

            function updateColorSwatch(hexValue){
                var colorSwatch = document.getElementById('colorSwatch');
                colorSwatch.style.background = hexValue;
                colorSwatch.style.visibility = "visible";
            }

            function updateOutputTypeBadge(outputType){

                var outputTypeBadge = document.getElementById('outputTypeBadge');

                switch(outputType.toUpperCase()){

                    case "BINARY":
                        outputTypeBadge.className = "badge badge-outputBinary"
                        outputTypeBadge.style.visibility = "visible";
                        outputTypeBadge.innerHTML= "Binary";
                        document.getElementById('increase').disabled = true;
                        document.getElementById('decrease').disabled = true;
                        document.getElementById('toggle').disabled = false;

                    break;

                    case "VARIABLE":
                        outputTypeBadge.className = "badge badge-outputVariable"
                        outputTypeBadge.style.visibility = "visible";
                        outputTypeBadge.innerHTML= "Variable";
                        document.getElementById('increase').disabled = false;
                        document.getElementById('decrease').disabled = false;
                        document.getElementById('toggle').disabled = true;
                    break;
                        
                    default:
                        outputTypeBadge.className = "badge";
                        outputTypeBadge.style.visibility = "hidden";
                        outputTypeBadge.innerHTML= "Unknown";
                    break;

                }

            }

            function getOutputs(controllerId, selectedValue = null){

                //Delete the outputId drop-down options
                while(document.getElementById("outputId").length > 0){
                    document.getElementById("outputId").remove(0);
                }

                //Hide the badge
                updateOutputTypeBadge("Unknown");

                //Get the list of outputs
                $.ajax({
                    beforeSend: function(request) {
                        request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                    },
                    url: "<?php print($outputUrl);?>",

                    success: function(data) {

                        data = data.sort((a, b) => (a.name > b.name) ? 1 : -1);  

                        //Add the default selection
                        optionHTML="<option selected disabled>Select...</option>";

                        $('#outputId').append(optionHTML);

                        //Add the list of outputs to the drop-down
                        $.each(data, function(i){

                            //Get the data 
                            if(data[i].controllerId == controllerId){

                                optionHTML = "<option value=\"" + data[i].id + "\" data-outputtype=\"" + data[i].outputType + "\">" + data[i].name + " : " + data[i].displayName + "</option>";
                                $('#outputId').append(optionHTML);

                            }
                        });

                        //Enable the controls
                        outputId.style.visibility = "visible";
                        outputIdLabel.style.visibility = "visible";

                        //Select the requested value, if one is specified
                        if(selectedValue != null){
                            $('#outputId').val(selectedValue);
                            updateOutputTypeBadge($('#outputId')[0].options[$('#outputId')[0].selectedIndex].getAttribute('data-outputtype'));
                            
                        }
                    },

                    fail: function(data){
                        $.toaster({ priority :'danger', title :'Getting Outputs Failed', message : data['responseJSON']['error']});
                    }
                });
            }

            function toggleActionSelection(){

                //Deselect any checked actions
                for(i = 0; i < document.getElementsByName("action").length; i++) {

                    document.getElementsByName("action")[i].checked = false;
                }

                if(document.getElementById("outputId").length > 1){

                    document.editItem.elements["action"].value = "";

                    increase.style.visibility = "visible";
                    increaseLabel.style.visibility = "visible";
                    decrease.style.visibility = "visible";
                    decreaseLabel.style.visibility = "visible";
                    toggle.style.visibility = "visible";
                    toggleLabel.style.visibility = "visible";

                }else{

                    document.editItem.elements["action"].value = "";

                    increase.style.visibility = "hidden";
                    increaseLabel.style.visibility = "hidden";
                    decrease.style.visibility = "hidden";
                    decreaseLabel.style.visibility = "hidden";
                    toggle.style.visibility = "hidden";
                    toggleLabel.style.visibility = "hidden";
                }
            }


            $(document).ready(function(){

                $.toaster({ settings : {'donotdismiss' : ['danger']  }});

                class action {
                    id = null;
                    inputId = null;
                    outputId = null;
                    actionType = null;

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
                    updateOutputTypeBadge("unknown");

                    var editItemForm = document.editItem;

                    if(operation == "add"){

                        //Set the modal title
                        document.getElementById("operation").innerHTML= "Add New Action";

                        $.when(

                            //Get the list of inputs
                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($inputUrl);?>",

                                success: function(data) {

                                    data = data.sort((a, b) => (a.displayName > b.displayName) ? 1 : -1);     

                                    //Add the default selection
                                    optionHTML="<option selected disabled>Select...</option>";

                                    $('#inputId').append(optionHTML);

                                    //Add the list of inputs to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\" data-controllerid=\"" + data[i].controllerId + "\" data-hexvalue=\"" + data[i].hexValue + "\">" + data[i].displayName + " : " + data[i].switchDisplayName + " (position " + data[i].port + ")</option>";
                                    
                                        $('#inputId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Inputs Failed', message : data['responseJSON']['error']});
                                }
                            })

                            ).then(function(){});

                    }

                    //If editing, retrieve the data from the API
                    if(operation == "edit"){

                        //Set the modal title
                        document.getElementById("operation").innerHTML= "Edit Existing Action";

                        actionData = null;

                        $.when(

                            $.ajax({

                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },

                                type: 'GET',
                                url: "<?php print($url);?>" + '/' + button.data('uniqueid'),

                                success: function(data) {

                                    actionData = data;  
           
                                },

                                error: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Action Failed', message : data['responseJSON']['error']});
                                },
                            }),

                            $.ajax({
                                beforeSend: function(request) {
                                    request.setRequestHeader("x-api-key", "<?php print(getConfig("x-api-key")); ?>");
                                },
                                url: "<?php print($inputUrl);?>",

                                success: function(data) {

                                    data = data.sort((a, b) => (a.displayName > b.displayName) ? 1 : -1);  

                                    //Add the default selection
                                    optionHTML="<option selected disabled>Select...</option>";

                                    $('#inputId').append(optionHTML);

                                    //Add the list of inputs to the drop-down
                                    $.each(data, function(i){
                                        optionHTML = "<option value=\"" + data[i].id + "\" data-controllerid=\"" + data[i].controllerId + "\" data-hexvalue=\"" + data[i].hexValue + "\">" + data[i].displayName + " : " + data[i].switchDisplayName + " (position " + data[i].port + ")</option>";
                                    
                                        $('#inputId').append(optionHTML);
                                    });

                                },

                                fail: function(data){
                                    $.toaster({ priority :'danger', title :'Getting Inputs Failed', message : data['responseJSON']['error']});
                                }
                            })

                        ).then(function(){

                            editItemForm.elements["uniqueId"].value = actionData['id']; 

                            //Select the correct input
                            editItemForm.elements["inputId"].value = actionData['inputId'];
                            updateColorSwatch(editItemForm.elements["inputId"].options[editItemForm.elements["inputId"].selectedIndex].getAttribute('data-hexvalue'));

                            $.when(
                                getOutputs(editItemForm.elements["inputId"].options[editItemForm.elements["inputId"].selectedIndex].getAttribute('data-controllerid'), actionData['outputId'])

                            ).then(function(){

                                //Set the action type
                                for (i = 0; i < editItemForm.elements["action"].length; i++) {

                                    editItemForm.elements["action"][i].style.visibility = "visible";
                                    document.getElementById(document.getElementById(editItemForm.elements["action"][i].id).id + "Label").style.visibility = "visible";

                                    if (editItemForm.elements["action"][i].id.toLowerCase() == actionData['actionType'].toLowerCase()) {
                                        editItemForm.elements["action"][i].checked = true;
                                    }
                                }
                            });     
                        });
                    }
                });


                $(document).on('click', '#buttonSave', function(event){

                    var editItemForm = document.editItem;

                    var item = new action();

                    item.id = editItemForm.elements["uniqueId"].value;
                    item.inputId = editItemForm.elements["inputId"].value;
                    item.outputId = editItemForm.elements["outputId"].value;

                    for (i = 0; i < editItemForm.elements["action"].length; i++) {

                        if (editItemForm.elements["action"][i].checked) {
                            item.actionType = editItemForm.elements["action"][i].id;
                        }
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
                    var actionType = button.data('actiontype');
                    var outputDisplayName = button.data('outputdisplayname');
                    var inputDisplayName = button.data('inputdisplayname');

                    //Clear existing input
                    destroyDeleteData();

                    var deleteItemForm = document.deleteItem;

                    //Set the hidden input value to the ID
                    deleteItemForm.elements["uniqueId"].value = button.data('uniqueid');

                    var prompt = "If you delete this entry, $OUTPUTDISPLAYNAME$ will no longer $ACTIONTYPE$ when $INPUTDISPLAYNAME$ occurs.<br><br>Are you sure?";

                    //Build the prompt
                    prompt = prompt.replace("$ACTIONTYPE$", actionType.toLowerCase());
                    prompt = prompt.replace("$OUTPUTDISPLAYNAME$", outputDisplayName);
                    prompt = prompt.replace("$INPUTDISPLAYNAME$", inputDisplayName);

                    //Replace any double-spaces
                    prompt = prompt.replace("  ", "");

                    //Set the prompt text on the modal
                    document.getElementById("deletePrompt").innerHTML= prompt;            

                });


                $(document).on('click', '#buttonDelete', function(event){

                    var deleteItemForm = document.deleteItem;

                    var item = new action;

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

                    //Delete the inputId drop-down options
                    while(document.getElementById("inputId").length > 0){
                        document.getElementById("inputId").remove(0);
                    }

                    //Delete the outputId drop-down options
                    while(document.getElementById("outputId").length > 0){
                        document.getElementById("outputId").remove(0);
                    }

                    //Deselect any checked actions
                    for(i = 0; i < document.getElementsByName("action").length; i++) {
                        document.getElementsByName("action")[i].checked = false;
                    }

                    //Hide the necessary elements
                    colorSwatch.style.visibility = "hidden";
                    outputId.style.visibility = "hidden";
                    outputIdLabel.style.visibility = "hidden";
                    increase.style.visibility = "hidden";
                    increaseLabel.style.visibility = "hidden";
                    decrease.style.visibility = "hidden";
                    decreaseLabel.style.visibility = "hidden";
                    toggle.style.visibility = "hidden";
                    toggleLabel.style.visibility = "hidden";

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

                                switch(data[i].actionType){
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


                                trHTML = "<tr class=\"dynamic\">"
                                            + "<td>" + data[i].switchDisplayName + " (position " + data[i].port + ")</td>"
                                            + "<td><span style=\"width: 15px; height: 15px; margin:auto; display: inline-block; border: 0.5px solid gray; vertical-align: middle; border-radius: 50%; background: " + data[i].hexValue + "\"></span> </td>"
                                            + "<td>" + data[i].inputDisplayName + "</td>"
                                            + "<td>" + data[i].outputName + " : " + data[i].outputDisplayName + "</td>"
                                            + "<td>" + actionType + "</td>"
                                            + "<td><button class=\"btn btn-default\" data-toggle=\"modal\" data-target=\"#modalEditItem\" data-backdrop=\"static\" data-operation=\"edit\" data-uniqueid=\"" + data[i].id + "\">Edit</button>"
                                                + "<button class=\"btn btn-danger\" data-toggle=\"modal\" data-target=\"#modalDeleteItem\" data-backdrop=\"static\" id=\"deleteButton\" data-actiontype=\"" + data[i].actionType + "\" data-outputdisplayname=\"" + data[i].outputDisplayName + "\" data-inputdisplayname=\"" + data[i].inputDisplayName + "\" data-uniqueid=\"" + data[i].id + "\">Delete</button>"
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
            <div id="pageTitle">Actions</div><button data-toggle="modal" data-target="#modalEditItem" data-backdrop="static" data-operation="add" class="btn btn-success">Add New</button>
        </div>

        <table class="dataTable" id="dynamicData">
            <tbody>
            <tr>
                <th colspan="2">Switch</th>
                <th>Input</th>
                <th>Output</th>
                <th>Action</th>
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
                            <label for="inputId">Input:</label>
                            <select id="inputId" onchange="updateColorSwatch(this.options[this.selectedIndex].getAttribute('data-hexvalue'));getOutputs(this.options[this.selectedIndex].getAttribute('data-controllerid'));toggleActionSelection();"></select>
                            <span id="colorSwatch" style="width: 15px; height: 15px; margin:auto; display: inline-block; border: 0.5px solid gray; vertical-align: middle; border-radius: 50%; background: transparent; visibility: hidden;"></span><br><br>
                            <label for="outputId" id="outputIdLabel" style="visibility: hidden;">Output:</label>
                            <select id="outputId" style="visibility: hidden;" onchange="toggleActionSelection(); updateOutputTypeBadge(this.options[this.selectedIndex].getAttribute('data-outputtype'))"></select> <span id="outputTypeBadge" style="visibility: hidden;"></span><br><br>
                            <input type="radio" id="increase" name="action" value="INCREASE" style="visibility: hidden;">
                            <label for="increase" style="visibility: hidden;" id="increaseLabel">Increase</label><br>
                            <input type="radio" id="decrease" name="action" value="DECREASE" style="visibility: hidden;">
                            <label for="decrease" style="visibility: hidden;" id="decreaseLabel">Decrease</label><br>
                            <input type="radio" id="toggle" name="action" value="TOGGLE" style="visibility: hidden;">
                            <label for="toggle" style="visibility: hidden;" id="toggleLabel">Toggle</label>                  
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