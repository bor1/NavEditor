<?php
require_once('auth.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Konfigurationen bearbeiten - <?php echo($ne_config_info['app_titleplain']); ?></title>

<?php
    echo NavTools::includeHtml(
            "default",
            "json2.js"
          );
?>

<script type="text/javascript">
<!--
var loadFeedImportDone = false;
var feedCounter = 0;

var loadWebsiteConfDone = false;

var loadVorlagenDone = false;

var loadConfDone = false;

var loadConfListDone = false;

var currentConfFileName = "";

function loadConfList() {
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_conf_list",
		"conf_file_name": ""
	}, loadConfListCallback);
}

function loadConfListCallback(rdata) {
	// TODO
	cflHtml = "";
	for(var i = 0; i < rdata.length; i++) {
		cflHtml += "<li><a href='javascript:;' rel='" + rdata[i].file_name + "'>" + rdata[i].file_name + "</a></li>";
	}
	// ne2_config is special
	cflHtml += "<li><a href='javascript:;' rel='ne2_config.conf'>ne2_config.conf</a></li>";
	$("#confList ul").html(cflHtml);
	loadConfListDone = true;

	$("#confList a").click(function() {
		var cfn = $(this).attr("rel");
		switch(cfn) {
			case "feedimport.conf":
				showPanel("fld_feedimport");
				loadFeedImport();
				break;
//			case "vorlagen.conf":
//				showPanel("fld_vorlagen");
//				loadVorlagen();
//				break;
			case ".htusers":
				showPanel("fld_htusers");
				loadHtUsers();
				break;
			case "hthosts":
				showPanel("fld_hthosts");
				loadHtHosts();
				break;
			case "<?php echo($ne_config_info['website_conf_filename']); ?>":
				showPanel("fld_website");
				$("#fld_common legend").text(cfn);
				loadWebsiteConf();
				break;
			default:
				showPanel("fld_common");
				$("#fld_common legend").text(cfn);
				currentConfFileName = cfn;
				loadConf(cfn);
				break;
		}
	});

	// init. panel
	showPanel("fld_feedimport");
	loadFeedImport();
}

/* load .htusers */
function loadHtUsers() {
	$("#htusers").html("Loading...");
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_htusers",
		"conf_file_name": ""
	}, function(rdata) {
		var huHtml = "";
		for(var i = 0; i < rdata.length; i++) {
			huHtml += "<div class='row'><div class='col-md-2' style='padding-top:8px;'>" + rdata[i].username + "</div>";
			huHtml += " <a rel='" + rdata[i].username + "' href='javascript:;' class='btn btn-danger btn-light'>L&ouml;schen</a></div>";
		}
		$("#htusers").html(huHtml);
		// delete htuser event
		$("#htusers div a").click(function() {
			if(confirm("Sind Sie sicher?")) {
				var un = $(this).attr("rel");
				$.post("app/edit_conf.php", {
					"oper": "delete_htuser",
					"conf_file_name": "",
					"username": un
				}, function(msg){
					alert(msg);
					loadHtUsers();
				});
			}
		});
	});
}

// load hthosts
function loadHtHosts() {
	$("#hthosts").html("Loading...");
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_hthosts",
		"conf_file_name": ""
	}, function(rdata) {
		var hhHtml = "";
		for(var j = 0; j < rdata.length; j++) {
			hhHtml += "<div class='row'><div class='col-md-2' style='padding-top:8px;'>" + rdata[j].host + "</div>";
			hhHtml += " <a rel='" + rdata[j].host + "' href='javascript:;' class='btn btn-danger btn-light'>L&ouml;schen</a></div>";
		}
		$("#hthosts").html(hhHtml);
		// delete hthost event
		$("#hthosts div a").click(function() {
			if(confirm("Sind Sie sicher?")) {
				var ht = $(this).attr("rel");
				$.post("app/edit_conf.php", {
					"oper": "delete_hthost",
					"conf_file_name": "",
					"host": ht
				}, function(msg) {
					alert(msg);
					loadHtHosts();
				});
			}
		});
	});
}

/* load normal conf-file */
function loadConf(confFileName) {
	$("#container").html("Loading...");
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_conf",
		"conf_file_name": confFileName
	}, loadConfCallback);
}

function loadConfCallback(rdata) {
	var ciHtml = "";
	for(var i = 0; i < rdata.length; i++) {
		var ciName = rdata[i].opt_name;
		var ciNameLabel = "<div class='form-group'><label type='varName' class='control-label col-md-3'>" + ciName + "</label> ";
		var ciValue = "<div class='controls col-md-9'><input type='text' class='textBox form-control' value='" + rdata[i].opt_value + "' /></div></div>";
		ciHtml += (ciNameLabel + ciValue);
	}
	$("#container").html(ciHtml);
	loadConfDone = true;
}

/*website.conf related */
function loadWebsiteConf() {
	var confFileName = 'website.conf';
	$("#websiteContainer").html("Loading...");
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_conf",
		"conf_file_name": confFileName
	}, loadWebsiteConfCallback);
}

function loadWebsiteConfCallback(rdata) {
	var ciHtml = "";
	for(var i = 0; i < rdata.length; i++) {
		var ciName = rdata[i].opt_name;
		var ciNameLabel = "<div class='form-group'><label type='varName' class='control-label col-md-3'>" + ciName + "</label> ";
		var ciValue = "<div class='controls col-md-9'><input type='text' class='textBox form-control' id='" + ciName + "' value='" + rdata[i].opt_value + "' /></div></div> ";
		ciHtml += (ciNameLabel + ciValue);
		if (ciName == 'name-des-Webauftritts') {
		ciHtml += "<div class='form-group'><div class='col-md-offset-3 col-md-9'><div class='checkbox'><label><input type='checkbox' id='chkAllowHtml' name='chkAllowHtml' value='ja' />Eigene HTML-Anweisungen zulassen</label></div></div></div>";
		}
	}
	$("#websiteContainer").html(ciHtml);
	loadWebsiteConfDone = true;
}

function saveContentCallback(data) {
	$("#ajaxLoader").hide();
	alert(data);
	saveVars(data);
}
//for every button save vars
function saveVars(data){
	var poData = [];
	$("#websiteContainer div").each(function() {
		var ccName = $(this).find("label[type=varName]").text();
		var ccValue = $(this).find("input[type=text]").val();
		var ccItem = {
			"opt_name": ccName,
			"opt_value": ccValue
		};
		poData.push(ccItem);
	});
	$("#genWebItemsContainer div").each(function() {
		var gcName = $(this).find("input[name=txtGenItemCf_Name]").val();
		var gcValue = $(this).find("input[name=txtGenItemCf_Val]").val();
		var gcItem = {
			"opt_name": gcName,
			"opt_value": gcValue
		};
		poData.push(gcItem);
	});
		$.post("app/edit_conf.php", {
			"oper": "set_conf",
			"conf_file_name": 'website.conf',
			"jsonData": JSON.stringify(poData)
		}, function(rdata) {
			alert(data);
			$("#genWebItemsContainer").html("<div class='mi'></div>");
			loadWebsiteConf();
		});
}


/* vorlagen.conf related */
function loadVorlagen() {
	// vorlagen
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_vorlagen",
		"conf_file_name": "vorlagen.conf"
	}, loadVorlagenCallback); // load tree data
}

function loadVorlagenCallback(data) {
	var chkHtml = "";
	for(var i = 0; i < data.length; i++) {
		chkHtml += "<label for=\"chkvlgc" + i.toString() + "\">" + data[i].item + "</label> ";
		chkHtml += "<input type=\"checkbox\" id=\"chkvlgc" + i.toString() + "\"" + (data[i].value == 1 ? " checked=\"checked\"" : "") + " value=\"" + data[i].item + "\" />";
		chkHtml += "<br />";
	}
	$("#vorlagen").html(chkHtml);
	loadVorlagenDone = true;
}

function saveVorlagenCallback(data) {
	alert(data);
	location.reload();
}

function buildJSONVorlagen() {
	var arrJSON = [];
	var chks = $("#vorlagen :checkbox");
	for(var i = 0; i < chks.length; i++) {
		arrJSON.push(
			{
				"item": chks[i].value,
				"value": chks[i].checked == true ? 1 : 0
			}
		);
	}
	return JSON.stringify(arrJSON);
}

/* feedimport.conf related */
function loadFeedImport() {
	// feedimport
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_feedimport",
		"conf_file_name": "feedimport.conf"
	}, loadFeedImportCallback);
}

function loadFeedImportCallback(rdata) {
	feedHtml = "";
	for(var i = 0; i < rdata.feeds.length; i++) {
		var feedId = rdata.feeds[i].id;
		var feedIdLabel = "<div class='form-group'><label class='control-label col-md-1'>Feed-" + feedId + "</label> ";
		var feedTitle = "<div class='controls col-md-3'><input type='text' id='txtFeedTitle_" + feedId + "' class='textBox form-control' value='" + rdata.feeds[i].title + "' /></div> ";
		var feedUrl = "<div class='controls col-md-5'><input type='text' id='txtFeedUrl_" + feedId + "' class='textBox form-control' value='" + rdata.feeds[i].url + "' /></div><div class='controls col-md-3'><a href='javascript:;' class='btn btn-danger btn-light'>L&ouml;schen</a></div></div>";
		feedHtml += (feedIdLabel + feedTitle + feedUrl);
		feedCounter++;
	}
	$("#feedimport").html(feedHtml + "<div class='mi'></div>");

	feedOptHtml = "";
	for(var j = 0; j < rdata.options.length; j++) {
		var feedOptName = rdata.options[j].opt_name;
		var feedOptNameLabel = "<div class='form-group'><label class='control-label col-md-2'>" + feedOptName + "</label> ";
		var feedOptValue = "<div class='controls col-md-5'><input type='text' class='textBox form-control' value='" + rdata.options[j].opt_value + "' /></div></div>";
		feedOptHtml += (feedOptNameLabel + feedOptValue);
	}
	$("#feedimportOpts").html(feedOptHtml);

	// add event for removing
	$("#feedimport div a").click(function() {
		if(confirm("Möchten Sie diesen Eintrag wirklich entfernen?")) {
			$(this).closest('div.form-group').remove();
		}
	});

	loadFeedImportDone = true;
}

function showPanel(panelId) {
	$("#contentPanel2 fieldset").hide(); // hide all
	$("#contentPanel2 #" + panelId).show();
}

/* ---------- Here comes jQuery: ---------- */
$(document).ready(function() {
	/* website.conf buttons functions begin */
	$("#btnUpdate").click(function() {
		if(!loadWebsiteConfDone) {
			return;
		}
		var text = $("#name-des-Webauftritts").val();
		var desc = $("#kurzbeschreibung-zum-Webauftritt").val();
		var imgUrl = $("#logo-URL").val();
		var imgAlt = $("#logo-Alt").val();
		var siteTitle = $("#titel-des-Webauftritts").val();
		var imgH = $("#logo-Height").val();
		var imgW = $("#logo-Width").val();

		// if image specified, then alt-text cannot be empty!
		if(imgUrl != "") {
			if(imgAlt == "") {
				alert("Bitte geben Sie die Beschreibung zu Ihrem Logobild an!");
				return false;
			}
		}

		if(confirm("Möchten Sie die Daten wirklich speichern?")) {
			if(imgUrl != "") {
				img = "<img alt=\"" + imgAlt + "\" src=\"" + imgUrl + "\" width=\"" + imgW + "\" height=\"" + imgH + "\" border=\"0\" />";
			}
			btnNameTmp = $(this).val();
			$(this).val("Moment...");
			$(this).attr("disabled", "disabled");
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
			}, function(data){
			$("#btnUpdate").val(btnNameTmp);
			$("#btnUpdate").removeAttr("disabled");
			saveContentCallback(data);
			});
		}
	});

	$("#btnUpdateExisted").click(function() {
		if(!loadWebsiteConfDone) {
			return;
		}
		if(confirm("Möchten Sie das Logo auf allen existierenden Seiten ersetzen?")) {
			$("#ajaxLoader").show();
			btnNameTmp = $(this).val();
			$(this).val("Moment...");
			$(this).attr("disabled", "disabled");
			var text = $("#name-des-Webauftritts").val();
			var desc = $("#kurzbeschreibung-zum-Webauftritt").val();
			var imgUrl = $("#logo-URL").val();
			var imgAlt = $("#logo-Alt").val();
			var siteTitle = $("#titel-des-Webauftritts").val();
			var imgH = $("#logo-Height").val();
			var imgW = $("#logo-Width").val();

			if(imgUrl != "") {
				img = "<img alt=\"" + imgAlt + "\" src=\"" + imgUrl + "\" width=\"" + imgW + "\" height=\"" + imgH + "\" border=\"0\" />";
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
			}, function(data){
			$("#btnUpdateExisted").val(btnNameTmp);
			$("#btnUpdateExisted").removeAttr("disabled");
			saveContentCallback(data);
			});
		}
	});



	$("#save_osm").click(function() {
		if(!loadWebsiteConfDone) {
			return;
		}
		btnNameTmp = $(this).val();
		$(this).val("Moment...");
		$(this).attr("disabled", "disabled");
		var inst = $("#name").attr("value");
		var street = $("#strasse").attr("value");
		var plz = $("#plz").attr("value");
		var city = $("#ort").attr("value");
		var personname = $("#kontakt1-name").attr("value");
		var personvorname = $("#kontakt1-vorname").attr("value");
		var telefon = $("#telefon").attr("value");
		var fax = $("#fax").attr("value");
		var email = $("#email").attr("value");
		var lat = $("#geo-lat").attr("value");
		var lon = $("#geo-long").attr("value");

		$.post("app/save_osm.php", { inst: inst, street: street, plz: plz, city: city, personname: personname, personvorname: personvorname, telefon: telefon, fax: fax, email: email, lat: lat, lon: lon}, function(resp) {
			//alert("kontakt.shtml wurde erstellt");
			$("#save_osm").val(btnNameTmp);
			$("#save_osm").removeAttr("disabled");
			saveVars("kontakt.shtml wurde erstellt");
			$('#hr').append('<p>Fertig.</p>');
		});
	});

	$("#btnWebAddConf").click(function() {
		var genItemHtml = "";
		var newItemHtml = "<div class='form-group'><div class='controls col-md-3'><input type='text' name='txtGenItemCf_Name' class='textBox form-control' /></div>";
		newItemHtml += " <div class='controls col-md-6'><input type='text' name='txtGenItemCf_Val' class='textBox form-control' /></div><div class='controls col-md-3'><a href='javascript:;' class='btn btn-danger btn-light'>L&ouml;schen</a></div></div>";
		$("#genWebItemsContainer .mi").replaceWith(genItemHtml + newItemHtml + "<div class='mi'></div>");

		// for removing
		$("#genWebItemsContainer a").click(function() {
			if(confirm("Wollen Sie diesen Eintrag wirklich löschen?")) {
				$(this).parents(".form-group").remove();
			}
		});
	});

	$("#btnWebUpdConf").click(function() {
		if(!loadWebsiteConfDone) {
			return;
		}
		var poData = [];
		$("#websiteContainer p").each(function() {
			var ccName = $(this).find("label[type=varName]").text();
			var ccValue = $(this).find("input[type=text]").val();
			var ccItem = {
				"opt_name": ccName,
				"opt_value": ccValue
			};
			poData.push(ccItem);
		});
		$("#genWebItemsContainer p").each(function() {
			var gcName = $(this).find("input[name=txtGenItemCf_Name]").val();
			var gcValue = $(this).find("input[name=txtGenItemCf_Val]").val();
			var gcItem = {
				"opt_name": gcName,
				"opt_value": gcValue
			};
			poData.push(gcItem);
		});
//		alert(JSON.stringify(poData));
		if(confirm("Möchten Sie wirklich speichern?")) {
			$(this).val("Moment...");
			$(this).attr("disabled", "disabled");
			$.post("app/edit_conf.php", {
				"oper": "set_conf",
				"conf_file_name": 'website.conf',
				"jsonData": JSON.stringify(poData)
			}, function(rdata) {
				alert(rdata);
				$("#btnWebUpdConf").val("Nur Variablen speichern");
				$("#btnWebUpdConf").removeAttr("disabled");
				$("#genWebItemsContainer").html("<div class='mi'></div>");
				loadWebsiteConf();
			});
		}
	});
	/* website.conf buttons functions end */



	/* Vorlagen related */
	$("#btnUpdVorlagen").click(function() {
		if(!loadVorlagenDone) {
			return;
		}

		if(confirm("Möchten Sie die Daten wirklich speichern?")) {
			$.post("app/edit_conf.php", {
				"oper": "set_vorlagen",
				"json_data": buildJSONVorlagen(),
				"conf_file_name": "vorlagen.conf"
			}, saveVorlagenCallback);
		}
	});
	/* /Vorlagen related */

	/* Feed-Import related */
	$("#btnAddFeedBox").click(function() {
		if(loadFeedImportDone) {
			feedCounter++;
			var newFeedBoxHtml = "<div class='form-group'><label class='control-label col-md-1'>Feed-" + feedCounter + "</label> ";
			newFeedBoxHtml += "<div class='controls col-md-3'><input type='text' id='txtFeedTitle_" + feedCounter + "' class='textBox form-control' /></div> ";
			newFeedBoxHtml += "<div class='controls col-md-5'><input type='text' id='txtFeedUrl_" + feedCounter + "' class='textBox form-control' /></div><div class='controls col-md-3'><a href='javascript:;' class='btn btn-danger btn-light'>L&ouml;schen</a></div></div>";
			$("#feedimport .mi").replaceWith(newFeedBoxHtml + "<div class='mi'></div>");

			// add event for removing
			$("#feedimport div a").click(function() {
				if(confirm("Möchten Sie diesen Eintrag wirklich entfernen?")) {
					$(this).closest('div.form-group').remove();
				}
			});
		}
	});

	$("#btnAddItemFeedImport").click(function() {
		var genItemHtml = "";
//		genItemHtml += $("#feedimportGenItems").html();
		var newItemHtml = "<div class='form-group'><div class='controls col-md-3'><input type='text' name='txtGenItemFI_Name' class='textBox form-control' /></div>";
		newItemHtml += " <div class='controls col-md-6'><input type='text' name='txtGenItemFI_Val' class='textBox form-control' /></div><div class='controls col-md-3'><a href='javascript:;' class='btn btn-danger btn-light'>L&ouml;schen</a></div></div>";
		$("#feedimportGenItems .mi").replaceWith(genItemHtml + newItemHtml + "<div class='mi'></div>");

		// for removing
		$("#feedimportGenItems div a").click(function() {
			if(confirm("Möchten Sie diesen Eintrag wirklich entfernen?")) {
				$(this).closest('div.form-group').remove();
			}
		});
	});

	$("#btnUpdFeedImport").click(function() {
		if(!loadFeedImportDone) {
			return;
		}

		var postData = {
			"feeds": [],
			"options": [],
			"general_items": []
		};
		$("#feedimport div").each(function() {
			var feedIdStr = $(this).find("label").text();
			var feedId = feedIdStr.split("-")[1];
			var feedTitle = $(this).find("input[id^='txtFeedTitle']").value;
			var feedUrl = $(this).find("input[id^='txtFeedUrl']").value;
			var feedItem = {
				"id": feedId,
				"title": feedTitle,
				"url": feedUrl
			};
			postData.feeds.push(feedItem);
		});
		$("#feedimportOpts div").each(function() {
			var foName = $(this).find("label").text();
			var foValue = $(this).find("div").find("input").val();
			var foItem = {
				"opt_name": foName,
				"opt_value": foValue
			}
			postData.options.push(foItem);
		});
		$("#feedimportGenItems div").each(function() {
			var fgName = $(this).find("div").find("input[name=txtGenItemFI_Name]").val();
			var fgValue = $(this).find("div").find("input[name=txtGenItemFI_Val]").val();
			var fgItem = {
				"gi_name": fgName,
				"gi_value": fgValue
			};
			postData.general_items.push(fgItem);
		});
		if(confirm("Möchten Sie wirklich speichern?")) {
			$(this).val("Moment...");
			$(this).attr("disabled", "disabled");
			$.post("app/edit_conf.php", {
				"oper": "set_feedimport",
				"conf_file_name": "feedimport.conf",
				"jsonData": JSON.stringify(postData)
			}, function(rdata) {
				alert(rdata);
				$("#btnUpdFeedImport").val("Speichern");
				$("#btnUpdFeedImport").removeAttr("disabled");
				location.reload();
			});
		}
	});

	/* common conf: post */
	$("#btnAddConf").click(function() {
		var genItemHtml = "";
		var newItemHtml = "<div class='form-group'><div class='controls col-md-3'><input type='text' name='txtGenItemCf_Name' class='textBox form-control' /></div>";
		newItemHtml += " <div class='controls col-md-6'><input type='text' name='txtGenItemCf_Val' class='textBox form-control' /></div><div class='controls col-md-3'><a href='javascript:;' class='btn btn-danger btn-light'>L&ouml;schen</a></div></div>";
		$("#genItemsContainer .mi").replaceWith(genItemHtml + newItemHtml + "<div class='mi'></div>");

		// for removing
		$("#genItemsContainer a").click(function() {
			if(confirm("Wollen Sie diesen Eintrag wirklich löschen?")) {
				$(this).parents(".form-group").remove();
			}
		});
	});

	$("#btnUpdConf").click(function() {
		if(!loadConfDone) {
			return;
		}
		var poData = [];
		$("#container div").each(function() {
			var ccName = $(this).find("label").text();
			var ccValue = $(this).find("input").val();
			var ccItem = {
				"opt_name": ccName,
				"opt_value": ccValue
			};
			poData.push(ccItem);
		});
		$("#genItemsContainer div").each(function() {
			var gcName = $(this).find("input[name=txtGenItemCf_Name]").val();
			var gcValue = $(this).find("input[name=txtGenItemCf_Val]").val();
			var gcItem = {
				"opt_name": gcName,
				"opt_value": gcValue
			};
			poData.push(gcItem);
		});
//		alert(JSON.stringify(poData));
		if(confirm("Möchten Sie wirklich speichern?")) {
			$(this).val("Moment...");
			$(this).attr("disabled", "disabled");
			$.post("app/edit_conf.php", {
				"oper": "set_conf",
				"conf_file_name": currentConfFileName,
				"jsonData": JSON.stringify(poData)
			}, function(rdata) {
				alert(rdata);
				$("#btnUpdConf").val("Speichern");
				$("#btnUpdConf").removeAttr("disabled");
				$("#genItemsContainer").html("<div class='mi'></div>");
				loadConf(currentConfFileName);
			});
		}
	});

	// add new htuser
	$("#btnAddNewHtUser").click(function() {
		var un = $("#txtNewHtUserName").val();
		var pw = $("#txtNewHtUserPass").val();
		if(un != "" && pw != "") {
			$.post("app/edit_conf.php", {
				"oper": "add_htuser",
				"conf_file_name": "",
				"username": un,
				"password": pw
			}, function(msg) {
				alert(msg);
				loadHtUsers();
			});
			$("#txtNewHtUserName").val("");
			$("#txtNewHtUserPass").val("");
		}
	});

	// add new hthost
	$("#btnAddNewHtHost").click(function() {
		var host = $("#txtNewHtHost").val();
		if(host != "") {
			$.post("app/edit_conf.php", {
				"oper": "add_hthost",
				"conf_file_name": "",
				"host": host
			}, function(msg) {
				alert(msg);
				loadHtHosts();
			});
		}
	});

	loadConfList();

});
// -->
</script>
</head>

<body id="bd_Conf">
	<?php require('common_nav_menu.php'); ?>

	<div class="container" id="wrapper">

		<div class="page-header">
            <h2 class="page-header">Konfigurationsdateien bearbeiten</h2>
        </div>

		<div class="row">

			<div class="col-md-3" id="confList">
				<h4>Konfigurationsdateien</h4>
					<ul></ul>
			</div>

			<div class="col-md-9 page" id="contentPanel2">
				<form id="frmEdit" class="form-horizontal">
					<fieldset id="fld_feedimport">
						<legend>feedimport.conf</legend>
						<div id="feedimport">Loading...<div class="mi"></div></div>
						<hr size="1" noshade="noshade" />
						<div id="feedimportOpts"></div>
						<div id="feedimportGenItems"><div class="mi"></div></div>
						<hr size="1" noshade="noshade" />
						<input type="button" id="btnAddFeedBox" value="Feed einf&uuml;gen" class="btn btn-primary btn-light" style="margin:5px;" />
						<input type="button" id="btnAddItemFeedImport" value="Konfig-Eintrag einf&uuml;gen" class="btn btn-primary btn-light" style="margin:5px;" />
						<input type="button" id="btnUpdFeedImport" value="Speichern" class="btn btn-success btn-light" style="margin:5px;" />
					</fieldset>

					<fieldset id="fld_website">
						<legend>website.conf</legend>
						<div id="websiteContainer">Loading...</div>
						<div id="genWebItemsContainer"><div class="mi"></div></div>
						<hr size="1" noshade="noshade" />
						<input type="button" id="btnWebAddConf" value="Eintrag einf&uuml;gen" class="btn btn-primary btn-light" />
						<input type="button" id="btnWebUpdConf" value="Nur Variablen speichern" class="btn btn-warning btn-light" />
						<br><br>
						<input type="button" id="btnUpdate" name="btnUpdate" value="Vorlagen aktualisieren" class="btn btn-success btn-light" />
						<input type="button" id="btnUpdateExisted" name="btnUpdateExisted" value="Existierende Seiten aktualisieren" class="btn btn-success btn-light" />
						<input type="hidden" id="selTempl" name="selTempl" value="seitenvorlage.html">
						<input id="save_osm" type="button" class="btn btn-primary btn-light" name="submit" value="Kontaktseite erstellen" />
					</fieldset>

					<fieldset id="fld_common">
						<legend></legend>
						<div id="container">Loading...</div>
						<div id="genItemsContainer"><div class="mi"></div></div>
						<hr size="1" noshade="noshade" />
						<input type="button" id="btnAddConf" value="Eintrag einf&uuml;gen" class="btn btn-primary btn-light" />
						<input type="button" id="btnUpdConf" value="Speichern" class="btn btn-success btn-light" />
					</fieldset>

					<fieldset id="fld_vorlagen">
						<legend>vorlagen.conf</legend>
						<div id="vorlagen">Loading...</div>
						<hr size="1" noshade="noshade" />
						<input type="button" id="btnUpdVorlagen" value="Speichern" class="btn btn-success btn-light" />
					</fieldset>

					<fieldset id="fld_htusers">
						<legend>.htusers</legend>
						<div id="htusers"></div>
						<hr size="1" noshade="noshade" />
						<div class="form-group">
							<label for="txtNewHtUserName" class="col-md-2 control-label">Username</label>
							<div class="col-sm-3">
								<input type="text" id="txtNewHtUserName" class="textBox form-control" />
							</div>
							<label for="txtNewHtUserPass" class="col-md-2 control-label">Password</label>
							<div class="col-sm-3">
								<input type="password" id="txtNewHtUserPass" class="txtBox form-control" />
							</div>
							<input type="button" id="btnAddNewHtUser" value="Hinzufügen" class="btn btn-success btn-light" />
						</div>
					</fieldset>

					<fieldset id="fld_hthosts">
						<legend>hthosts</legend>
						<div id="hthosts"></div>
						<hr size="1" noshade="noshade" />
						<div class="form-group">
							<label for="txtNewHtHost" class="col-md-2 control-label">Domain/IP</label>
							<div class="col-sm-5">
								<input type="text" id="txtNewHtHost" class="textBox form-control" />
							</div>
							<input type="button" id="btnAddNewHtHost" value="Hinzufügen" class="btn btn-success btn-light" />
						</div>
					</fieldset>
				</form>
			</div>
		</div>

	</div>

<?php require('common_footer.php'); ?>

</body>

</html>
