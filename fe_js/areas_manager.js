/** 
 * Javascript files for frontend areas_manager.php
 * @author Simon Michalke
 */

//fe_areas_manager = true;

var fe_areas_manager = {

    vars : {
        formLoaded : false,
        currentlyLoaded : ""
    },
    
    loadContent : function(){
    
        $('#areasList').html('loading List ... <img src="ajax-loader.gif">');
        $('#areasSettings').html('loading Form ... <img src="ajax-loader.gif">');
        
        fe_areas_manager.loadList();
        fe_areas_manager.preloadForm();

    },

    loadList : function(){
        
        var listString = ne3_magic.createList(fe_areas_manager.createListFromNames(areas_manager_list_names));
        
        //add the "Add new Area" button
        listString += '<button onclick="fe_areas_manager.addArea()">new Area</button>';
        
        $('#areasList').html(listString);
    },

    preloadForm : function(){
        fe_areas_manager.loadForm();
        fe_areas_manager.disableForm();
    },

    loadForm : function(){
        $('#areasSettings').html(ne3_magic.createForm(areas_manager_form));
        
    },
    
    disableForm : function(){
        $(".bereichSettingsElement").val("");
        $(".bereichSettingsElement").prop('disabled', true);
        console.log("Form disabled!");
    },
    
    createListFromNames : function(names){
        
        //generate an object that will be parsed by ne3_magic.createList.
        
        var list = {};
        
        list.id = "areasList";
        list.css_id_s    = "";
        list.css_id_form = "";
        list.identifier  = "json_list_data";
        
        list.elements    = new Array();
        list.elements[0] = { "type"    : "h4", "content" : "Bereiche:" };
        
        i=1;
        for (; i<=names.length; i++){
            list.elements[i] = {};
            list.elements[i].type = "link";
            list.elements[i].onclick = 'fe_areas_manager.loadData(\'' + names[i-1] + '\')';
            list.elements[i].content = "" + names[i-1];
            list.elements[i].css_class = "glyphicon";
        }
        
        return list;
    },
    
    fillData : function(JSONdata){
        
        var data = JSON.parse(JSONdata);
        
        console.log(data);
        console.log(data.name);
        
        $("#name").val(data.name);
        $("#title").val(data.title);
        $("#file_name").val(data.file_name);
        $("#description").val(data.description);
        $("#content_marker_start").val(data.content_marker_start);
        $("#content_marker_end").val(data.content_marker_end);
        $("#help_page_name").val(data.help_page_name);
        $("#user_role_required").val(data.user_role_required);

        $(".bereichSettingsElement").prop('disabled', false);

    },
    
    loadData : function(name){
                
        NavTools.call_php('app/classes/AreasManager.php', 'getAreaSettings',
                name,
                fe_areas_manager.fillData);
                
                
        this.vars.currentlyLoaded = $("#name").val();
        console.log("Loaded form " + this.vars.currentlyLoaded);
    },
    
    saveDataCallback : function(message){
        console.log(message);
        
        window.location.reload();
    },
    
    saveData : function(){
        
        var areaName = "";
        var newArea = false;
        
        if (this.vars.currentlyLoaded === ""){
            //new Area
            newArea = true;
            areaName = $("#name").val();
        }
        else{
            //change existing Area
            areaName = this.vars.currentlyLoaded;
        }
        
        if (areaName === ""){
            alert("Bitte geben Sie einen Namen ein!");
            return;
        }
        
        var data = {
            //see ajax_handler.php how this should look like
            "name" : areaName,
            "data" : {
                
                "name"          : $("#name").val(),
                "title"         : $("#title").val(),
                "file_name"     : $("#file_name").val(),
                "description"   : $("#description").val(),
                "content_marker_start" : $("#content_marker_start").val(),
                "content_marker_end"   : $("#content_marker_end").val(),
                "help_page_name"       : $("#help_page_name").val(),
                "user_role_required"   : $("#user_role_required").val()
            }
        };
        
        if (newArea)
            NavTools.call_php("app/classes/AreasManager.php", "addAreaSettings", JSON.stringify(data), this.saveDataCallback);
        else
            NavTools.call_php("app/classes/AreasManager.php", "updateAreaSettings", JSON.stringify(data), this.saveDataCallback);
    },
    
    deleteArea : function(name){
        
        var text = NavTools.call_php('app/classes/AreasManager.php', 'deleteAreaSettings',
                name,
                function(data){console.log(data);});
        

    },
    
    addArea : function(){
        this.vars.currentlyLoaded = "";
        
        $(".bereichSettingsElement").prop('disabled', false);
        
        $(".bereichSettingsElement").val("");
    }

};
