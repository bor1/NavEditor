<?php require_once("auth.php"); ?>

<script type="text/javascript">

//globals:
var thisConf = "<?php echo($ne_config_info['website_conf_filename']); ?>";


//wenn neue conf Dateien fehlen, die neu generieren.
<?php
if(!file_exists("../../".$ne_config_info['website_conf_filename']) || !file_exists("../../".$ne_config_info['variables_conf_filename'])){
?>
    //only for loading /beginn------------
    var loaded = new Object;
    var website = "<?php echo $ne_config_info['website_conf_filename']; ?>";
    var variables = "<?php echo $ne_config_info['variables_conf_filename']; ?>";
    loaded[website] = false;
    loaded[variables] = false;
    $(document).ready(function() {
        loading(true);
    });

    function loadingCheck(){
        if (loaded[website] && loaded[variables]){
            loading(false);
            clearInterval(loadingAktive);
            loadConf(thisConf);
        }
    }
    var loadingAktive = setInterval("loadingCheck()", 500);
    //only for loading /end--------------

    function create_conf(confName, confData){
        $.post("app/create_conf.php", {
                "oper": "create_conf",
                "name": confName,
                "jsonData": JSON.stringify(confData)
        }, function(rdata) {
                loaded[confName] = true;
        });
    }

    alert('Hinweis: Eine oder mehrere Konfigurationsdateien fehlen. Diese werden nun automatisch neu erstellt.');
    var json_data = [];
    //load kontakt daten von contactdata.conf save to json_data
    $.get("app/load_osm.php", function(data) {
        var arrValues = data.split('\\:\\');
        var valueNames = new Array("name", "strasse", "plz", "ort", "kontakt1-name", "kontakt1-vorname", "telefon", "fax", "email");
        for(i=0; i<11; i++){
            var item = {
            "opt_name": valueNames[i],
            "opt_value": arrValues[i]
            };
            json_data.push(item);
        }
        //load logo daten von vorlage. save to json_data
        $.getJSON("app/edit_logo.php?r=" + Math.random(), {
            "json_oper": "get_content",
            "template_name": "seitenvorlage.html"
        }, function(data){
            json_data.push({"opt_name": "name-des-Webauftritts","opt_value": data.content_text});
            json_data.push({"opt_name": "titel-des-Webauftritts","opt_value": data.site_title_text});
            json_data.push({"opt_name": "kurzbeschreibung-zum-Webauftritt","opt_value": data.content_desc});
            json_data.push({"opt_name": "logo-URL","opt_value": data.content_img});
            json_data.push({"opt_name": "logo-Alt","opt_value": data.content_img_alt});
            json_data.push({"opt_name": "logo-Width","opt_value": ""});
            json_data.push({"opt_name": "logo-Height","opt_value": ""});
            create_conf(website, json_data);
            create_conf(variables, "");
        });
    });

<?php
}
?>

</script>
