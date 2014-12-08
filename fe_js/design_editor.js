var loadFileListDone = false;

function loadContentCallback(data) {
        if(!data){alert('Keine Designs gefunden!');return;}
	var curr = data.current_design;
	var optHtml = "";
	for(var i = 0; i < data.designs.length; i++) {
		if(data.designs[i].value == curr) {
			optHtml += "<option value=\"" + data.designs[i].value + "\" selected=\"selected\">" + data.designs[i].text + "</option>";
			loadChkboxes(data.designs[i].value);
		} else {
			optHtml += "<option value=\"" + data.designs[i].value + "\">" + data.designs[i].text + "</option>";
		}
	}
	$("#selDesigns").html(optHtml);
	loadFileListDone = true;
	showScreenShot($("#selDesigns").val());
	$('#confDesignLegend').html('Design '+ curr +" konfigurieren");
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
			$('#settingsBlock').html(din_chkboxes_html);
		});
}

/* ---------- Here comes jQuery: ---------- */
$(document).ready(function() {
	$.getJSON("app/edit_design.php?r=" + Math.random(), {
		"oper": "get_file_list"
	}, loadContentCallback); // load tree data

	$("#btnUpdate").click(function() {
		if(confirm("Möchten Sie das Design wirklich ändern?")) {
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

});