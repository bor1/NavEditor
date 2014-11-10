/** 
 * Javascript files for frontend areas_manager.php
 * @author Simon Michalke
 */

//fe_areas_manager = true;

var fe_areas_manager = {

    vars : {
        formLoaded : false
    },
    
    loadContent : function(){
    
        $('#areasList').html('loading List ... <img src="ajax-loader.gif">');
        $('#areasSettings').html('loading Form ... <img src="ajax-loader.gif">');
        
        fe_areas_manager.loadList();
        fe_areas_manager.preloadForm();

    },

    loadList : function(){
        
        $('#areasList').html(ne3_magic.createList(fe_areas_manager.createListFromNames(areas_manager_list_names)));
    },

    preloadForm : function(){
        fe_areas_manager.loadForm();
        fe_areas_manager.disableForm();
    },

    loadForm : function(){
        
        $('#areasSettings').html(ne3_magic.createForm(areas_manager_form));
    },
    
    disableForm : function(){
        //
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
        console.log(JSONdata);
        
        var data = JSON.parse(JSONdata);
        
    },
    
    loadData : function(name){
        
        console.log(name);
        
        NavTools.call_php('app/classes/AreasManager.php', 'getAreaSettings',
                name,
                fe_areas_manager.fillData);
    },
    
    saveData : function(name){
        //
    },
    
    deleteArea : function(name){
        
        var text = NavTools.call_php('app/classes/AreasManager.php', 'deleteAreaSettings',
                name,
                function(data){console.log(data);});
        

    }

};
