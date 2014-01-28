<?php
require_once('auth.php');

$custom_css_classes = '';
if($ne_config_info['custom_content_css_classes'] != '') {
	$custom_css_classes = array();
	$arr_cls = explode('|', $ne_config_info['custom_content_css_classes']);
	foreach($arr_cls as $ac) {
		array_push($custom_css_classes, $ac . '=' . $ac);
	}
	$custom_css_classes = implode(';', $custom_css_classes);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mitarbeiter bearbeiten - <?php echo($ne_config_info['app_titleplain']); ?></title>
<?php
echo NavTools::includeHtml(
        'default',
        'tinymce/tinymce.min.js',
        'json2.js',
        'ajaxfileupload.js'
);
?>

<script type="text/javascript">
//tinyMCE.init({
//	mode: "textareas",
//	language: "de",
//	theme: "advanced",
//	skin: "o2k7",
//	relative_urls: false,
//	convert_urls: false,
//	plugins: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
//
//	theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,styleselect,|,bullist,numlist,outdent,indent,|,cut,copy,paste,pastetext,pasteword,|,undo,redo",
//	theme_advanced_buttons2: "link,unlink,anchor,image,cleanup,|,charmap,emotions,iespell,|,ltr,rtl,|,fullscreen,help,code,|,addIB1,addIB2,addIB3",
//	theme_advanced_buttons3: "",
//
//	theme_advanced_toolbar_location: "top",
//	theme_advanced_toolbar_align: "left",
//	theme_advanced_statusbar_location: "bottom",
//	theme_advanced_blockformats: "p,address,pre,h2,h3,h4,h5,h6,blockquote,code",
//	theme_advanced_styles: "<?php echo($custom_css_classes); ?>",
//	setup: function(ed) {
//		// add a custom button
//		ed.addButton("addIB1", {
//			title: "Inhaltsblock-1 einfügen",
//			image: "<? echo NE_DIR_RELATIVE; ?>css/ib1.gif",
//			onclick: function() {
//				ed.focus();
//				var divId = "custom" + Math.random();
//				divId = divId.replace(/\./, "_");
//				ed.selection.setContent("<h2><a href=\"javascript:anzeigen('" + divId + "')\">Titeltext</a></h2><div id=\"" + divId + "\" style=\"display: block;\"><ul><li>List-Item-1</li><li>List-Item-2</li></ul><p class=\"noprint\"><a href=\"javascript:anzeigen('" + divId + "')\">Schlie&szlig;en</a></p></div>");
//			}
//		});
//		// add a custom button
//		ed.addButton("addIB2", {
//			title: "Inhaltsblock-2 einfügen",
//			image: "<? echo NE_DIR_RELATIVE; ?>css/ib2.gif",
//			onclick: function() {
//				ed.focus();
//				var divId = "custom" + Math.random();
//				divId = divId.replace(/\./, "_");
//				ed.selection.setContent("<h3><a href=\"javascript:anzeigen('" + divId + "')\">Titeltext</a></h3><div id=\"" + divId + "\" style=\"display: block;\"><ul><li>List-Item-1</li><li>List-Item-2</li></ul><p class=\"noprint\"><a href=\"javascript:anzeigen('" + divId + "')\">Schlie&szlig;en</a></p></div>");
//			}
//		});
//		// add a custom button
//		ed.addButton("addIB3", {
//			title: "Inhaltsblock-3 einfügen",
//			image: "<? echo NE_DIR_RELATIVE; ?>css/ib3.gif",
//			onclick: function() {
//				ed.focus();
//				var divId = "custom" + Math.random();
//				divId = divId.replace(/\./, "_");
//				ed.selection.setContent("<h4><a href=\"javascript:anzeigen('" + divId + "')\">Titeltext</a></h4><div id=\"" + divId + "\" style=\"display: block;\"><ul><li>List-Item-1</li><li>List-Item-2</li></ul><p class=\"noprint\"><a href=\"javascript:anzeigen('" + divId + "')\">Schlie&szlig;en</a></p></div>");
//			}
//		});
//	}
//});



tinyMCE.init({
	forced_root_block : '',
	mode: "textareas",
	language: "de",
//	plugins: "image link code table preview mitarbeiter feedimport ssiInclude image_choose",
	plugins: "pagebreak,layer,table,save,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,template",
	menubar: false,
//	toolbar1: "undo redo | cut copy paste | link image table | outdent indent | code | preview | mitarbeiter | feedimport",
//	toolbar2: "fontselect fontsizeselect | styleselect | alignleft aligncenter alignright alignjustify | bold italic underline strikethrough | bullist numlist",
	toolbar1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,styleselect,|,bullist,numlist,outdent,indent,|,cut,copy,paste,pastetext,pasteword,|,undo,redo",
	toolbar2: "link,unlink,anchor,image,cleanup,|,charmap,emotions,iespell,|,ltr,rtl,|,fullscreen,help,code,|,addIB1,addIB2,addIB3",
	relative_urls: false,
	convert_urls: false,
	//plugins: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	theme_advanced_styles: "infologo",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	theme_advanced_statusbar_location: "bottom",
	oninit: function() {
		tinymceReady = true;
	},

    setup: function(ed) {
		// add a custom button
		ed.addButton("addIB1", {
			title: "Inhaltsblock-1 einfügen",
			image: "<?php echo NE_DIR_RELATIVE; ?>css/ib1.gif",
			onclick: function() {
				ed.focus();
				var divId = "custom" + Math.random();
				divId = divId.replace(/\./, "_");
				ed.selection.setContent("<h2><a href=\"javascript:anzeigen('" + divId + "')\">Titeltext</a></h2><div id=\"" + divId + "\" style=\"display: block;\"><ul><li>List-Item-1</li><li>List-Item-2</li></ul><p class=\"noprint\"><a href=\"javascript:anzeigen('" + divId + "')\">Schlie&szlig;en</a></p></div>");
			}
		});
		// add a custom button
		ed.addButton("addIB2", {
			title: "Inhaltsblock-2 einfügen",
			image: "<?php echo NE_DIR_RELATIVE; ?>css/ib2.gif",
			onclick: function() {
				ed.focus();
				var divId = "custom" + Math.random();
				divId = divId.replace(/\./, "_");
				ed.selection.setContent("<h3><a href=\"javascript:anzeigen('" + divId + "')\">Titeltext</a></h3><div id=\"" + divId + "\" style=\"display: block;\"><ul><li>List-Item-1</li><li>List-Item-2</li></ul><p class=\"noprint\"><a href=\"javascript:anzeigen('" + divId + "')\">Schlie&szlig;en</a></p></div>");
			}
		});
		// add a custom button
		ed.addButton("addIB3", {
			title: "Inhaltsblock-3 einfügen",
			image: "<?php echo NE_DIR_RELATIVE; ?>css/ib3.gif",
			onclick: function() {
				ed.focus();
				var divId = "custom" + Math.random();
				divId = divId.replace(/\./, "_");
				ed.selection.setContent("<h4><a href=\"javascript:anzeigen('" + divId + "')\">Titeltext</a></h4><div id=\"" + divId + "\" style=\"display: block;\"><ul><li>List-Item-1</li><li>List-Item-2</li></ul><p class=\"noprint\"><a href=\"javascript:anzeigen('" + divId + "')\">Schlie&szlig;en</a></p></div>");
			}
		});
	}
});
</script>

<script type="text/javascript">
<!--
var loadMAListDone = false;
var currentFileName = "";

function ajaxFileUpload(maFileName) {
	$("#ajaxWaiting")
	.ajaxStart(function() {
		$(this).show();
	})
	.ajaxComplete(function() {
		$(this).hide();
	});

	$.ajaxFileUpload({
		"url": "app/ma_photo_upload.php",
		"secureuri": false,
		"fileElementId": "filAttachment",
		"customHiddenData": "NAME|" + maFileName,
		"maxFileSize": 1048576 * 8,
		"dataType": "json",
		"success": function(data, status) {
		},
		"error": function(data, status, e) {
			alert("Oops... " + e);
		}
	});

	return false;
}

function maNameEncode(maName) {
	var fname = maName;
	fname = fname.toLowerCase();

	// east euro chars? aeiou
	fname = fname.replace(/\u00e0/g, "a"); // à
	fname = fname.replace(/\u00e1/g, "a"); // á
	fname = fname.replace(/\u00e2/g, "a"); // â
	fname = fname.replace(/\u00e3/g, "a"); // ã
	fname = fname.replace(/\u00e5/g, "a"); // å

	fname = fname.replace(/\u00e7/g, "c"); // ç

	fname = fname.replace(/\u00e8/g, "e"); // è
	fname = fname.replace(/\u00e9/g, "e"); // é
	fname = fname.replace(/\u00ea/g, "e"); // ê
	fname = fname.replace(/\u00eb/g, "e"); // ë

	fname = fname.replace(/\u00ec/g, "i"); // ì
	fname = fname.replace(/\u00ed/g, "i"); // í
	fname = fname.replace(/\u00ee/g, "i"); // î
	fname = fname.replace(/\u00ef/g, "i"); // ï

	fname = fname.replace(/\u00f0/g, "o"); // ð
	fname = fname.replace(/\u00f2/g, "o"); // ò
	fname = fname.replace(/\u00f3/g, "o"); // ó
	fname = fname.replace(/\u00f4/g, "o"); // ô
	fname = fname.replace(/\u00f5/g, "o"); // õ

	fname = fname.replace(/\u00f9/g, "u"); // ù
	fname = fname.replace(/\u00fa/g, "u"); // ú
	fname = fname.replace(/\u00fb/g, "u"); // û

	// German chars
	fname = fname.replace(/\u00df/g, "ss");
	fname = fname.replace(/\u00e4/g, "ae");
	fname = fname.replace(/\u00f6/g, "oe");
	fname = fname.replace(/\u00fc/g, "ue");

	// misc
	fname = fname.replace(/\(.*\)/, ""); // remove parentheses
	fname = fname.replace(/^\s+|\s+$/g, ""); // trim
	fname = fname.replace(/([^\w.\-_])/g, "-"); // other chars to "-"
	fname = fname.replace(/-{2,}/, "-"); // no more than one adjacent "-"

	return fname;
}

function maNameDecode(flName) {
	var maName = flName;
	maName = maName.replace(".txt", "");
	var maNames = maName.split("-");
	for(var i = 0; i < maNames.length; i++) {
		var tmp = maNames[i]
				.replace(/ss/g, "&szlig;")
				.replace(/oe/g, "&ouml;");

		maNames[i] = tmp.slice(0, 1).toUpperCase() + tmp.slice(1);
	}
	maName = maNames.join(" ");
	return maName;
}

function loadFileContent(fileName, displayName) {
	$.post("app/edit_ma.php", {
		"oper": "get_content",
		"ma_file_name": fileName
	}, function(rdata) {
		jdata = JSON.parse(rdata);
		currentFileName = fileName;
		var names = displayName.split(" ");
		$("#txtFirstName").val(names[0]);
		$("#txtLastName").val(jQuery.trim(displayName.replace(names[0], "")));
		$("#filAttachment").val("");

		// process the html content, show hidden divs
		var oriContent = jdata.file_content;
		// server ssi tag workaround
		var rdata = oriContent.replace(/<comment_ssi>/g, "<!-" + "-#");
		rdata = rdata.replace(/<comment>/g, "<!-" + "-");
		rdata = rdata.replace(/<\/comment>/g, "-" + "->");
		var modContent = rdata.replace(/style="display: none;"/ig, "style=\"display: block;\"");

		tinyMCE.get("txtContent").setContent(modContent);
		if(jdata.photo_url != "") {
			$("#userPhoto").attr("src", jdata.photo_url);
			$("#userPhoto").show();
		} else {
			$("#userPhoto").hide();
		}
	});
}

function loadUnivISId() {
	$.get("app/edit_ma.php?r=" + Math.random(), {
		"oper": "get_univis_id",
		"ma_file_name": ""
	}, function(rdata) {
		$("#txtUnivISId").val(rdata);
	});
}

function loadMAList() {
	$.getJSON("app/edit_ma.php?r=" + Math.random(), {
		"oper": "get_ma_file_list",
		"ma_file_name": ""
	}, loadMAListCallback);
}

function loadMAListCallback(rdata) {
	htmlString = "";
	for(var i = 0; i < rdata.length; i++) {
		htmlString += "<li style='position:relative;'><a class='ma_edit' href='javascript:;' rel='" + rdata[i].file_name + "'>" + maNameDecode(rdata[i].file_name) + "</a><div style='position:absolute;bottom:10%;right:0;'><a class='ma_delete' rel='" + rdata[i].file_name + "' href='javascript:;'><b>X</b></a></div></li>";
	}
	$("#confList ul").html(htmlString);
	loadMAListDone = true;

	$("#confList ul li a.ma_edit").click(function() {
		var cfn = $(this).attr("rel");
		var dis = $(this).html();
		loadFileContent(cfn, dis);
	});

	$("#confList ul li a.ma_delete").click(function() {
		var cfn = $(this).attr("rel");
		if(confirm('Sind Sie sicher?')) {
			$.post("app/edit_ma.php", {
				"oper": "delete",
				"ma_file_name": cfn
			}, function(rdata) {
				alert(rdata);
				location.reload();
			});
		}
	});
}

/* ---------- Here comes jQuery: ---------- */
$(document).ready(function() {
	loadUnivISId();
	loadMAList();

	$("#btnAddNew").click(function() {
		$("#txtFirstName").val("");
		$("#txtLastName").val("");
		$("#filAttachment").val("");
		tinyMCE.get("txtContent").setContent("");
		currentFileName = "";
		$("#txtFirstName").focus();
	});

	$("#btnUpdate").click(function() {
		var fName = $("#txtFirstName").val();
		var lName = $("#txtLastName").val();
		var content = tinyMCE.get("txtContent").getContent();
		if(fName == "" || lName == "" || content == "") {
			alert("All fields must be filled!");
			return false;
		}
		var maName = fName + " " + lName;
		if(currentFileName == "") {
			currentFileName = maNameEncode(maName) + ".txt";
		}
		var hasPhoto = false;
		if($("#filAttachment").val() != "") {
			hasPhoto = true;
		}

		// restore the content (change all shown divs back to hidden)
		var mContent = content.replace(/style="display: block;"/ig, "style=\"display: none;\"");
		mContent = mContent.replace(new RegExp("<!-" + "-#", "g"), "<comment_ssi>");
		mContent = mContent.replace(/<!--/g, "<comment>");
		mContent = mContent.replace(/-->/g, "</comment>");

		$.post("app/edit_ma.php", {
			"oper": "update_content",
			"ma_file_name": currentFileName,
			"file_content": mContent
		}, function(rdata) {
			// then do upload
			if(hasPhoto) {
				ajaxFileUpload(maNameEncode(maName) + ".jpg");
			}

			alert(rdata);
			loadMAList();
		});
	});

	$("#btnUpload").click(function() {
		var fName = $("#txtFirstName").val();
		var lName = $("#txtLastName").val();
		if(fName == "" || lName == "") {
			alert("Bitte Vor- und Nachname eingeben!");
			return false;
		}
		var maName = fName + " " + lName;
		if($("#filAttachment").val() != "") {
			ajaxFileUpload(maNameEncode(maName) + ".jpg");
		} else {
			alert("Bitte ein Foto wählen!");
			return false;
		}
	});

	$("#btnSetUnivISId").click(function() {
		var newUnivISId = $("#txtUnivISId").val();
		if(newUnivISId == "") {
			alert("ID darf nicht leer sein!");
			return false;
		}

		if(confirm("Ist die neue UnivIS-ID richtig?")) {
			$.post("app/edit_ma.php", {
				"oper": "set_univis_id",
				"ma_file_name": "",
				"new_univis_id": newUnivISId
			}, function(rdata) {
				alert(rdata);
				location.reload();
			});
		}
	});
});
// -->
</script>
</head>

<body id="bd_MA">
<?php require('common_nav_menu.php'); ?>

    <div id="wrapper" class="container-fluid" style="padding-top: 30px">
	<div id="confList" class="span3">

        <fieldset>
			<label>UnivIS-ID</label>
			<input type="text" id="txtUnivISId" class="textBox" size="16" />
			<input type="button" id="btnSetUnivISId" class="button" value="setzen" />
		</fieldset>

        <fieldset>
            <label class="help-block">Die vorhandenen Zusatzinformationen<br />zu Personen:</label>
			<input type="button" id="btnAddNew" class="button" value="Neue(n) Mitarbeiter(in) anlegen" />
			<ul></ul>
		</fieldset>
	</div>

	<div id="contentPanel2" class="span9">
        <form id="frmEdit" style="position:relative;" class="form-inline">
			<fieldset id="fld_feedimport">
				<legend>Mitarbeiter bearbeiten</legend>
                <p>
                    <div class="controls-row">
                        <div class="control-group span3">
                            <label class="control-label" for="txtFirstName">Vorname:</label>
                            <div class="controls">
                                <input type="text" id="txtFirstName" name="txtFirstName" size="32" class="textBox" />
                            </div>
                        </div>
                        <div class="control-group span3">
                            <label class="control-label" for="txtLastName">Nachname:</label>
                            <div class="controls">
                                <input type="text" id="txtLastName" name="txtLastName" size="32" class="textBox" />
                            </div>
                        </div>
                    </div>
                    <div class="control-group inline">
                        <label class="control-label span" for="filAttachment">Foto:</label>
                        <div class="controls span ">
                            <input type="file" id="filAttachment" name="filAttachment" class="textBox" />
                            <img id="ajaxWaiting" src="ajax-loader.gif" border="0" style="width:16px;height:16px;border:0;display:none;" />
                        </div>
                        <div class="controls span">
                            <input type="button" id="btnUpload" name="btnUpload" class="button btn" value="hochladen" />
                        </div>
                    </div>

                </p>
				<p>
                    <a href="file_editor.php" target="_blank" class="row">Dateien in FileEditor hochladen</a>
				</p>
				<textarea id="txtContent" name="txtContent" cols="120" rows="15" class="textBox"></textarea>
				<img id="userPhoto" border="0" style="position:absolute;top:11px;right:2px;display:none;" />
				<hr size="1" noshade="noshade" />
				<input type="button" id="btnUpdate" name="btnUpdate" value="Update" class="button" />
			</fieldset>
		</form>
	</div>

</div>
<?php require('common_footer.php'); ?>
</body>

</html>