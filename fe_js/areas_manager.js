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
    
        fe_areas_manager.loadList();
        fe_areas_manager.preloadForm();

    },

    loadList : function(){
        $('#areasList').html('loading List ... <img src="ajax-loader.gif">');
    },

    preloadForm : function(){
        fe_areas_manager.loadForm();
        fe_areas_manager.disableForm();
    },

    loadForm : function(){
        $('#areasSettings').html('loading Form ... <img src="ajax-loader.gif">');
        
        $('#areasSettings').html(ne3_magic.createForm(areas_manager_form));
    },
    
    disableForm : function(){
        //
    }

};
