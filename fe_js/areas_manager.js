/** 
 * Javascript files for frontend areas_manager.php
 * @author Simon Michalke
 */

//fe_areas_manager = true;

fe_areas_manager.vars = true;
fe_areas_manager.vars.formLoaded = false;

fe_areas_manager.loadContent = function(){
    
    fe_areas_manager.loadList();
    fe_areas_maneger.preloadForm();

    $("areasList").innerHTML = "dies ist die liste";
    
};

fe_areas_manager.preloadForm = function(){
    fe_areas_manager.loadForm();
    fe_areas_manager.disableForm();
};

fe_areas_manager.loadForm = function(){
    //
};