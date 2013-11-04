<?php
require_once('auth.php');

// help
function has_help_file() {
	global $ne_config_info;
	$help_file = $ne_config_info['help_path'] .'design_editor'. $ne_config_info['help_filesuffix'] ;
	return file_exists($help_file);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Design wechseln - <?php echo($ne_config_info['app_titleplain']); ?></title>

<?php echo NavTools::includeHtml("default"); ?>

<script type="text/javascript">
var loadFileListDone = false;

var helpText = "";

function loadContentCallback(data) {
        if(!data){alert('no designs found');return;}
	var current = data.current_design,
		current_design_img_url = "/css/" + current.replace(/head-/gi, '').replace(/.shtml/gi, '') + "/thumb.png",
		current_design_img = $('<img src="'+current_design_img_url+'">'),

		rendered_designs = new Array();

	for (var i = 0; i < data.designs.length; i++) {
		if(data.designs[i].value === current) continue;
		var img_url = "/css/" + data.designs[i].value.replace(/head-/gi, '').replace(/.shtml/gi, '') + "/thumb.png",
			img = $('<img src="'+img_url+'">'),
			caption_content = "<h4>"+data.designs[i].text+'</h4><a data-design="'+data.designs[i].value+'" href="javascript:void(0);" class="btn btn-success btn-light pull-right">Ausw&auml;hlen</a>',
			caption = $('<div class="caption clearfix">').html(caption_content),
			thumbnail = $('<div class="thumbnail">').html(new Array(img, caption)),
			html = $('<div class="span6">').html(thumbnail).css("height", "400px").css("margin-bottom", "60px");
		rendered_designs.push(html);
	};


	/*
	for(var i = 0; i < data.designs.length; i++) {
		if(data.designs[i].value == curr) {
			optHtml += "<option value=\"" + data.designs[i].value + "\" selected=\"selected\">" + data.designs[i].text + "</option>";
			loadChkboxes(data.designs[i].value);
		} else {
			optHtml += "<option value=\"" + data.designs[i].value + "\">" + data.designs[i].text + "</option>";
		}
	}
	$("#selDesigns").html(optHtml);*/


	$("#designs").html(rendered_designs);
	$("#current-design-img").html(current_design_img);

	loadChkboxes(current);

	loadFileListDone = true;
	showScreenShot($("#selDesigns").val());
	$('#confDesignLegend').html('Design '+ current +" konfigurieren");
}

function saveContentCallback(data) {
	alert(data);
}

function showScreenShot(fileName) {
	$.post("app/edit_design.php", {
		"oper": "get_screenshot",
		"head_file_name": fileName
	}, function(rdata) {
		$("#previewImage").html(rdata);
	});
}

function decode_utf8(s){
	return decodeURIComponent(escape(s));
}
//get design settings
function loadChkboxes(design){
		$.getJSON("app/edit_design.php", {
		"oper": "get_settings",
		"head_file_name": design
		}, function(rdata) {
			var din_chkboxes_html = "";
                        if(!rdata){
                            din_chkboxes_html = "no setting found";
                        }else{
                            for(var i = 0; i < rdata.length; i++){
					// <label class="checkbox">
					//   <input type="checkbox" value="">
					//   Option one is this and that—be sure to include why it's great
					// </label>
					din_chkboxes_html += '<label class="checkbox">';
					din_chkboxes_html +=	'<input type="checkbox" id="' + rdata[i].setting + '" value="' + rdata[i].setting +'" '+ (rdata[i].checked ? 'checked="checked"': '') + ' />';
					din_chkboxes_html += 	decode_utf8(rdata[i].setting_descr);
					din_chkboxes_html += '</label>';
                    // din_chkboxes_html += "<input type='checkbox' id='"+ rdata[i].setting +"' value='" + rdata[i].setting +"' "+ (rdata[i].checked ? "checked='checked'" : "") +" /><label for='"+ rdata[i].setting +"'>  "+ decode_utf8(rdata[i].setting_descr) +"</label><br>";
                            }
                        }
			$('#design-settings').html(din_chkboxes_html);
		});
}

/* ---------- Here comes jQuery: ---------- */
$(document).ready(function() {
	$.getJSON("app/edit_design.php?r=" + Math.random(), {
		"oper": "get_file_list"
	}, loadContentCallback); // load tree data

	$("#btnUpdate").click(function() {
		if(confirm("Are you sure to change the Design?")) {
			$.post("app/edit_design.php", {
				"oper": "set_head_file",
				"new_head_file": $("#selDesigns").val()
			}, saveContentCallback);
		}
	});

	$("#selDesigns").change(function() {
		if(!loadFileListDone) return;
		this_selected = $(this).val();
		showScreenShot(this_selected);
		$('#confDesignLegend').html('Design '+ this_selected +" konfigurieren");
		loadChkboxes($("#selDesigns").val());
	});



	// set design settings
	$("#btnUpdKopf").click(function() {
		var checks = $("#frmKopf [type='checkbox']");
		var settings = {};
		if(checks.length > 0) {
			for(var i = 0; i < checks.length; i++) {
				if($(checks[i]).attr('checked')){
					settings[$(checks[i]).val()] = true;
				}else{
					settings[$(checks[i]).val()] = false;
				}
			}
			console.log(settings);
                        $.post("app/edit_design.php", {
                                "oper": "set_settings",
                                "head_file_name": $("#selDesigns").val(),
                                "settings": JSON.stringify(settings)
                        },function(rdata) {
                                alert(rdata);
                        });
                }
	});

	// help
	$(".help-container .fetch").click(function() {
		var $this = $(this),
			content = $this.siblings(".hover-popover").find("content").html(),
			showContent = function(content) {
				$this.siblings(".hover-popover").show().find(".content").html(content);
			};

		console.log(content);

		if(content === undefined || content == "") {
			$.get("app/get_help.php?r=" + Math.random(), {
				"page_name": "design_editor"
			}, showContent);
		} else {
			showContent(content);
		}
	});

	$(".hover-popover .dismiss").click(function() {
		$(this).closest(".hover-popover").hide();
    });
});
</script>
</head>

<body id="bd_Design">

	<?php require('common_nav_menu.php'); ?>

	<div class="container page" id="contentPanel1">

        <div class="page-header">
            <h2 class="page-header">Design</h2>
            <div class="pull-right">

				 <?php
	            	// help
	            	if (has_help_file()) {
	            ?>
	            	<div class="help-container">
						<a class="fetch btn btn btn-primary btn-light" href="javascript:void(0);"><i class="icon-white">?</i> Hilfe</a>
						<div class="hover-popover">
							<div class="header clearfix">
								<h4>Hilfe</h4>
								<div class="pull-right">
									<a class="dismiss btn btn-black-white" href="javascript:void(0);">Ok</a>

								</div>
							</div>

							<div class="content"></div>
						</div>
					</div>
				<?php
	            	}
	            ?>
	        </div>
        </div>

        <div class="row padding-top" id="current-design">
        	<div class="span12">

	        	<div class="row">
	        		<div class="span7">
						<h4 class="page-header">Aktuelles Design</h4>
	        			<div id="current-design-img"> </div>
	        		</div>

	        		<div class="span5">
	        			<h4 class="page-header">Design konfigurieren</h4>
	        			<div id="design-settings"> </div>
	        		</div>
	        	</div>
        	</div>
        </div>

        <div class="row padding-top">
        	<div class="span12">
	        	<h4 class="page-header">Alternative Designs</h4>

        		<div id="designs" class="row"> </div>
        	</div>
        </div>


	</div>

<?php require('common_footer.php'); ?>

</body>

</html>
