<?php
require_once('auth.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Logo bearbeiten - <?php echo($ne_config_info['app_titleplain']); ?></title>
<?php
    echo NavTools::includeHtml("default", "json2.js");
?>

<script type="text/javascript">
var imgObj, imgW, imgH, jsonArray;

var thisConf = "<?php echo($ne_config_info['website_conf_filename']); ?>";
function loadConf(confFileName) {
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_conf",
		"conf_file_name": confFileName
	}, loadContentCallback);
}
// function getValueByName(jdata, wert){
	// var tmpRet = "parameter nicht gefunden";
	// $.each(jdata, function(intInd, value){
		// if (value.opt_name == wert){
			// tmpRet = value.opt_value;
			// return false;
		// }
	// });
	// return tmpRet;
// }


function loadContentCallback(data) {
	jsonArray = data;

	$.each(data, function( intIndex, obj){
		$("#"+obj.opt_name).val(obj.opt_value);
	});

	imgH = $("#logo-Height").val();
	imgW = $("#logo-Width").val();

	if($("#logo-URL").val() != "") {
		imgPreLoad();
	}
}

function previewImageLoadCallback() {
	if(imgObj.complete) {
        imgW = imgW || imgObj.width;
        imgH = imgW || imgObj.height;
	}
}

function saveContentCallback(data) {
	//alert(data);
	saveVars(data);
//	location.reload();
}

//saveVars function, for every button
function saveVars(datab4){
	$.each(jsonArray, function(ind, obj){
		if($("#"+obj.opt_name).length){
			jsonArray[ind].opt_value = $("#"+obj.opt_name).val();
		}
	});
	$.post("app/edit_conf.php", {
			"oper": "set_conf",
			"conf_file_name": thisConf,
			"jsonData": JSON.stringify(jsonArray)
		}, function(rdata) {
			alert(datab4);
			loading(false);
			loadConf(thisConf);
		});
}


function imgPreLoad(){
    var imgStr = "";
	if($("#logo-URL").val() !== ""){
		var imgsrctmp = $("#logo-URL").val();
		var imgalttmp = $("#logo-Alt").val();
		imgH = $("#logo-Height").val();
		imgW = $("#logo-Width").val();
		imgObj = new Image();
		imgObj.src = imgsrctmp;
		imgObj.onload = previewImageLoadCallback;
        var hStyle = (imgH)?"height:" + imgH + "px;":"";
        var wStyle = (imgW)?"width:" + imgW + "px;":"";
		imgStr = "<img alt='" + imgalttmp + "' style='" + hStyle + wStyle + "' src='" + imgsrctmp + "' border='0'/>";
	}
	$("#imgPrev").html(imgStr);
}

function loading(yesNo){
	if (yesNo) {
		if ($('div.tmpLoadingOverlay').length){
			$('div.tmpLoadingOverlay').fadeIn(100);
		}else{
			$('body').append('<div class="tmpLoadingOverlay"><div style="z-index:1000;position:fixed;top:0;bottom:0;left:0;width:100%;height=100%;background:#000;opacity:0.35;-moz-opacity:0.35;filter:alpha(opacity=35);visibility:visible;"><p class="p_tmp_class_loading"  style="position:fixed;top:50%;width:100%;z-index:1001;color:#fff;font-size:20px;font-weight:bold;text-align:center;">Loading...</p></div></div>');
		}
		$("body").css("overflow", "hidden");
	}else{
		$('div.tmpLoadingOverlay').fadeOut(1000);
		$("body").css("overflow", "auto");
    }
}


$(document).ready(function() {
	loadConf(thisConf);
//	on change any param of logo, reload it (preview)
	$("#bildBlock :input").on('keydown', function() {
        setTimeout(imgPreLoad, 500);
	});
	//all inputs add class 'textBox'
	$('input[type="text"]').addClass("textBox");

	//save all inputs to conf.
	$("#btnSaveall").click(function saveAll() {
		$.each(jsonArray, function(ind, obj){
			if($("#"+obj.opt_name).length){
				jsonArray[ind].opt_value = $("#"+obj.opt_name).val();
			}
		});
		loading(true);
		var btnTmp = $(this);
		var btnTmpVal = $(this).val();
		$(this).val("Moment...");
        $(this).attr("disabled", "disabled");
        $.post("app/edit_conf.php", {
            "oper": "set_conf",
            "conf_file_name": thisConf,
            "jsonData": JSON.stringify(jsonArray)
        }, function(rdata) {
            alert(rdata);
            btnTmp.val(btnTmpVal);
            btnTmp.removeAttr("disabled");
            loading(false);
            loadConf(thisConf);
        });
	});



	$("#chkAllowHtml").click(function(){
		if ($("#chkAllowHtml:checked").length > 0){
            alert("HTML auf eigene Gefahr verwenden");
		}
	});


	$("#btnUpdate").click(function() {
		var text = $("#name-des-Webauftritts").val();
		var desc = $("#kurzbeschreibung-zum-Webauftritt").val();
		var imgUrl = $("#logo-URL").val();
		var imgAlt = $("#logo-Alt").val();
		var siteTitle = $("#titel-des-Webauftritts").val();

		// if image specified, then alt-text cannot be empty!
		if(imgUrl != "") {
			if(imgAlt == "") {
				alert("Bitte geben Sie die Beschreibung zu Ihrem Logo an!");
				return false;
			}
		}

		if(confirm("Wollen Sie wirklich speichern?")) {
			loading(true);
			if(imgUrl !== "") {
				img = "<img alt=\"" + imgAlt + "\" src=\"" + imgUrl + "\" width=\"" + imgW + "\" height=\"" + imgH + "\" border=\"0\" />";
			}else{
				img = "";
			}
			var templname = $("#selTempl").val();
			var pdata = {
				"content_text": text,
				"content_desc": desc,
				"content_img": img,
				"content_img_alt": imgAlt,
				"site_title_text": siteTitle,
				"content_allow_html": $("#chkAllowHtml:checked").length > 0 ? true : false
			};
			$.post("app/edit_logo.php", {
				"json_oper": "update_content",
				"json_content": JSON.stringify(pdata),
				"template_name": templname
			}, saveContentCallback);
		}
	});

//	$("#btnLoadLogo").click(function() {
//		$.getJSON("app/edit_logo.php?r=" + Math.random(), {
//			"json_oper": "get_content",
//			"template_name": $("#selTempl").val()
//		}, loadContentCallback);
//	});


	$("#btnUpdateExisted").click(function() {
		if(confirm("Wollen Sie wirklich alle Seiten aktualisieren und mit dem neuen Titel und/oder Logo versehen?")) {
			loading(true);
			var text = $("#name-des-Webauftritts").val();
			var desc = $("#kurzbeschreibung-zum-Webauftritt").val();
			var imgUrl = $("#logo-URL").val();
			var imgAlt = $("#logo-Alt").val();
			var siteTitle = $("#titel-des-Webauftritts").val();
			var img = '';
			if(imgUrl !== "") {
//				img = "<img alt=\"" + imgAlt + "\" src=\"" + imgUrl + "\" width=\"" + imgW + "\" height=\"" + imgH + "\" border=\"0\" />";
                img = $('#imgPrev').html();
			}
			var templname = $("#selTempl").val();
			var pdata = {
				"content_text": text,
				"content_desc": desc,
				"content_img": img,
				"content_img_alt": imgAlt,
				"site_title_text": siteTitle,
				"content_allow_html": $("#chkAllowHtml:checked").length > 0 ? true : false
			};
			$.post("app/edit_logo.php", {
				"json_oper": "update_content_all",
				"json_content": JSON.stringify(pdata),
				"template_name": templname
			}, saveContentCallback);
		}
	});

	$("#btnCopySiteName").click(function() {
		$("#titel-des-Webauftritts").val($("#name-des-Webauftritts").val());
	});



	// initial load
	// $.getJSON("app/edit_logo.php?r=" + Math.random(), {
		// "json_oper": "get_content",
		// "template_name": $("#selTempl").val()
	// }, loadContentCallback);

});


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
</head>

    <body id="bd_Logo">

        <?php require('common_nav_menu.php'); ?>

        <div class="container page" id="wrapper">

            <div class="page-header">
                <h2 class="page-header">Webseitendaten bearbeiten</h2>
            </div>


            <div class="tabbable"> <!-- Only required for left/right tabs -->
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="active"><a href="#logo" data-toggle="tab">Logo bearbeiten</a></li>
                    <li><a href="#osm" data-toggle="tab">OpenStreetMap-Kontaktseite erstellen</a></li>
                </ul>
                <div class="tab-content">

					<div class="tab-pane active" id="logo">
                        <form action="" method="post" name="frmEdit" id="frmEdit" class="form-horizontal">

                            <input type="hidden" id="selTempl" name="selTempl" value="seitenvorlage.html">

							<div class="form-group">
								<label class="col-sm-3 control-label" for="name-des-Webauftritts">Name des Webauftritts:</label>
								<div class="col-sm-9">
									<input type="text" id="name-des-Webauftritts" class="form-control" name="name-des-Webauftritts" size="40" />
								</div>
								<div class="col-sm-9 col-sm-offset-3">
									<label class="checkbox">
										<input type="checkbox" id="chkAllowHtml" name="chkAllowHtml" value="ja"> Eigene HTML-Anweisungen zulassen
									</label>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="titel-des-Webauftritts">Titel des Webauftritts:<br />(erscheint im Browser-Tab)</label>
								<div class="input-append col-sm-6">
									<input type="text" id="titel-des-Webauftritts" class="form-control" name="titel-des-Webauftritts" size="40" />
								</div>
								<div class="col-sm-3">
									<a class="btn btn-primary btn-light" id="btnCopySiteName">Aus Name kopieren</a>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="kurzbeschreibung-zum-Webauftritt">Kurzbeschreibung zum Webauftritt:</label>
								<div class="col-sm-9">
									<input type="text" id="kurzbeschreibung-zum-Webauftritt" class="form-control" name="kurzbeschreibung-zum-Webauftritt" size="60" />
								</div>
							</div>

							<hr style="border-top: solid 1px #ccc; border-bottom: solid 1px transparent;">
							<div id="bildBlock">
								<div class="form-group">
									<label class="col-sm-3 control-label"for="logo-URL">Bild f&uuml;r das Logo (Optional):</label>
									<div class="input-append col-sm-6">
										<input type="text" id="logo-URL" class="form-control" name="logo-URL" size="40" />
									</div>
									<div class="col-sm-3">
										<a class="btn btn-primary btn-light" href="file_editor.php" target="_blank">Bild hochladen</a>
									</div>
									<div class="col-sm-9 col-sm-offset-3">
										<div id="imgPrev"></div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="logo-Alt">Alternativer Text f&uuml;r das Bildlogo (falls die Grafik nicht angezeigt oder angesehen werden kann):</label>
								<div class="col-sm-9">
									<input type="text" id="logo-Alt" class="form-control" name="logo-Alt" size="40" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="logo-Height">Bildh&ouml;he (optional):</label>
								<div class="col-sm-3">
									<input type="text" id="logo-Height" name="logo-Height" class="col-sm-4 form-control" size="40" />
								</div>
								<label class="col-sm-2 control-label" for="logo-Width">Bildbreite (optional):</label>
								<div class="col-sm-3">
									<input type="text" id="logo-Width" name="logo-Width" class="col-sm-4 form-control" size="40" />
								</div>
							</div>

							<div class="form-footer">
								<input type="button" id="btnUpdate" name="btnUpdate" value="Seitenvorlage aktualisieren" class="btn btn-light btn-large btn-success" />
								<input type="button" id="btnUpdateExisted" name="btnUpdateExisted" value="Existierende Seiten aktualisieren" class="btn btn-light btn-large btn-success" />
								<img id="ajaxLoader" alt="please wait..." src="ajax-loader.gif" border="0" width="16" height="16" style="display:none;" />
							</div>
						</form>
					</div>

                    <div class="tab-pane" id="osm">
                        <!-- Kontakt Block ab hier -->
                        <script type="text/javascript">
                            <!--
                            var helpText = "";

                            $(document).ready(function() {
                                // $("#btnLoadConf").click(function() {
                                // $.get("app/load_osm.php", function(data) {
                                // var arrValues = data.split('\\:\\');
                                // var n = 1;
                                // $.each(
                                // arrValues, function( intIndex, objValue){
                                // $("#" + n).attr("value", objValue);
                                // n++;
                                // }
                                // );
                                // var lat = arrValues[9];
                                // var lon = arrValues[10];
                                // setCenter(lat, lon);
                                // });
                                // });

                                $("#save_osm").click(function() {
                                    loading(true);
                                    var inst = $("#name").attr("value");
                                    var street = $("#strasse").attr("value");
                                    var plz = $("#plz").attr("value");
                                    var city = $("#ort").attr("value");
                                    var personname = $("#kontakt1-name").attr("value");
                                    var personvorname = $("#kontakt1-vorname").attr("value");
                                    var telefon = $("#telefon").attr("value");
                                    var fax = $("#fax").attr("value");
                                    var email = $("#email").attr("value");

                                    $.post("app/save_osm.php", { inst: inst, street: street, plz: plz, city: city, personname: personname, personvorname: personvorname, telefon: telefon, fax: fax, email: email}, function(resp) {
                                        saveVars("kontakt.shtml wurde erstellt");
                                    });
                                });
                            });
                            -->
                        </script>
<!--                        <form action="" method="post" name="frmEdit" id="frmEdit">
                            <fieldset>
                                <legend>OpenStreetMap-Kontaktseite erstellen</legend>
                            </fieldset>
                        </form>-->
                        <form action="" method="" id="suche" class="form-horizontal">
                            <div class="form-group">
								<label for="name" class="col-sm-2 control-label">Institut</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="name" name="inst" />
								</div>
							</div>
                            <div class="form-group">
								<label for="strasse" class="col-sm-2 control-label">Stra&szlig;e</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="strasse" name="street" />
								</div>
                            </div>
                            <div class="form-group">
								<label class="col-sm-2 control-label">PLZ, Ort</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" id="plz" name="plz" />
								</div>
                                <div class="col-sm-4">
									<input type="text" class="form-control" id="ort" name="city" />
								</div>
                            </div>
                            <div class="form-group">
								<label class="col-sm-2 control-label">Kontaktperson<br />(Name, Vorname)</label>
								<div class="col-sm-3">
									<input type="text" class="form-control" id="kontakt1-name" name="person-name" />
								</div>
								<div class="col-sm-3">
									<input type="text" class="form-control" id="kontakt1-vorname" name="person-vorname" />
								</div>
							</div>
                            <div class="form-group">
								<label for="telefon" class="col-sm-2 control-label">Telefon</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="telefon" name="telefon" />
								</div>
                            </div>
                            <div class="form-group">
								<label for="fax" class="col-sm-2 control-label">Fax</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="fax" name="fax" />
								</div>
                            </div>
                            <div class="form-group">
								<label for="email" class="col-sm-2 control-label">E-Mail</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="email" name="email" />
								</div>
                            </div>
                            <div class="form-footer">
                                <input id="save_osm" type="button" class="submit btn btn-light btn-large btn-success" name="submit" value="Kontaktseite erstellen" />
                                <input id="btnSaveall" type="button" class="submit btn btn-light btn-large btn-success" name="submit" value="Nur Variablen speichern" />
                            </div>
						</form>
					</div>
				</div>
			</div>
		</div>

<?php require('common_footer.php'); ?>

	</body>

</html>
