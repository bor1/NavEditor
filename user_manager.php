<?php
require_once('auth.php');
//BAD CODE. TODO AngularJS ?

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>User management - <?php echo($ne_config_info['app_titleplain']); ?></title>

<?php
    echo NavTools::includeHtml("default",
            "jqueryui/ne2-theme/jquery-ui-1.8.17.custom.css",
            "jquery-ui.min.js",
            "json2.js",
            "jquery.md5.js",
            "livevalidation_standalone.compressed.js",
            "jqueryFileTree.js",
            "jqueryFileTree.css",
            "live_validation.css"
            )
?>

<script type="text/javascript">
var admin_uname = "<?php echo(NavTools::getServerAdmin()); ?>";

var _user_data_array = [];
var _currentValues = [];
var _user_roles_array = $.parseJSON('<?php echo(json_encode($ne_user_roles)); ?>') ;
var _user_modus_array = $.parseJSON('<?php echo(json_encode($ne_user_modus)); ?>') ;
var _empty_user_data_array = $.parseJSON('<?php echo(json_encode(get_ne_user_params_simple())); ?>');
var _not_editable_user_params_array = $.parseJSON('<?php echo(json_encode(get_ne_user_params_not_editable())); ?>');
var _user_params_full = $.parseJSON('<?php echo(json_encode($ne_user_params)); ?>');
var _somethingChanged = false;
var _check_boxes_status = [];
var FileTreeObj = null;

function load_validation(){
    //helper func.
    var setValidationPattern = function(field, pattern, errormsg){
        var validObj = new LiveValidation(""+field, {onValid: function(){}});
        validObj.add(Validate.Format, {
            pattern: pattern,
            failureMessage: errormsg
        });
        return validObj;

    }

    var arrayOfFieldNames = ['user_name'];
    var arrayOfValidVars = [];
    var count = 0;
    for (var i = 0; i < arrayOfFieldNames.length; i++){
        arrayOfValidVars[count] = new LiveValidation(arrayOfFieldNames[i], {onValid: function(){}});
        arrayOfValidVars[count].add(Validate.Format, {
            pattern: /^[a-zA-Z0-9._-]*$/i,
            failureMessage: 'Darf nur Buchstaben, Ziffern, "_", "-" und Punkt enthalten'
        })
        count++;
    }
    var user_name_pat = setValidationPattern('user_name', /^[a-zA-Z0-9._-]*$/i, 'Darf nur Buchstaben, Ziffern, "_", "-" und Punkt enthalten');
    var vorname = setValidationPattern('vorname', /^([A-Z]){1}([a-z\s-])*$/, 'Darf nur Buchstaben, Leerzeichen und "-" enthalten. Grossbuchstabe am Anfang');
    var nachname = setValidationPattern('nachname', /^([A-Z]){1}([a-z\s-])*$/, 'Darf nur Buchstaben, Leerzeichen und "-" enthalten. Grossbuchstabe am Anfang');
    var ablaufdatum = setValidationPattern('ablaufdatum', /^(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[012])[/](19|20)\d\d$/i, 'Datum falsch eingegeben? Bitte folgendes Format benutzen: dd/mm/yyyy, z.B.22/03/2012');
    var user_name = new LiveValidation('user_name', {onValid: function(){}});
    user_name.add(Validate.Presence, {failureMessage: 'Name darf nicht leer sein!'});

    var mail = new LiveValidation('email', {onValid: function(){}});
    mail.add( Validate.Email);

    var password2 = new LiveValidation("password_hash2", {onValid: function(){}});
    password2.add( Validate.Confirmation, {
        match: 'password_hash',
        failureMessage: "Passwort muss \u00FCbereinstimmen"
    });

    return LiveValidation.massValidate( [ user_name_pat, ablaufdatum, mail, password2, vorname, nachname] );
}

function array_diff_key (arr1) {
    var argl = arguments.length,
        retArr = {},
        k1 = '',
        i = 1,
        k = '',
        arr = {};

    arr1keys: for (k1 in arr1) {
        for (i = 1; i < argl; i++) {
            arr = arguments[i];
            for (k in arr) {
                if (k === k1) {
                    // If it reaches here, it was found in at least one array, so try next value
                    continue arr1keys;
                }
            }
            retArr[k1] = arr1[k1];
        }
    }
    return retArr;
}

var _editable_user_params_array = array_diff_key(_empty_user_data_array, _not_editable_user_params_array);

//wird aufgerufen nach dem page geladen ist, und 1. daten ajax responce da ist.
function loadContentCallback(data) {

    var users = new Array(),
        users_ul = $("<ul>").addClass("user-list");

     _user_data_array = data;

    $.each(data, function (index, value) {
        var li = $("<li>").attr('id', index),
            icon = $('<i class="icon-user">'),
            a = $('<a href="javascript:void(0);">').addClass("user_button").html(value.user_name);

        li.html(new Array(icon, a));
        users.push(li);
    });

    //add new button
    users.push($("<li>").attr('id', 'addNewUser')
            .append($('<i class="icon-plus-sign">'))
            .append($('<a href="javascript:void(0);">')
                        .addClass("user_button")
                        .html('Benutzer hinzufügen')
                    )
             );

    users_ul.html(users);
    $("#userList").html(users_ul);

    loadFilePermTree();

}

function loadFilePermTree(){
    mainPath  = "<?php echo ($_SERVER['DOCUMENT_ROOT']); ?>";
    addFolderTree();
}

function addFolderTree(){
    //nicht noch mal laden
    if(FileTreeObj) return;

    FileTreeObj = new FileTree($('#userPermission'), {
        root: '/',
        permissions: '1',
        expandCallBack: function(){
            loadCheckBoxes();
            fillPermissionSubCheckBox();
        },
        selectCallBack: function(path, isFile) {
            var rel = "";
            if (isFile){
                rel = path;
                //toggle checkbox on click on file
                var checkBox = $('#userPermission input[value="'+rel+'"]');
                checkBox.prop('checked', !checkBox.is(':checked'));
                checkBox.change();
            }else if(!isFile){
                //rel = folder;
            }
        }
    });
}

function loadCheckBoxes(){
    $("#userPermission input").val(_check_boxes_status);
}

//dynamische form daten pro benutzer
function fillFieldsWithData(dataArray){
    var $form = $('<container>');
    for(var element in dataArray){
        switch (element) {
                case "permission":
                    _check_boxes_status = dataArray[element].split("|");
                    $("#userPermission input").val(_check_boxes_status);
                    fillPermissionSubCheckBox();
                    break;
                case "password_hash":
                    $form.append(createOptionHtml(_user_params_full[element].name, "password_hash", "", 0, "password"));
                    $form.append(createOptionHtml(_user_params_full[element].name+" wiederholen", "password_hash2", "", 0, "password"));
                    break;
                case "rolle":
                    $form.append(createDropBoxOptionHtml(_user_params_full[element].name, element, _user_roles_array, dataArray[element]));
                    break;
                case "bedienermodus":
                    //noch nicht funktionsfaehig -> zuerst nicht anzeigen
                    $form.append(createDropBoxOptionHtml(_user_params_full[element].name, element, _user_modus_array, dataArray[element])
                            .hide());
                    break;
                case "letzter_login": case "erstellungsdatum":  case "ablaufdatum":
                    if(dataArray[element] > 0){
                        var datum = new Date(dataArray[element]*1000);
                        var datumString = $.datepicker.formatDate("dd/mm/yy",datum);
                        if(element == "letzter_login"){datumString = datum.toLocaleString();} // mit time anzeigen
                    }else{
                        var datumString = "";
                    }
                    $form.append(createOptionHtml(_user_params_full[element].name, element, datumString));
                    break;
                case "zusatzrechte":
                    break;
                default:
                    $form.append(createOptionHtml(_user_params_full[element].name, element, dataArray[element]));
                    break;
            }
    }

    //select set of elements
    var $elementsToAppend = $form.children();

    //make not editable READONLY, or disabled?
    $.each($elementsToAppend.find(':input'), function(i, obj){
        if($(obj).attr('name') in _not_editable_user_params_array){
            $(obj).attr('disabled', true);
        }
    });

    $('#userOptions').html($elementsToAppend);




    //set datepicker for some of elements.  Bugged. need to dynamically recreate on focus.
    $(document).on('focus', '#userOptions :input[name="ablaufdatum"]',  function(){
        $(this).datepicker( "destroy" );
        $(this).datepicker({
            dateFormat: "dd/mm/yy"
        });
    });
}

/**
 * creates template for some input
 * @param {String} optionName name of input
 * @param {jQuery} $input jQuery input element to put in template
 * @returns {jQuery} created full elment
 */
function createOption(optionName, $input){
    var controlGroupDiv,
        label,
        divControls;
    controlGroupDiv = $('<div>').attr('class','control-group');
    label = $('<label>').attr('class','control-label').html(optionName);
    divControls = $('<div>').attr('class','controls');

    //add input
    divControls.append($input);
    //add set together, and return
    return controlGroupDiv.append(label).append(divControls);
}

/**
 * genrates html for input field
 * @param {String} optionName input description
 * @param {String} htmlNameParam ID and NAME of option
 * @param {String} wert value
 * @param {String} disParam 0, null or false if not disabled
 * @param {String} type type of input
 * @returns {jQuery}  created element
 */
function createOptionHtml(optionName, htmlNameParam, wert, disParam, type){
    var htmlName = (htmlNameParam === null)? optionName: htmlNameParam;
    type = (!type)? 'text' : type;

    var $input;
    //create input
    $input = $('<input>').attr({
                'class' :'userOptionsElement',
                'type'  :type,
                'id'    :htmlName,
                'name'  :htmlName,
                'value' :wert
            })
            .prop('disabled',!!disParam);

    //create full option with the input and return it
    return createOption(optionName, $input);
}

//args: optionName - label name
//optionHtmlNameParam - optional <select> name
//listArray - array of select list elems
//selectedElem - selected element (name)
function createDropBoxOptionHtml(optionName, optionHtmlNameParam, listArray, selectedElem){
    var optionHtmlName = (optionHtmlNameParam == null) ? optionName : optionHtmlNameParam;
    var value = "";
    var selected = "";
    var name = "";
    var $option, $select;
    //create select element
    $select = $('<select>').attr({'name':optionHtmlName, 'class':'userOptionsElement', 'size':'1'});
    //fill with options
    for(var elem in listArray){
        value = listArray[elem]['value'];
        name = listArray[elem]['name'];
        //true if selected. Compare int with text maybe
        selected = (''+value === ''+selectedElem);
        //create opton
        $option = $('<option>')
                .attr('value', value)
                .html(name);
        //add selected if needed
        if(selected){$option.attr('selected','true');}
        //add to $select
        $select.append($option);
    }
    //create full option and return it
    return createOption(optionName, $select);
}

function createButtonHtml(buttonName, label){
//    var html = '<button class="temp_buttons btn" id='+buttonName+'>'+ label +'</button>';
    return $('<button>').attr({'class':"temp_buttons btn btn-primary",'id':buttonName, 'style':'margin:5px'}).html(label);
}
function clearTempButtons(){
    $('.temp_buttons').remove();
}

//spaghetticode function? Oo
function fillPermissionSubCheckBox(pfad, checked){
    function removeCheckBoxParentRek(pfad, count){
        if(pfad.length == 0){return;};
        if(count>1000){return;}
        var parent = verzeichniss(pfad);
        $('#userPermission input[value="'+parent+'"]').prop('checked', false);
            //for global array of chkbx state
            saveChkBoxStatus(parent, false);
            $("#userPermission input[value^='"+ parent +"']:checked").each(function(i, obj){
                saveChkBoxStatus($(obj).val(), true);
            });
        if(parent != "/"){
            parent = verzeichniss(parent.replace(/\/$/, ""));
            removeCheckBoxParentRek(parent, count+1);
        }
    }
    function addCheckBoxParentRek(pfad, count){
        if(pfad.length == 0){return;};
        if(count>1000){return;}
        var verz = verzeichniss(pfad);
        if($("#userPermission input[value^='"+ verz +"']").not(':checked').length == 1){
            $('#userPermission input[value="'+verz+'"]').prop('checked', true);
            saveChkBoxStatus(verz, true);
        }
        if(verz != "/"){
            verz = verzeichniss(verz.replace(/\/$/, ""));
            addCheckBoxParentRek(verz, count+1);
        }

    }
    //checked or just refresh (no args)
    if(checked || pfad == null){
        var checkedArray = [];
        //checked folder -> fill array with it, and check subfolders/files
        if(pfad != null  && pfad.match(/.*\/$/)){
            checkedArray.push(pfad);
        //default, without params.
        }else if(pfad == null){
            $.each($("#userPermission input:checked"), function(i, obj){
                checkedArray.push($(obj).val());
            });
        }
        //even checking subfolders/files
        $.each($("#userPermission input"), function(i, obj){
            if(hasparent($(obj).val(),checkedArray)){
                $(obj).prop('checked', true);
            }
        });
        //test if all files are checked for the parent folder. In case its true, check parent
        addCheckBoxParentRek((pfad == null)?"":pfad,0);
    //uncheck
    }else{
        //uncheck folder -> uncheck all files contained in the folder
        if(pfad.match(/.*\/$/)){
            $('#userPermission input[value^="'+pfad+'"]').prop('checked',false);
        }
        removeCheckBoxParentRek(pfad, 0);
    }
}



function removeUserCallback(data) {
	alert(data);
    reloadForm(_currentValues['user']);
}

function updateUserCallback(data) {
	alert(data);
	reloadForm();
}

function createUserCallback(data) {
	alert(data);
	reloadForm();
}

function setPanelScroll() {
	var winHeight = $(window).height();
	var panelHeight = winHeight - 208; // TODO
	$("#usermanager").css("height", panelHeight + "px");
}

function checkForm(pwcheck){
    var capitaliseFirstLetter = function(field)
    {
        var string = field.val();
        field.val( string.charAt(0).toUpperCase() + string.slice(1));
    }

    capitaliseFirstLetter($("#usermanager input[name='vorname']"));
    capitaliseFirstLetter($("#usermanager input[name='nachname']"))

    var passWord = $("#usermanager input[name='password_hash']").val();


    if(pwcheck != null && pwcheck){
        if(passWord == "") {
            alert("Bitte Passwort eingeben!");
            return false;
        }
    }


    var permission = readPermissions();
    if(permission == ""){
        if(!confirm("Keine Zugriffsrechte zugewiesen, trotzdem weiter?")) {
            return false;
        }
    }

    if (!load_validation()) {
        alert("Bitte die Fehler korrigieren")
        return false;
    }

    return true;
}

//prueft ob path in permission's array einen parent pfad hat.
//z.b. path: /foo/bar/file.txt hat parent /foo/bar/
//path: /foo/bar/ hat parent /foo/
function hasparent(path, permission){
    var retVal = false;
    var verz, regexpparent;
    for(var i =0, j = permission.length; i <j; i++){
        if(permission[i].match(/.*\/$/i)){
            verz = permission[i];
            verz = verz.replace(/\//g, '\\/');
            regexpparent = eval('/^'+verz+'/i');

            if(path.match(regexpparent)){
              retVal = true;
              break;
            }
        }
    }
    return retVal;
}

function verzeichniss(path){
    var str = path + '';
    var slash = str.lastIndexOf('/');
    return str.substr(0, slash+1);
}

//fuellt checkboxes abhaengig von permArray
function readPermissions(permArray){
    var permission = [];
    //nichts uebergeben? die sichtbare checkbox's auslesen
    if(permArray == null){
        $("#userPermission input:checked").each(function() {
            if(!hasparent($(this).val(), permission)){
                permission.push($(this).val());
            }
        });
    }else{
        $.each(permArray, function(i, elem) {
            if(!hasparent(elem, permission)){
                permission.push(elem);
            }
        });
    }


    return permission.join("|");
}

//liest daten von input, und gibt als array zurueck
function readInput(){
    var arrValues = _editable_user_params_array;
    var serArr = [];

    $.each($("#userOptions :input"), function(i, obj){
        if($(obj).attr('disabled') == null){
            serArr[$(obj).attr('name')] = $(obj).val();
        }
    })
    serArr['permission'] = readPermissions(_check_boxes_status);

    if(serArr['password_hash'] != undefined && serArr['password_hash'] != null && serArr['password_hash'] != ""){
        serArr['password_hash'] = $.md5(serArr['password_hash']);
    }

    //datum in ms umwandeln
    serArr['ablaufdatum'] = Math.round(($.datepicker.parseDate('dd/mm/yy', serArr['ablaufdatum'])/1000)); //Date.parse(serArr['ablaufdatum'])/1000);


    for(var elem in arrValues){
        arrValues[elem] = (serArr[elem] == null) ? "" : serArr[elem];
    }
    return arrValues;
}

//checks if something changed by the user. wert?-> setter.
function checkInputChange(wert){
    if(wert == null){
        if(_somethingChanged == true){
            if(confirm(unescape("Daten ge%E4ndert%2C wirklich verlassen%3F"))){
                return false;
            }
            return true;
        }
    }else{
        if(wert){
            _somethingChanged = true;
        }else{
            _somethingChanged = false;
        }
    }
}

//save/remove value from global chkBoxSTATUS array.
function saveChkBoxStatus(value, chkYesNo){
        if(chkYesNo){
            //add pfad, delete childs
            _check_boxes_status.push(value);
        }else{
            //remove pfad
            _check_boxes_status.splice(_check_boxes_status.indexOf(value),1);// = $.grep(_check_boxes_status, function(elem) {
                //return elem != value;
            //});
        }
        //remove childs
        _check_boxes_status = $.grep(_check_boxes_status, function(elem) {
            return !(elem.substring(0, value.length) == value && elem != value);
        });
        //make uniq.
        $.unique(_check_boxes_status);

        //if no elements, set "", if more then 1 remove ""
        if(_check_boxes_status.length == 0){
            _check_boxes_status.push("");
        }else if(_check_boxes_status.length > 1 ){
            _check_boxes_status = $.grep(_check_boxes_status, function(elem) {
                return elem != "";
            });
        }
    }

/**
 * loads/reloads dynamically form data
 * @param {String} selectUserAfterLoading user to select, after form loaded
 */
function reloadForm(selectUserAfterLoading){
    //felder verstecken, "select user" anzeigen
    $('#content').hide();
    $('#userOptions').html('');
    $('#user_name_in_haeder').html('Wählen Sie einen Benutzer aus der Liste aus.');

    //entweder uebergabeparameter, oder daten von dem field username, oder ''
    var currentUser = selectUserAfterLoading || $('#user_name').val() || '';

    $.getJSON("app/edit_user.php?r=" + Math.random(), {
        "json_oper": "get_users"
    }, function(data){
        loadContentCallback(data);
        //falls ein user muss ausgewaehlt werden
        if(currentUser.length > 0){
            selectUser(currentUser);
        }
    });
}

/**
 * selects user from menu
 * @param {String} userName user name to select
 */
function selectUser(userName){
    _somethingChanged = false;
    $('a.user_button').filter(function(){
        return $(this).text() === userName;
    })
    .first().click();
}

/* ---------- Here comes jQuery: ---------- */
$(document).ready(function() {
    //daten per ajax laden
    reloadForm();

    //rechten checkbox's fuellen wenn noetig
    $(document).on('change', "#userPermission input", function(){
        var checkedYesNo = $(this).prop('checked');
        var thisRef = $(this).val();
        saveChkBoxStatus(thisRef, checkedYesNo );
        fillPermissionSubCheckBox(thisRef, checkedYesNo);

    });

    //input changed, merken
    $(document).on('change', '#userDetails input', function(){
       if(_currentValues['user'] != null){
           if( _currentValues['user'].length > 0){
               _somethingChanged = true;
           }
       }
    });

    //on select some user, or click "add user" menu
    $(document).on('click', "#userList li", function(){
        //wenn first user select
        $('#content').show();
        //test for change
        if(checkInputChange()){return;}
        //variables
        var $this = $(this),
            userArray = _empty_user_data_array,
            userName = '',
            $addButtonsHtml = '',
            headDescription = '';

        //visuelle "angeklickt"
        $this.addClass("active");
        $this.siblings().removeClass("active");

        //set default tab
        $("a[href='#options']").tab('show');

        //user selected
        if($this.attr('id') !== 'addNewUser'){
            userArray = _user_data_array[$this.attr("id")];
            userName= userArray['user_name'];
            $addButtonsHtml = createButtonHtml('updateUser', 'Benutzer aktualisiren').add(createButtonHtml('removeUser', 'Benutzer löschen'));
            headDescription = userName;
        }
        //add new user selected
        else{
            //empty values
            //and new buttons
            $addButtonsHtml = createButtonHtml('createUser', 'Hinzufügen');
            headDescription = 'Neuer Benutzer'
        }
        $('#user_name_in_haeder').html(headDescription);
        fillFieldsWithData(userArray);
        _currentValues['user'] = userName;
        //ui bug ? ..
        $('#operButtons').html($addButtonsHtml);
        checkInputChange(false);

    });


    $(document).on('click', "#usermanager #createUser", function() {
        if(!checkForm(true)){
            return;
        }
        var userName = $("#usermanager input[name='user_name']").val();
        var params = readInput();
        params = JSON.stringify(params);

        $.post("app/edit_user.php",
        {
           "json_oper": "create_user",
           "user"     : userName,
           "params"   : params
        }, createUserCallback);

    });
    //on save user changes
    $(document).on('click', "#usermanager #updateUser", function() {
        if(!checkForm()){
            return;
        }
        var userName = _currentValues['user'];

        if(confirm(unescape('Den Benutzer: \"'+ userName + '" aktualisieren?'))){
            var params = readInput();
            if(params['password_hash'] == ""){
                delete params['password_hash'];
            }
            params = JSON.stringify(params);

            $.post("app/edit_user.php",
            {
               "json_oper": "update_user",
               "user"     : userName,
               "params"   : params
            }, updateUserCallback);
        }


    });
    //on click remove user
    $(document).on('click', "#usermanager #removeUser", function() {
        var userName = _currentValues['user'];
        if(confirm(unescape('Den Benutzer: \"'+ userName + '" l%F6schen?'))){
              if(admin_uname == userName) {
                alert("Sie dürfen nicht den SERVER-ADMININSTRATOR entfernen!");
                return;
            }
            $.post("app/edit_user.php", {
                "json_oper": "remove_user",
                "user_name": userName
            }, removeUserCallback);
        }
    });

    $(window).resize(function() {
        setPanelScroll();
    });
    setPanelScroll();
});
</script>
</head>

<body id="bd_User">
    <?php require('common_nav_menu.php'); ?>

    <div class="container">
        <div class="page-header">
            <h3 class="page-header">Benutzerverwaltung</h3>
        </div>

        <div  class="row padding-top" id="contentPanel1222">
            <div id="usermanager" >
                <div class="span3">
                    <h4>Benutzer</h4>

                    <div id="userList" class="user-list"> </div>
                </div>
                <div class="span9 page" id="userDetails">

                    <div class="page-header">
                        <h4 id='user_name_in_haeder' class="page-header"></h4>

<!--                        <div class="pull-right">
                                    <a class="dismiss btn btn-light btn-danger" href="javascript:void(0);"><i class="icon-white icon-trash"></i> Nutzer L&ouml;schen</a>

                        </div>-->
                    </div>

                    <div id="content" class="tabbable"> <!-- Only required for left/right tabs -->
                      <ul class="nav nav-tabs nav-tabs-custom">
                        <li class="active"><a href="#options" data-toggle="tab">Details bearbeiten</a></li>
                        <li><a href="#permissions" data-toggle="tab">Berechtigungen einstellen</a></li>
                      </ul>
                      <div class="tab-content">
                        <div class="tab-pane active" id="options">
                            <div id="userOptions" class="form-horizontal"></div>
                        </div>
                        <div class="tab-pane" id="permissions">
                            <div id="userPermission"></div>
                        </div>
                        <hr/>
                        <div class="span btn-group" id="operButtons"></div>

                      </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

<?php require('common_footer.php'); ?>
</body>

</html>
